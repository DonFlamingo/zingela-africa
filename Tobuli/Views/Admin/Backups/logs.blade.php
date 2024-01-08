@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('front.latest_uploads') }}
@stop

@section('body')
    <h5>{{ trans('front.next_backup') }} {{ isset($settings['next_backup']) ? date('Y-m-d H:i', $settings['next_backup']) : '-' }}</h5>

    <table class="table table-list">
        <thead>
            <tr>
                <th>{{ trans('front.server_time') }}</th>
                <th>{{ trans('validation.attributes.ftp_path') }}</th>
                <th>{{ trans('validation.attributes.message') }}</th>
            </tr>
        </thead>
        <tbody>
        @if (isset($settings['messages']) && count($settings['messages']))
            @foreach($settings['messages'] as $message)
                <tr class="{{ $message['status'] ? 'success' : 'danger' }}">
                    <td>{{ $message['date'] }}</td>
                    <td>{{ $message['path'] }}</td>
                    <td>{{ $message['message'] }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop