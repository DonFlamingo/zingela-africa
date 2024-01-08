<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Repositories\EmailTemplate\EmailTemplateRepositoryInterface as EmailTemplate;
use Tobuli\Validation\EmailTemplateFormValidator;

class EmailTemplatesController extends BaseController {
    /**
     * @var EmailTemplate
     */
    private $emailTemplate;
    private $section = 'email_templates';
    /**
     * @var EmailTemplateFormValidator
     */
    private $emailTemplateFormValidator;

    function __construct(EmailTemplate $emailTemplate, EmailTemplateFormValidator $emailTemplateFormValidator)
    {
        parent::__construct();
        $this->emailTemplate = $emailTemplate;
        $this->emailTemplateFormValidator = $emailTemplateFormValidator;
    }

    public function index() {
        $input = Input::all();

        $items = $this->emailTemplate->searchAndPaginate($input, 'title');
        $section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items', 'input', 'section', 'pagination', 'page', 'total_pages', 'url_path'));
    }

    public function edit($id = NULL) {
        $item = $this->emailTemplate->find($id);
        if (empty($item))
            return modalError(dontExist('global.email_template'));

        return View::make('admin::'.ucfirst($this->section).'.edit')->with(compact('item'));
    }

    public function update() {
        $input = Input::all();
        $id = $input['id'];

        try
        {
            $this->emailTemplateFormValidator->validate('update', $input, $id);

            $this->emailTemplate->update($id, $input);
            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::json(['errors' => $e->getErrors()]);
        }
    }
}
