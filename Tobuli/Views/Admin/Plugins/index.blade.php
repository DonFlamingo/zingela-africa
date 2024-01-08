@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="panel-title">{{ trans('admin.plugins') }}</div>
        </div>

        <div class="panel-body" data-table>
            {!! Form::open(array('route' => 'admin.plugins.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'plugin-form')) !!}
            <table class="table table-list">
                <thead>
                    <tr>
                        <th class="table-checkbox"></th>
                        {!! tableHeader('validation.attributes.name') !!}
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($plugins as $plugin)
                    <tr>
                        <td>
                            <div class="checkbox">
                                {!! Form::checkbox('plugins['.$plugin->key.'][status]', 1, $plugin->status) !!}
                                {!! Form::label(null) !!}
                            </div>
                        </td>
                        <td>{{ $plugin->name }}</td>
                        <td>
                            @if(View::exists('Admin.Plugins.Partials.'.$plugin->key))
                                @include('Admin.Plugins.Partials.'.$plugin->key)
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-action" onClick="$('#plugin-form').submit();">{{ trans('global.save') }}</button>
        </div>

    </div>
@stop