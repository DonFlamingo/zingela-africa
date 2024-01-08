<?php namespace ModalHelpers;

use Facades\Repositories\UserGprsTemplateRepo;
use Facades\Validators\UserGprsTemplateFormValidator;
use Tobuli\Exceptions\ValidationException;

class UserGprsTemplateModalHelper extends ModalHelper
{
    public function get()
    {
        $user_gprs_templates = UserGprsTemplateRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $user_gprs_templates->setPath(route('user_gprs_templates.index'));

        if ($this->api) {
            $user_gprs_templates = $user_gprs_templates->toArray();
            $user_gprs_templates['url'] = route('api.get_user_gprs_templates');
        }

        return compact('user_gprs_templates');
    }

    public function create()
    {
        try
        {
            UserGprsTemplateFormValidator::validate('create', $this->data);

            $item = UserGprsTemplateRepo::create([
                'user_id' => $this->user->id,
                'title' => $this->data['title'],
                'message' => $this->data['message']
            ]);

            return ['status' => 1, 'item' => $item];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function editData()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : request()->route('user_gprs_templates');
        
        $item = UserGprsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.gprs_template')]] : modal(dontExist('validation.attributes.gprs_template'), 'danger');

        return compact('item');
    }

    public function edit()
    {
        $item = UserGprsTemplateRepo::find($this->data['id']);

        try
        {
            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['id' => dontExist('validation.attributes.gprs_template')]);

            UserGprsTemplateFormValidator::validate('update', $this->data);

            UserGprsTemplateRepo::update($item->id, [
                'title' => $this->data['title'],
                'message' => $this->data['message']
            ]);

            return ['status' => 1];
        }
        catch (ValidationException $e)
        {
            return ['status' => 0, 'errors' => $e->getErrors()];
        }
    }

    public function getMessage()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : $this->data['id'];
        
        $item = UserGprsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.message')]];;

        return ['status' => 1, 'message' => $item->message];
    }

    public function doDestroy($id)
    {
        $item = UserGprsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('validation.attributes.gprs_template'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('user_gprs_template_id', $this->data) ? $this->data['user_gprs_template_id'] : $this->data['id'];
        
        $item = UserGprsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.gprs_templates')]];

        UserGprsTemplateRepo::delete($id);
        
        return ['status' => 1];
    }
}