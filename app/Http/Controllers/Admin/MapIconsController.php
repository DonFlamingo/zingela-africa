<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Repositories\MapIcon\MapIconRepositoryInterface as MapIcon;
use Tobuli\Repositories\UserMapIcon\UserMapIconRepositoryInterface as UserMapIcon;
use Tobuli\Validation\MapIconUploadValidator;
use Tobuli\Exceptions\ValidationException;

class MapIconsController extends BaseController{
    private $section = 'map_icons';
    /**
     * @var
     */
    private $mapIcon;
    /**
     * @var UserMapIcon
     */
    private $userMapIcon;
    /**
     * @var MapIconUploadValidator
     */
    private $mapIconUploadValidator;

    function __construct(MapIcon $mapIcon, UserMapIcon $userMapIcon, MapIconUploadValidator $mapIconUploadValidator)
    {
        parent::__construct();
        $this->mapIcon = $mapIcon;
        $this->userMapIcon = $userMapIcon;
        $this->mapIconUploadValidator = $mapIconUploadValidator;
    }

    public function index() {
        $input = Input::all();

        $items = $this->mapIcon->searchAndPaginate($input, 'path', 'desc', 40);
        $section = $this->section;
        $page = $items->currentPage();
        $total_pages = $items->lastPage();
        $pagination = smartPaginate($items->currentPage(), $total_pages);
        $url_path = $items->resolveCurrentPath();

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with(compact('items', 'input', 'section', 'pagination', 'page', 'total_pages', 'url_path'));
    }

    public function store() {
        $file = Input::file('file');
        try
        {
            $this->mapIconUploadValidator->validate('create', ['file' => $file]);
            $file = Input::file('file');
            list($w, $h) = getimagesize($file);
            $destinationPath = 'images/map_icons';
            $filename = uniqid('', TRUE).'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $this->mapIcon->create(['path' => $destinationPath.'/'.$filename, 'width' => $w, 'height' => $h]);


            /*$base_public_path = base_path('../../').'/public/frontend/images/map_icons';
            File::cleanDirectory($base_public_path);
            File::copyDirectory(base_path('public').'/'.$destinationPath, $base_public_path);*/
            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::make($e->getErrors()->first(), '406');
        }
    }

    public function destroy() {
        $ids = Input::get('id');
        if (is_array($ids) && $nr = count($ids)) {
            $all = $this->mapIcon->count();
            if ($nr >= $all) {
                return Response::json(['status' => 0, 'error' => trans('admin.cant_delete_all')]);
            }
            $icon = $this->mapIcon->whereNotInFirst($ids);

            $this->userMapIcon->updateWhereIconIds($ids, ['map_icon_id' => $icon->id]);
            foreach($ids as $id) {
                $del_icon = $this->mapIcon->find($id);
                if ($del_icon) {
                    $filename = public_path().'/'.$del_icon->path;
                    if (File::exists($filename)) {
                        File::delete($filename);
                    }
                    $this->mapIcon->delete($id);
                }
            }

            /*File::cleanDirectory(base_path('../../').'/public/frontend/images/map_icons');
            File::copyDirectory('frontend/images/map_icons', base_path('../../').'/public/frontend/images/map_icons');*/
        }

        return Response::json(['status' => 1]);
    }
}
