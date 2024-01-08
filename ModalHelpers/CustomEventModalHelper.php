<?php namespace ModalHelpers;

use Facades\Repositories\EventCustomRepo;
use Facades\Repositories\TrackerPortRepo;
use Facades\Validators\EventCustomFormValidator;
use Illuminate\Support\Facades\DB;
use Tobuli\Exceptions\ValidationException;

class CustomEventModalHelper extends ModalHelper
{
    public function get()
    {
        $events = EventCustomRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $events->setPath(route('custom_events.index'));

        if ($this->api) {
            $events = $events->toArray();
            $events['url'] = route('api.get_custom_events');
        }

        return compact('events');
    }

    public function createData()
    {
        $protocols = TrackerPortRepo::all()->lists('name', 'name')->all();
        $protocols = array_merge(config('tobuli.additional_protocols'), $protocols);
        ksort($protocols);
        $types = ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')];
        if ($this->api) {
            $protocols_arr = [];
            foreach ($protocols as $key => $value)
                array_push($protocols_arr, ['id' => $key, 'value' => $value]);
            $protocols = $protocols_arr;

            $types = [['id' => '1', 'value' => trans('front.event_type_1')], ['id' => '2', 'value' => trans('front.event_type_2')], ['id' => '3', 'value' => trans('front.event_type_3')]];
        }

        return compact('protocols', 'types');
    }

    public function create()
    {
        try
        {
            EventCustomFormValidator::validate('create', $this->data);

            $insert = FALSE;
            if (!$this->api) {
                foreach($this->data['tag'] as $key => $tag) {
                    $tag = strtolower($tag);
                    $type = $this->data['type'][$key];
                    $tag_value = $this->data['tag_value'][$key];
                    if ($tag == '' && $tag_value == '')
                        continue;

                    if ($tag == '' || $tag_value == '')
                        throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

                    $insert = TRUE;
                    $this->data['conditions'][] = [
                        'tag' => $tag,
                        'type' => $type,
                        'tag_value' => $tag_value
                    ];
                }
            }
            else {
                $this->data['tags'] = json_decode($this->data['conditions'], true);
                $this->data['conditions'] = [];
                foreach($this->data['tags'] as $key => $val) {
                    $tag = strtolower($val['tag']);
                    $type = $val['type'];
                    $tag_value = $val['tag_value'];
                    if ($tag == '' && $tag_value == '')
                        continue;

                    if ($tag == '' || $tag_value == '')
                        throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

                    $insert = TRUE;
                    $this->data['conditions'][] = [
                        'tag' => $tag,
                        'type' => $type,
                        'tag_value' => $tag_value
                    ];
                }
            }

            if (!$insert)
                throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

			if ($this->api) {
				$item = EventCustomRepo::create($this->data + ['user_id' => $this->user->id, 'always' => isset($this->data['alawys']) ? $this->data['alawys'] : 0 ]);
			}else{
				$item = EventCustomRepo::create($this->data + ['user_id' => $this->user->id, 'always' => isset($this->data['alawys'])]);
			}
			
            $tags_arr = [];
            foreach ($this->data['conditions'] as $condition) {
                $tags_arr[$condition['tag']] = [
                    'event_custom_id' => $item->id,
                    'tag' => $condition['tag']
                ];
            }
            DB::table('event_custom_tags')->insert($tags_arr);

            return ['status' => 1, 'item' => $item];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData()
    {
        $id = array_key_exists('custom_event_id', $this->data) ? $this->data['custom_event_id'] : request()->route('custom_events');
        $item = EventCustomRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('front.event')]] : modal(dontExist('front.event'), 'danger');

        $protocols = TrackerPortRepo::all()->lists('name', 'name')->all();
        $protocols = array_merge(config('tobuli.additional_protocols'), $protocols);
        ksort($protocols);
        $types = ['1' => trans('front.event_type_1'), '2' => trans('front.event_type_2'), '3' => trans('front.event_type_3')];
        if ($this->api) {
            $protocols_arr = [];
            foreach ($protocols as $key => $value)
                array_push($protocols_arr, ['id' => $key, 'value' => $value]);
            $protocols = $protocols_arr;

            $types = [['id' => '1', 'value' => trans('front.event_type_1')], ['id' => '2', 'value' => trans('front.event_type_2')], ['id' => '3', 'value' => trans('front.event_type_3')]];
        }

        return compact('item', 'protocols', 'types');
    }

    public function edit()
    {
        $item = EventCustomRepo::find($this->data['id']);

        try
        {
            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['id' => dontExist('front.event')]);

            EventCustomFormValidator::validate('update', $this->data);

            $insert = FALSE;
            $tags_arr = [];
            foreach($this->data['tag'] as $key => $tag) {
                $tag = strtolower($tag);
                $type = $this->data['type'][$key];
                $tag_value = $this->data['tag_value'][$key];
                if ($tag == '' && $tag_value == '')
                    continue;

                if ($tag == '' || $tag_value == '')
                    throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

                $insert = TRUE;

                $tags_arr[$tag] = [
                    'event_custom_id' => $item->id,
                    'tag' => $tag
                ];
                $this->data['conditions'][] = [
                    'tag' => $tag,
                    'type' => $type,
                    'tag_value' => $tag_value
                ];
            }

            if (!$insert)
                throw new ValidationException(['conditions' => trans('front.fill_all_fields')]);

            EventCustomRepo::update($item->id, $this->data + ['always' => isset($this->data['alawys'])]);
            $item->tags()->delete();
            DB::table('event_custom_tags')->insert($tags_arr);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function doDestroy($id)
    {
        $item = EventCustomRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('front.event'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('custom_event_id', $this->data) ? $this->data['custom_event_id'] : $this->data['id'];
        $item = EventCustomRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('front.event')]];

        EventCustomRepo::delete($id);
        
        return ['status' => 1];
    }
}