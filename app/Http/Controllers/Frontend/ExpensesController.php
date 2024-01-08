<?php
namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Facades\ModalHelpers\ExpensesModalHelper;

ini_set('memory_limit', '-1');
set_time_limit(0);

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ExpensesModalHelper::get();

        return !$this->api ? view('front::Expenses.index')->with($data) : ['status' => 1, 'items' => $data];

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = ExpensesModalHelper::createData();
        
        return is_array($data) && !$this->api ? view('front::Expenses.create')->with($data) : $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return ExpensesModalHelper::create();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function doDestroy($id)
    {
        $data = ExpensesModalHelper::doDestroy($id);
        return is_array($data) ? view('front::Expenses.destroy')->with($data) : $data;
    }

    public function destroy()
    {
        return ExpensesModalHelper::destroy();
    }
}
