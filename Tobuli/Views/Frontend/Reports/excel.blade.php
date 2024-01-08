<html>
    <table>
        <thead>
            <tr>
                <th>{!!trans('global.device')!!}</th>
                <th>{!!trans('global.date')!!}</th>
                <th>{!!trans('global.km')!!}</th>
                <th>{!!trans('front.f_cons_km')!!}</th>
                <th>{!!trans('front.f_cons_mpg')!!}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $itemss)
                @foreach ($itemss as $item)
                    <?php
                    $distance = number_format($item->distance, 2, '.', '');
                    $fuel_con = number_format(($distance * $item->fuel_per_km), 4, '.', '');
                    ?>
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{!!datetime($item->date)!!}</td>
                        <td>{!!$distance!!}</td>
                        <td>{!!$item->fuel_measurement_id== 1 ? $fuel_con : 0!!}</td>
                        <td>{!!$item->fuel_measurement_id == 2 ? number_format(litersToGallons($fuel_con), 4, '.', '') : 0!!}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</html>