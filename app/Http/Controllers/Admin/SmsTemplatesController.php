<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Repositories\SmsTemplate\SmsTemplateRepositoryInterface as SmsTemplate;
use Tobuli\Validation\SmsTemplateFormValidator;

class SmsTemplatesController extends BaseController {
    /**
     * @var SmsTemplate
     */
    private $smsTemplate;
    private $section = 'sms_templates';
    /**
     * @var SmsTemplateFormValidator
     */
    private $smsTemplateFormValidator;

    function __construct(SmsTemplate $smsTemplate, SmsTemplateFormValidator $smsTemplateFormValidator)
    {
        parent::__construct();
        $this->smsTemplate = $smsTemplate;
        $this->smsTemplateFormValidator = $smsTemplateFormValidator;
    }

    public function index() {
        $input = Input::all();

        $items = $this->smsTemplate->searchAndPaginate($input, 'title');
        $section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items', 'input', 'section', 'pagination', 'page', 'total_pages', 'url_path'));
    }

    public function edit($id = NULL) {
        $item = $this->smsTemplate->find($id);
        if (empty($item))
            return modalError(dontExist('front.sms_template'));

        return View::make('admin::'.ucfirst($this->section).'.edit')->with(compact('item'));
    }

    public function update() {
        $input = Input::all();
        $id = $input['id'];

        try
        {
            $this->smsTemplateFormValidator->validate('update', $input, $id);

            $this->smsTemplate->update($id, $input);
            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }
}
