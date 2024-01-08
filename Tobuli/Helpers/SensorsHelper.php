<?php
require_once base_path('Tobuli/Helpers/eos/eos.class.php');

function getSensorValue($item_other, $sensor, $update = FALSE, $newest = TRUE, $sufix = TRUE) {
    $sensor = json_decode(json_encode($sensor), TRUE);


   // $sensor['unit_of_measurement'] = "";
    preg_match('/<'.preg_quote($sensor['tag_name'], '/').'>(.*?)<\/'.preg_quote($sensor['tag_name'], '/').'>/s', $item_other, $matches);
    if (!isset($matches['1'])) {
        if (!$update && !$newest && !$sufix)
            return null;

        if ($sensor['odometer_value_by'] != 'virtual_odometer' && (!$newest || empty($sensor['value']) || $sensor['value'] == '-'))
            return ($sufix ? '- ' : NULL).($sufix ? $sensor['unit_of_measurement'] : '');

        $matches['1'] = $sensor['value'];
    }

    $value = $matches['1'];
    $update_value = $value;
    $value_number = parseNumber($value);

    $res = setflagFormulaGet($sensor, $value);
    $sensor['formula'] = $res['formula'];
    $formula_value = $res['value'];
    $old_on_value = $sensor['on_value'];
    $old_off_value = $sensor['off_value'];
    $old_on_tag_value = $sensor['on_tag_value'];
    $old_off_tag_value = $sensor['off_tag_value'];

    if ($sensor['type'] == 'acc') {
        $res = setflagWithValueGet($value, $sensor['on_value']);
        $sensor['on_value'] = $res['ac_value'];
        $on_value = $res['value'];

        $res = setflagWithValueGet($value, $sensor['off_value']);
        $sensor['off_value'] = $res['ac_value'];
        $off_value = $res['value'];
    }

    if (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
        $res = setflagWithValueGet($value, $sensor['on_tag_value']);
        $sensor['on_tag_value'] = $res['ac_value'];
        $on_tag_value = $res['value'];

        $res = setflagWithValueGet($value, $sensor['off_tag_value']);
        $sensor['off_tag_value'] = $res['ac_value'];
        $off_tag_value = $res['value'];
    }

    if (($sensor['type'] == 'acc') && ($sensor['on_value'] != $on_value && $sensor['off_value'] != $off_value)) {
        $update = FALSE;
        $update_value = $sensor['value'];

        $res = setflagWithValueGet($sensor['value'], $old_on_value);
        $on_value = $res['value'];

        $res = setflagWithValueGet($sensor['value'], $old_off_value);
        $off_value = $res['value'];
    }

    if (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private']) && !checkCondition($sensor['on_type'], $on_tag_value, $sensor['on_tag_value']) && !checkCondition($sensor['off_type'], $off_tag_value, $sensor['off_tag_value'])) {
        $update = FALSE;
        $update_value = $sensor['value'];

        $res = setflagWithValueGet($sensor['value'], $old_on_tag_value);
        $on_tag_value = $res['value'];

        $res = setflagWithValueGet($sensor['value'], $old_off_tag_value);
        $off_tag_value = $res['value'];
    }

    if ($update && $sensor['value'] != $update_value) {
        $update_arr = [
            'value' => $update_value,
        ];
        if ($sensor['type'] == 'odometer' && $sensor['odometer_value_by'] == 'connected_odometer') {
            $update_arr['value_formula'] = solveEquation($formula_value, $sensor['formula']);
        }

        DB::table('device_sensors')
            ->where('id', $sensor['id'])
            ->update($update_arr);
    }

    $sensor_value = NULL;

    if ($sensor['type'] == 'acc') {
        if ($sensor['on_value'] == $on_value)
            $sensor_value = trans('front.on');

        if ($sensor['off_value'] == $off_value)
            $sensor_value = trans('front.off');
    }
    elseif ($sensor['type'] == 'battery') {
        if ($sensor['shown_value_by'] == 'tag_value')
            $sensor_value = $value;

        if ($sensor['shown_value_by'] == 'min_max_values' && is_numeric($sensor['max_value']) && is_numeric($sensor['min_value']) && is_numeric($value_number) && $value >= $sensor['min_value'] && $value_number <= $sensor['max_value']) {
            if ($value_number <= $sensor['min_value'])
                $sensor_value = 0;
            elseif ($value_number >= $sensor['max_value'])
                $sensor_value = 100;
            else {
                $sensor_value = getPrc($sensor['max_value'] - $sensor['min_value'], ($value_number - $sensor['min_value'])).'%';
            }
        }

        if ($sensor['shown_value_by'] == 'formula') {
            $sensor_value = solveEquation($formula_value, $sensor['formula']);
        }
    }
    elseif ($sensor['type'] == 'gsm') {
        if (is_numeric($sensor['max_value']) && is_numeric($sensor['min_value']) && is_numeric($value_number))
            if ($value_number <= $sensor['min_value'])
                $sensor_value = 0;
            elseif ($value_number >= $sensor['max_value'])
                $sensor_value = 100;
            else {
                $sensor_value = getPrc($sensor['max_value'] - $sensor['min_value'], ($value_number - $sensor['min_value']));
            }
    }
    elseif ($sensor['type'] == 'odometer') {
        if ($sensor['odometer_value_by'] == 'connected_odometer') {
            $sensor_value = solveEquation($formula_value, $sensor['formula']);
        }

        if ($sensor['odometer_value_by'] == 'virtual_odometer')
            $sensor_value = float($sensor['odometer_value']);
    }
    elseif ($sensor['type'] == 'satellites' || $sensor['type'] == 'engine_hours') {
        $sensor_value = $value;
    }
    elseif ($sensor['type'] == 'fuel_tank' && is_numeric($sensor['full_tank']) && is_numeric($sensor['full_tank_value'])) {
        if (is_numeric($value_number)) {
            $fuel_value = $value_number;
            if ($sensor['full_tank'] != $sensor['full_tank_value'])
                $fuel_value = $sensor['full_tank'] * (getPrc($sensor['full_tank_value'], $value_number) / 100);

            $sensor_value = $fuel_value;
        }
    }
    elseif ($sensor['type'] == 'fuel_tank_calibration') {
        if ( !is_array($sensor['calibrations']) && is_string($sensor['calibrations']) ) {
            $sensor['calibrations'] = unserialize( $sensor['calibrations'] );
        }

        $calibrations = array_reverse($sensor['calibrations'], TRUE);
        $first = key($calibrations);
        $first_val = current($calibrations);
        $last_val = end($calibrations);
        $last = key($calibrations);
        $order = 'asc';
        if ($first_val > $last_val && $first < $last) {
            $order = 'dec';
        }


        if (($value_number < $first && $order == 'dec') || ($value_number > $first && $order == 'asc')) {
            $sensor_value = $first_val;
        }
        else {
            $prev_item = [];
            foreach ($calibrations as $x => $y) {
                if (!empty($prev_item)) {
                    if (($value_number < $x && $order == 'dec') || ($value_number > $x && $order == 'asc')) {
                        $sensor_value = calibrate($value_number, $prev_item['x'], $prev_item['y'], $x, $y);
                        break;
                    }
                }
                $prev_item = [
                    'x' => $x,
                    'y' => $y
                ];
            }

            if (is_null($sensor_value))
                $sensor_value = $y;
        }

        $sensor_value = round($sensor_value, 2);
    }
    elseif ($sensor['type'] == 'temperature' || $sensor['type'] == 'tachometer' ) {
        $sensor_value = solveEquation($formula_value, $sensor['formula']);
    }
    elseif ( in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private']) ) {
        if (checkCondition($sensor['on_type'], $on_tag_value, $sensor['on_tag_value']))
            $sensor_value = trans('front.on');

        if (checkCondition($sensor['off_type'], $off_tag_value, $sensor['off_tag_value']))
            $sensor_value = trans('front.off');
    }

    return (empty($sensor_value) || $sensor_value == '-' ? ($sufix ? '-' : NULL) : $sensor_value).($sufix ? ' '.$sensor['unit_of_measurement'] : '');
}

function solveEquation($value, $formula) {
    $equation = str_replace('[value]', $value, $formula);
    $eos = new eqEOS();
    try {
        $result = $eos->solveIF($equation);
    }
    catch(\Exception $e) {
        $result = '-';
    }
    return $result;
}

function formatSensors($item_other, $sensors, &$values) {
    $arr = [];
    foreach ($sensors as $sensor) {

        if ($sensor->type == 'harsh_acceleration' || $sensor->type == 'harsh_breaking')
            continue;
        $value = htmlentities(getSensorValue($item_other, json_decode(json_encode($sensor), true) , TRUE));
        if(!empty($value)){
            $value = str_replace("-" , "" , $value);
            //$value = str_replace("%" , "" , $value);
        }
        $arr[] = [
            'type' => $sensor->type,
            'name' => getSensorName($sensor),
            'value' => trim($value),
            'show_in_popup' => $sensor->show_in_popup,
        ];

        if ($sensor->type == 'odometer') {
            $values['odometer']['value'] = $sensor->odometer_value_by == 'virtual_odometer' ? $sensor->odometer_value : $sensor->value_formula;
            $values['odometer']['sufix'] = $sensor->unit_of_measurement;
        }
        if ($sensor->type == 'engine_hours') {
            $values['engine_hours']['value'] = $sensor->value;
            $values['engine_hours']['sufix'] = $sensor->unit_of_measurement;
        }
    }
   // print_r($arr); die;
    return $arr;
}

function getSensorName($sensor) {
    if ( is_array($sensor) )
        return htmlentities($sensor['name'] . (($sensor['type'] == 'fuel_tank' || $sensor['type'] == 'fuel_tank_calibration') && !empty($sensor['fuel_tank_name']) ? ' ('.$sensor['fuel_tank_name'].')' : ''));
    else
        return htmlentities($sensor->name . (($sensor->type == 'fuel_tank' || $sensor->type == 'fuel_tank_calibration') && !empty($sensor->fuel_tank_name) ? ' ('.$sensor->fuel_tank_name.')' : ''));
}

function getPrc($nr1, $nr2) {
    if ($nr1 < $nr2)
        return 100;
    return float(($nr2/$nr1) * 100);
}

function parseSensorsSelect($sensors) {
    $engine_hours = [
        'gps' => trans('front.gps')
    ];

    $detect_engine = [
        'gps' => trans('front.gps')
    ];

    foreach ($sensors as $sensor) {
        if ($sensor['type'] == 'acc') {
            $engine_hours['acc'] = trans('front.sensor').': '.trans('front.acc_on_off');
            $detect_engine['acc'] = trans('front.sensor').': '.trans('front.acc_on_off');
        }
        elseif ($sensor['type'] == 'engine') {
            $engine_hours['engine'] = trans('front.sensor').': '.trans('front.engine_on_off');
            $detect_engine['engine'] = trans('front.sensor').': '.trans('front.engine_on_off');
        }
        elseif ($sensor['type'] == 'ignition') {
            $engine_hours['ignition'] = trans('front.sensor').': '.trans('front.ignition_on_off');
            $detect_engine['ignition'] = trans('front.sensor').': '.trans('front.ignition_on_off');
        }
        elseif ($sensor['type'] == 'engine_hours') {
            $engine_hours['engine_hours'] = trans('front.sensor').': '.trans('validation.attributes.engine_hours');
        }
    }

    return [
        'engine_hours' => $engine_hours,
        'detect_engine' => $detect_engine
    ];
}

function getSensorValueBool($item_other, $sensor, $current_value = NULL) {
    $sensor_value = 2;
    if (is_null($current_value) && !empty($sensor['tag_name'])) {
        preg_match('/<' . preg_quote($sensor['tag_name'], '/') . '>(.*?)<\/' . preg_quote($sensor['tag_name'], '/') . '>/s', $item_other, $matches);
        if (!isset($matches['1']))
            return $sensor_value;

        $value = $matches['1'];
    }
    else
        $value = $current_value;

    if ($sensor['type'] == 'acc') {
        preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['on_value'], $match);
        if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
            $sensor['on_value'] = $match['3'];
            $on_value = substr($value, $match['1'], $match['2']);
        }
        else {
            $on_value = $value;
        }

        preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['off_value'], $match);
        if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
            $sensor['off_value'] = $match['3'];
            $off_value = substr($value, $match['1'], $match['2']);
        }
        else {
            $off_value = $value;
        }
    }

    if (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
        preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['on_tag_value'], $match);
        if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
            $sensor['on_tag_value'] = $match['3'];
            $on_tag_value = substr($value, $match['1'], $match['2']);
        }
        else {
            $on_tag_value = $value;
        }

        preg_match('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $sensor['off_tag_value'], $match);
        if (isset($match['1']) && isset($match['2']) && isset($match['3'])) {
            $sensor['off_tag_value'] = $match['3'];
            $off_tag_value = substr($value, $match['1'], $match['2']);
        }
        else {
            $off_tag_value = $value;
        }
    }

    if ($sensor['type'] == 'acc') {
        if ($sensor['on_value'] == $on_value)
            $sensor_value = 1;

        if ($sensor['off_value'] == $off_value)
            $sensor_value = 0;
    }
    elseif (in_array($sensor['type'], ['ignition', 'door', 'engine', 'drive_business', 'drive_private'])) {
        if (checkCondition($sensor['on_type'], $on_tag_value, $sensor['on_tag_value']))
            $sensor_value = 1;

        if (checkCondition($sensor['off_type'], $off_tag_value, $sensor['off_tag_value']))
            $sensor_value = 0;
    }

    return $sensor_value;
}

function getSensorValueRaw($item_other, $sensor) {
    $sensor = json_decode(json_encode($sensor), TRUE);

    $sensor_value = NULL;
    if (!empty($sensor['tag_name'])) {
        preg_match('/<' . preg_quote($sensor['tag_name'], '/') . '>(.*?)<\/' . preg_quote($sensor['tag_name'], '/') . '>/s', $item_other, $matches);
        if (!isset($matches['1']))
            return $sensor_value;

        $sensor_value = $matches['1'];
    }

    return $sensor_value;
}