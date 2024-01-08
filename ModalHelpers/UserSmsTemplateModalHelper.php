<?php namespace ModalHelpers;

use Facades\Repositories\UserSmsTemplateRepo;
use Facades\Validators\UserSmsTemplateFormValidator;
use Tobuli\Exceptions\ValidationException;

class UserSmsTemplateModalHelper extends ModalHelper
{
    public function get()
    {
        $user_sms_templates = UserSmsTemplateRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $user_sms_templates->setPath(route('user_sms_templates.index'));

        if ($this->api) {
            $user_sms_templates = $user_sms_templates->toArray();
            $user_sms_templates['url'] = route('api.get_user_sms_templates');
        }

        return compact('user_sms_templates');
    }

    public function create()
    {
        try
        {
            UserSmsTemplateFormValidator::validate('create', $this->data);

            $item = UserSmsTemplateRepo::create([
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
        $id = array_key_exists('user_sms_template_id', $this->data) ? $this->data['user_sms_template_id'] : request()->route('user_sms_templates');
        
        $item = UserSmsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return $this->api ? ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.sms_template')]] : modal(dontExist('validation.attributes.sms_template'), 'danger');

        return compact('item');
    }

    public function edit()
    {
        $item = UserSmsTemplateRepo::find($this->data['id']);

        try
        {
            if (empty($item) || $item->user_id != $this->user->id)
                throw new ValidationException(['id' => dontExist('validation.attributes.sms_template')]);

            UserSmsTemplateFormValidator::validate('update', $this->data);

            UserSmsTemplateRepo::update($item->id, [
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
        $id = array_key_exists('user_sms_template_id', $this->data) ? $this->data['user_sms_template_id'] : $this->data['id'];
        
        $item = UserSmsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.message')]];;

        return ['status' => 1, 'message' => $item->message];
    }

    public function doDestroy($id)
    {
        $item = UserSmsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return modal(dontExist('validation.attributes.sms_template'), 'danger');

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('user_sms_template_id', $this->data) ? $this->data['user_sms_template_id'] : $this->data['id'];
        
        $item = UserSmsTemplateRepo::find($id);
        if (empty($item) || $item->user_id != $this->user->id)
            return ['status' => 0, 'errors' => ['id' => dontExist('validation.attributes.sms_templates')]];

        UserSmsTemplateRepo::delete($id);
        
        return ['status' => 1];
    }
}