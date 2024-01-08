<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                <th>{!!trans('validation.attributes.title')!!}</th>
                <th>{!!trans('validation.attributes.type')!!}</th>
                <th>{!!trans('validation.attributes.format')!!}</th>
                <th>{!!trans('front.devices')!!}</th>
                <th>{!!trans('front.geofences')!!}</th>
                <th>{!!trans('front.schedule')!!}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($reports))
            @foreach ($reports as $report)
                <tr>
                    <td>
                        {{$report->title}}
                    </td>
                    <td>
                        {{$types[$report->type]}}
                    </td>
                    <td>
                        {{trans('front.'.$report->format)}}
                    </td>
                    <td>
                        {{count($report->devices)}}
                    </td>
                    <td>
                        {{count($report->geofences)}}
                    </td>
                    <td>
                        {!!($report->daily || $report->weekly ? trans('global.yes') : trans('global.no'))!!}
                    </td>
                    <td class="actions">
                        <a href="javascript:" class="report_item_edit"><i class="icon edit"></i></a>
                        <?php
                        $report = $report->toArray();
                        $report['geofences'] = array_map(function($value) {
                            return array_only($value, ['id']);
                        },$report['geofences']);

                        $report['devices'] = array_map(function($value) {
                            return array_only($value, ['id']);
                        },$report['devices']);
                        ?>
                        <span class="report_item_json hidden">{!!json_encode($report)!!}</span>

                        <a href="javascript:" data-url="{!!route('reports.do_destroy', $report['id'])!!}" data-modal="reports_destroy"><i class="icon delete"></i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">{!!trans('front.no_reports')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $reports->render() !!}
</div>