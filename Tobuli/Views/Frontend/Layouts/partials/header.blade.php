<div id="header" class="folded">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                @if ( has_asset_logo('logo') )
                <a class="navbar-brand" href="/" title="{{ settings('main_settings.server_name') }}"><img src="{{ asset_logo('logo') }}"></a>
                @endif
            </div>

            <ul class="nav navbar-nav navbar-right">
                @if (isAdmin())
                    <li>
                        <a href="{!!route('admin')!!}" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('global.admin')!!}">
                            <span class="icon admin"></span>
                            <span class="text">{!!trans('global.admin')!!}</span>
                        </a>
                    </li>
                @endif

                <li class="dropdown">
                    <a href="javascript:" class="dropdown-toggle" role="button" data-toggle="dropdown" id="dropTools" rel="tooltip" data-placement="bottom" title="{!!trans('front.tools')!!}">
                        <span class="icon tools"></span>
                        <span class="text">{!!trans('front.tools')!!}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu" aria-labelledby="dropTools">
                        <li>
                            <a href="javascript:" onclick="app.openTab('alerts_tab');">
                                <span class="icon alerts"></span>
                                <span class="text">{!!trans('front.alerts')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="app.openTab('geofencing_tab');">
                                <span class="icon geofences"></span>
                                <span class="text">{!!trans('front.geofencing')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="app.openTab('routes_tab');">
                                <span class="icon routes"></span>
                                <span class="text">{!!trans('front.routes')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-url="{!!route('reports.create')!!}" data-modal="reports_create" role="button">
                                <span class="icon reports"></span>
                                <span class="text">{!!trans('front.reports')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-url="{!!route('expenses.create')!!}" data-modal="expenses_create" role="button">
                                <span class="fa fa-money"></span>&nbsp;
                                <span class="text">{!!trans('front.expenses')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a  href="#objects_tab" data-toggle="tab" onclick="app.ruler();">
                                <span class="icon ruler"></span>
                                <span class="text">{!!trans('front.ruler')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" onClick="app.openTab('map_icons_tab');">
                                <span class="icon poi"></span>
                                <span class="text">{!!trans('front.poi')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-toggle="modal" data-target="#showPoint">
                                <span class="icon point"></span>
                                <span class="text">{!!trans('front.show_point')!!}</span>

                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-toggle="modal" data-target="#showAddress">
                                <span class="icon address"></span>
                                <span class="text">{!! trans('front.show_address') !!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" data-toggle="modal" data-target="#findAssets">
                                <span class="icon search"></span>
                                <span class="text">Find Assets</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-url="{{ route('send_command.create') }}" data-modal="send_command">
                                <span class="icon send-command"></span>
                                <span class="text">{!!trans('front.send_command')!!}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:" data-url="{!!route('my_account_settings.edit')!!}" data-modal="my_account_settings_edit" role="button" rel="tooltip" data-placement="bottom" title="{!!trans('front.setup')!!}">
                        <span class="icon setup"></span>
                        <span class="text">{!!trans('front.setup')!!}</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:" class="dropdown-toggle" role="button" id="dropMyAccount" data-toggle="dropdown" rel="tooltip" data-placement="bottom" title="{!!trans('front.my_account')!!}">
                        <span class="icon account"></span>
                        <span class="text">{!!trans('front.my_account')!!}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropMyAccount">
                        <li>
                            <a href="javascript:" data-url="{{ route('subscriptions.languages') }}" data-modal="language-selection">
                                <span class="icon"><img src="{!!asset('assets/img/flag/'.(Session::has('language') ? Session::get('language') : Auth::user()->lang).'.png')!!}" alt="Language" class="img-thumbnail"></span>
                                <span class="text">{!!trans('Change Language')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-url="{{ route('subscriptions.index') }}" data-modal="subscriptions_edit">
                                <span class="icon membership"></span>
                                <span class="text">{!!trans('front.subscriptions')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:" data-url="{{ route('my_account.edit') }}" data-modal="subscriptions_edit">
                                <span class="icon password"></span>
                                <span class="text">{!!trans('front.change_password')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{!!route('logout')!!}">
                                <span class="icon logout"></span>
                                <span class="text">{!!trans('global.log_out')!!}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
