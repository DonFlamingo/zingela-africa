@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-md')

@section('title')
    <i class="fa fa-money"></i> {!!trans('front.expenses')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#expenses-create" class="expenses-form-create-link" role="tab" data-toggle="tab">{!!trans('front.create')!!}</a></li>
        <li><a href="#expenses-list" class="expenses-list-link" role="tab" data-toggle="tab" style="width: auto; padding-left: 10px; padding-right: 10px;">{!!trans('front.list_of_expenses')!!}</a></li>
    </ul>

    <div id="expenses-modal">
    {!!Form::open(['route' => 'expenses.store', 'method' => 'POST', 'id' => 'expenses_form'])!!}
    {!!Form::hidden('id')!!}
    {!!Form::hidden('_method', 'POST')!!}

        <div class="tab-content" id="expenses_form_inputs">

            <div id="expenses-create" class="tab-pane active">

                <div class="row form-horizontal">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('name', trans('validation.attributes.name'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                {!!Form::text('name', null, ['class' => 'form-control'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="from" class="col-sm-3 control-label">{{ trans('validation.attributes.date') }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="has-feedback">
                                        <i class="icon calendar form-control-feedback"></i>
                                        <input name="date" type="text" class="datepicker form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('quantity', trans('validation.attributes.quantity'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-6">
                                <input name="quantity" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('cost', trans('validation.attributes.cost'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-6">
                                <input name="cost" type="number" class="form-control">
                                <span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('supplier', trans('validation.attributes.supplier'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                {!!Form::text('supplier', null, ['class' => 'form-control'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('buyer', trans('validation.attributes.buyer'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                {!!Form::text('buyer', null, ['class' => 'form-control'])!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{!!trans('front.devices')!!}</label>
                            {!! Form::hidden('devices_fake') !!}
                            <div class="col-sm-9">
                                {!!Form::select('devices[]', $devices->lists('name', 'id')->all(), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon devices', 'data-live-search' => true, 'data-actions-box' => true])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('odometer', trans('validation.attributes.odometer'),  ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                {!!Form::text('odometer', null, ['class' => 'form-control'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('engine_hour', trans('validation.attributes.engine_hour'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                <input name="engine_hour" type="number" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            {!!Form::label('description', trans('validation.attributes.description'), ['class' => 'col-sm-3 control-label'])!!}
                            <div class="col-sm-9">
                                <textarea name="description" class="form-control" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="expenses-list" class="tab-pane">
                <div data-table>
                    @include('Frontend.Expenses.index')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
    <script>
        if ( typeof _static_expenses_create === "undefined") {
            var _static_expenses_create = true;

            $(document).on('click', '#expenses_create button.create:visible', function() {
                $('#reports_create').remove();
                $('#expenses_create form').attr('action', $(this).data('action'));
                $('#expenses_create input[name="_method"]').val('POST');
                $('#expenses_create button.update_hidden').trigger('click');
            });

            $(document).on('click', '#expenses_create button.new:visible', function() {
                var parent = $('#expenses_create');

                parent.find('input[name="name"]').val('');
                parent.find('input[name="date"]').val('');
                parent.find('input[name="quantity"]').val('');
                parent.find('input[name="cost"]').val('');
                parent.find('input[name="supplier"]').val('');
                parent.find('input[name="buyer"]').val('');
                parent.find('select[name=devices]').val([]);
                parent.find('input[name="odometer"]').val('');
                parent.find('input[name="engine_hours"]').val('');
                parent.find('textarea[name="description"]').val('');

                $('a.expenses-form-create-link').trigger('click');
            });

            tables.set_config('expenses-list', {
                url:'{{ route('expenses.index') }}'
            });

            function expenses_create_modal_callback(res) {
                if (res.status == 3) {
                    if (typeof res.url != 'undefined') {
                        var form = $('<form method="POST" action="' + res.url + '">');
                        $('body').append(form);
                        form.submit();
                    }
                }
                if (res.status == 2) {
                    tables.get('expenses-list');
                    $('a.expenses-list-link').trigger('click');
                }
            }

            function expenses_destroy_modal_callback() {
                tables.get('expenses-list');
            }
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="update_hidden" style="display: none;"></button>
    <button type="button" class="btn btn-action create" data-action="{!!route('expenses.store')!!}">{!!trans('front.create')!!}</button>
    <button type="button" class="btn btn-default new">{!!trans('front.new')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
