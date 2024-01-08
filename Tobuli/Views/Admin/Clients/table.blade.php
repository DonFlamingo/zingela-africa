<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'active', NULL) !!}
            {!! tableHeaderSort($items->sorting, 'first_name') !!}
            {!! tableHeaderSort($items->sorting, 'last_name') !!}
            {!! tableHeaderSort($items->sorting, 'email') !!}
            @if (Auth::User()->group_id == 1)
                {!! tableHeaderSort($items->sorting, 'group_id') !!}
                {!! tableHeaderSort($items->sorting, 'manager_email', trans('validation.attributes.manager_id')) !!}
            @endif
            {!! tableHeader('front.devices') !!}
            @if (Auth::User()->group_id == 1)
                {!! tableHeader('admin.subusers') !!}
            @endif
            {!! tableHeaderSort($items->sorting, 'devices_limit') !!}
            {!! tableHeaderSort($items->sorting, 'subscription_expiration', trans('validation.attributes.expiration_date')) !!}
            {!! tableHeaderSort($items->sorting, 'loged_at') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        <span class="label label-sm label-{!! $item->active ? 'success' : 'danger' !!}">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                    </td>
                    <td>
                        {!! $item->first_name !!}
                    </td>
                    <td>
                        {!! $item->last_name !!}
                    </td>
                    <td>
                        {!! $item->email !!}
                    </td>
                    @if (Auth::User()->group_id == 1)
                        <td>
                            {!! trans('admin.group_'.$item->group_id) !!}
                        </td>
                        <td>
                            {!! isset($item->manager_email) ? $item->manager_email : '' !!}
                        </td>
                    @endif
                    <td>
                        {{ $item->devices }}
                    </td>
                    @if (Auth::User()->group_id == 1)
                        <td>
                            {{ $item->subusers }}
                        </td>
                    @endif
                    <td>
                        {!! is_null($item->devices_limit) ? trans('front.unlimited') : $item->devices_limit !!} {{ !empty($item->billing_plan) ? "({$item->billing_plan})" : '' }}
                    </td>
                    <td>
                        {!! $item->subscription_expiration == '0000-00-00 00:00:00' ? trans('front.unlimited') : $item->subscription_expiration !!}
                    </td>
                    <td>
                        {!! datetime($item->loged_at) !!}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="{!! $section !!}_edit" data-url="{!! route("admin.{$section}.edit", $item->id) !!}">{!! trans('global.edit') !!}</a></li>
                                <li><a href="javascript:" data-modal="{!! $section !!}_login_as" data-url="{!! route("admin.{$section}.login_as", $item->id) !!}">{!! trans('front.login_as') !!}</a></li>
                            </ul>
                        </div>
                        <i class="btn icon ico-arrow-down"
                           type="button"
                           data-url="{{ route('admin.clients.get_devices', $item->id) }}"
                           data-toggle="collapse"
                           data-target="#user-devices-{{ $item->id }}">
                        </i>
                    </td>
                </tr>
                <tr class="row-table-inner" style="text-align: center;">
                    <td colspan="13" id="user-devices-{{ $item->id }}" aria-expanded="false" class="collapse"></td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="13">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")
