<section class="popup">
    <h2 class="popup-heading">{{ trans('front.to_use_features') }}</h2>
    <table>
        <tr>
            <td class="transparent-background"></td>
            <td class="transparent-background"></td>
            <td class="popular green-arrow">{{ trans('front.most_popular') }}</td>
            <td class="favorite blue-arrow">{{ trans('front.best_value') }}</td>
            <td class="transparent-background"></td>
        </tr>
        <tr>
            <td class="transparent-background no-border-top"></td>
            <td class="border dark-background no-border-top"><h2>{{ trans('front.plan_1') }}</h2>{{ trans('front.free') }}<span style="height: 14px;"></span></td>
            <td class="border dark-background no-border-top"><h2>{{ trans('front.plan_2') }}</h2>$2 &#47; {{ trans('front.month') }} <span>({{ trans('front.paid_yearly') }})</span></td>
            <td class="border dark-background no-border-top"><h2>{{ trans('front.plan_3') }}</h2>$12 &#47; {{ trans('front.month') }} <span>({{ trans('front.paid_yearly') }})</span></td>
            <td class="border dark-background no-border-top"><h2>{{ trans('front.plan_4') }}</h2>{{ trans('front.from') }} $95 &#47; {{ trans('front.month') }} <span>({{ trans('front.paid_monthly') }})</span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-top no-border-right no-border-bottom">
                {{ trans('front.real_time_tracking') }}
                <span class="tooltip1"></span>
            </td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.mobile') }}
                <span class="tooltip7"></span>
            </td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.notifications') }}
                <span class="tooltip2"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.history_and_reports') }}
                <span class="tooltip3"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.fuel_savings') }}
                <span class="tooltip4"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.geofencing') }}
                <span class="tooltip5"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.poi_tools') }}
                <span class="tooltip6"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.optional_accessories') }}
                <span class="tooltip8"></span>
            </td>
            <td class="border"><span class="unchecked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
            <td class="border"><span class="checked"></span></td>
        </tr>
        <tr>
            <td class="border feature shadow no-border-right no-border-bottom">
                {{ trans('front.tracking_objects') }}
                <span class="tooltip9"></span>
            </td>
            <td class="border device-number">1</td>
            <td class="border device-number">1-5</td>
            <td class="border device-number">1-25</td>
            <td class="border device-number">{{ trans('front.unlimited') }}</td>
        </tr>
        <tr>
            <td class="transparent-background"></td>
            <td class="border">
                @if (Auth::User()->devices_limit < 5 && Auth::User()->id)
                    <span>{{ trans('front.current_plan') }}</span>
                @else
                    <a href="{{ Config::get('tobuli.frontend_shop') }}/gps-tracking-and-fleet-management-system-1{{ $email }}#5" title="{{ trans('front.upgrade') }}" class="btn btn-action">{{ trans('front.upgrade') }}</a>
                @endif
            </td>
            <td class="border">
                @if (Auth::User()->devices_limit == 5)
                    <span>{{ trans('front.current_plan') }}</span>
                @else
                    <a href="{{ Config::get('tobuli.frontend_shop') }}/gps-tracking-and-fleet-management-system-1{{ $email }}#6" title="{{ trans('front.upgrade') }}" class="btn btn-action">{{ trans('front.upgrade') }}</a>
                @endif
            </td>
            <td class="border">
                @if (Auth::User()->devices_limit == 25)
                    <span>{{ trans('front.current_plan') }}</span>
                @else
                    <a href="{{ Config::get('tobuli.frontend_shop') }}/gps-tracking-and-fleet-management-system-1{{ $email }}#7" title="{{ trans('front.upgrade') }}" class="btn btn-action">{{ trans('front.upgrade') }}</a>
                @endif
            </td>
            <td class="border">
                @if (Auth::User()->devices_limit > 25)
                    <span>{{ trans('front.current_plan') }}</span>
                @else
                    <a href="{{ Config::get('tobuli.frontend_shop') }}/hosted-software-br-running-on-atrams-servers-22{{ $email }}" title="{{ trans('front.learn_more') }}" class="button button-color-darkgray">{{ trans('front.learn_more') }}</a>
                @endif
            </td>
        </tr>
    </table>
</section>
<!-- end .popup -->