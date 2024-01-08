<div class="small-custom-modal modal modal-sm fade" id="gps-device-modal" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="minimize-modal-container">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="small-modal-header">
                        <ul class="nav nav-tabs small-modal-nav" role="tablist">
                            <li role="presentation" class="title"></li>
                            <li data-toggle="tooltip" data-placement="top" title="{{ trans('front.close') }}"><a href="javascript:" data-dismiss="modal"><i class="fa fa-times fa-1"></i></a></li>
                            <li data-toggle="tooltip" data-placement="top" title="{{ trans('front.tags') }}" role="presentation" class="active"><a href="#gps-device-parameters" aria-controls="gps-device-parameters" role="tab" data-toggle="tab"><i class="fa fa-bars fa-1"></i></a></li>
                            <li data-toggle="tooltip" data-placement="top" title="Google Street View" role="presentation"><a href="#gps-device-street-view" aria-controls="gps-device-street-view" role="tab" data-toggle="tab"><i class="fa fa-road fa-1"></i></a></li>
                            {{--<li data-toggle="tooltip" data-placement="top" title="Take Photo" role="presentation"><a href="#gps-device-take-photo" aria-controls="gps-device-take-photo" role="tab" data-toggle="tab"><i class="fa fa-camera fa-1"></i></a></li>--}}
                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="collapse-control">
                            <a href="#"></a>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="gps-device-take-photo">
                            <div class="main-img-container">
                                <img class="small-img" alt="Street view" src="">
                            </div>
                            <div class="buttons">
                                <button class="btn btn-left btn-narrow" type="button" class="btn btn-narrow">Take photo</button>
                                <button class="btn btn-right btn-narrow" type="button" class="btn btn-narrow">Show galery</button>
                            </div>
                            <div class="checkbox checkbox-primary modal-input-group">
                                <input id="auto-take-photo" type="checkbox" checked>
                                <label class="checkbox-label" for="auto-take-photo">Take photo every <span class="auto-take-photo-time">1 hour</span></label>
                                <div class="dropdown custom-dropdown auto-take-photo-dropdown">
                              <span id="autoTakePhotoDropdownMenu" class="dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="fa fa-cog"></i>
                              </span>
                                    <ul class="dropdown-menu" aria-labelledby="autoTakePhotoDropdownMenu">
                                        <div class="arrow-outer"></div>
                                        <div class="arrow-inner"></div>
                                        <li><a href="#">5 min</a></li>
                                        <li><a href="#">30 min</a></li>
                                        <li><a href="#">1 hour</a></li>
                                        <li><a href="#">24 hours</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="gps-device-street-view">
                            <div class="main-img-container">
                                <img class="small-img" alt="Street view" src="">
                            </div>
                            <div class="buttons buttons-right">
                                <button class="btn-enlarge btn btn-narrow" type="button" class="btn btn-narrow">{{ trans('front.enlarge') }} <i class="fa fa-expand fa-1"></i></button>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="gps-device-parameters">
                            <table class="info-table">
                                <tr class="main-params">
                                    <th>{{ trans('front.address') }}:</th>
                                    <td class="device_popup_address"><div class="spinner loading_xsmall"></div></td>
                                </tr>
                                <tr class="main-params">
                                    <th>{{ trans('front.time') }}:</th>
                                    <td class="position_time"></td>
                                </tr>
                                <tr class="main-params">
                                    <th>{{ trans('front.stop_duration') }}:</th>
                                    <td class="device_popup_stoptime"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.position') }}:</th>
                                    <td class="position"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.speed') }}:</th>
                                    <td class="speed"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.altitude') }}:</th>
                                    <td class="altitude"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.angle') }}:</th>
                                    <td class="angle"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.driver') }}:</th>
                                    <td class="driver"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.model') }}:</th>
                                    <td class="model"></td>
                                </tr>
                                {{--<tr class="side-params">
                                    <th>Nearest zone:</th>
                                    <td>Heywood (0.00 km)</td>
                                </tr>--}}
                                <tr class="side-params">
                                    <th>{{ trans('front.plate') }}:</th>
                                    <td class="plate"></td>
                                </tr>
                                <tr class="side-params">
                                    <th>{{ trans('front.protocol') }}:</th>
                                    <td class="protocol"></td>
                                </tr>
                                {{--                                <tr class="side-params">
                                                                    <th>{{ trans('front.time') }} ({{ strtolower(trans('front.position')) }}):</th>
                                                                    <td class="position_time"></td>
                                                                </tr>
                                                                <tr class="side-params">
                                                                    <th>{{ trans('front.time') }} ({{ strtolower(trans('front.server')) }}):</th>
                                                                    <td class="server_time"></td>
                                                                </tr>--}}
                                <tr>
                                    <td colspan="2" class="bottom-button">
                                        <button class="toggle-side-params btn btn-narrow" type="button" class="btn btn-narrow">{{ trans('front.show_more') }}</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="enlarge-modal-container" style="display: none">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p>Google Street View</p>
                    <button class="btn-minimize btn btn-narrow" type="button" class="btn btn-narrow">{{ trans('front.minimize') }} <i class="fa fa-compress fa-1"></i></button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="main-img-container">
                            <img class="small-img" alt="Street view" src="">
                        </div>
                    </div>
                    <div class="info-text">
                        <p><strong>{{ trans('front.object') }}:</strong> <span class="title"></span></p>
                    </div>
                    <div class="info-text">
                        <p><strong>{{ trans('front.address') }}:</strong> <span class="device_popup_address"></span></p>
                    </div>
                    <div class="info-text">
                        <p><strong>{{ trans('front.street_view') }}:</strong> <span class="device_popup_street_view"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>