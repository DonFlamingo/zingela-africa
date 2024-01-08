@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_translations">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon globe"></i> {!! trans('admin.translations') !!}</div>
        </div>

        <div class="panel-body" data-table>
            <table class="table table-striped">
                @foreach ($langs as $lang)
                    <tr>
                        <td><a href="{{ route('admin.translations.show', $lang) }}"><img src="{{ asset("assets/img/flag/{$lang}.png") }}" alt="{{ $lang }}"> {{ $names[$lang] }}</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop