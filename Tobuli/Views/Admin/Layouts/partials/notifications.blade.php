<div class="top-menu">
<ul class="nav navbar-nav pull-right">
    <li class="classic-menu-dropdown">
<!-- BEGIN USER LOGIN DROPDOWN -->
<li class="dropdown dropdown-user">
    <a href="javascript:" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="fa fa-user"></i>
					<span style="color: #dddddd;">
					{{ Auth::User()->email }} ({{ trans('admin.group_'.Auth::User()->group_id) }})</span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu">
        @if (Auth::User()->group_id == 1)
            <li>
                <a href="{{ route('admin.restart_traccar') }}" class="js-confirm-link" data-confirm="{{ trans('admin.do_restart_tracking_service') }}"><i class="fa fa-refresh"></i> {{ trans('admin.restart_tracking_service') }} </a>
            </li>
        @endif
        <li>
            <a href="{{ route('logout') }}">
                <i class="fa fa-key"></i> {{ trans('global.log_out') }} </a>
        </li>
    </ul>
</li>
<!-- END USER LOGIN DROPDOWN -->
</ul>
</div>