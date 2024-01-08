<div id="widgets">
    <a class="btn-collapse" onclick="app.changeSetting('toggleWidgets');"><i></i></a>

    <div class="widgets-content">
        <div class="widget widget-device">
            <div class="widget-heading">
                <div class="widget-title">
                    <div class="pull-right">
                        <span data-device="status"></span> <span data-device="status-text"></span>
                    </div>
                    <i class="icon device"></i>
                    <span data-device="name"></span>
                </div>
            </div>
            <div class="widget-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>Address:</td>
                        <td><span data-device="address"></span></td>
                    </tr>
                    <tr>
                        <td>Time:</td>
                        <td><span data-device="time"></span></td>
                    </tr>
                    <tr>
                        <td>Stop duration:</td>
                        <td><span data-device="stoptime" data-id=""></span></td>
                    </tr>
                    <tr>
                        <td>Driver:</td>
                        <td><span data-device="driver"></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="widget widget-sensors">
            <div class="widget-heading">
                <div class="widget-title"><i class="icon sensors"></i> Sensors</div>
            </div>
            <div class="widget-body">
                <div class="table-container">
                    <table class="table" data-device="sensors">
                        <tbody ></tbody>
                    </table>
                </div>
                {{--
                <div class="table-container">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><i class="icon ignition"></i> Ignition</td>
                            <td><span data-device="ignition"></span></td>
                        </tr>
                        <tr>
                            <td><i class="icon odometer"></i> Odometer</td>
                            <td><span data-device="odometer"></span></td>
                        </tr>
                        <tr>
                            <td><i class="icon fuel"></i> Fuel level</td>
                            <td><span data-device="fuel_tank"></span></td>
                        </tr>
                        <tr>
                            <td><span data-device="battery-l"></span> Battery</td>
                            <td><span data-device="battery"></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-container">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><i class="icon speed"></i> Speed</td>
                            <td><span data-device="speed"></span></td>
                        </tr>
                        <tr>
                            <td><i class="icon tachometer"></i> RMP</td>
                            <td><span data-device="tachometer"></span></td>
                        </tr>
                        <tr>
                            <td><i class="icon temperature"></i> Temperature</td>
                            <td><span data-device="temperature"></span></td>
                        </tr>
                        <tr>
                            <td><span data-device="gsm-l"></span> GSM</td>
                            <td><span data-device="gsm"></span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                --}}
            </div>
        </div>

        <div class="widget widget-services">
            <div class="widget-heading">
                <div class="widget-title">
                    <i class="icon tools"></i> Services
                </div>
            </div>
            <div class="widget-body">
                <table class="table" data-device="services">
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
