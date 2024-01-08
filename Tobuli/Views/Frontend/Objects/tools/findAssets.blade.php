<div class="modal fade" id="findAssets">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
                <h4 class="modal-title"><i class="icon search"></i> Find Assets</h4>
            </div>

            <div class="modal-body">
              <ul class="nav nav-tabs nav-default" role="tablist">
                  <li class="active"><a href="#findAssets-form-vehicles" role="tab" data-toggle="tab">By Vehicles</a></li>
                  <li><a href="#findAssets-form-poi" role="tab" data-toggle="tab">By POI</a></li>
              </ul>
              <div class="tab-content">
                <div id="findAssets-form-vehicles" class="tab-pane active">
                {!! Form::open(array('id' => 'findAssets-vehicles-form', 'route' => 'objects.find_assets.vehicle')) !!}
                <div class="form-group">
                        {!!Form::label('distance', 'Within km:')!!}
                        {!!Form::text('findAsset_vehicle_distance', null, ['class' => 'form-control', 'id' => 'findAsset_vehicle_distance'])!!}
                </div>
                    <div class="form-group">
                            {!!Form::label('vehicles', 'Of:')!!}
                            <select class="form-control" id="findAsset_vehicle" name="findAsset_vehicle" tabindex="-98">
                              @foreach($devices_find as $device_find)
                                <option value="{{$device_find->id}}|{{$device_find->name}}|{{$device_find->traccar->lastValidLatitude}}|{{$device_find->traccar->lastValidLongitude}}">{{$device_find->name}}</option>
                              @endforeach
                            </select>
                    </div>
                {!! Form::close() !!}
                <div class="modal-footer">
                    <button type="button" id="findAssetsVehicle" class="btn btn-action" onclick="app.findAssetsVehicle();" data-dismiss="modal">{!!trans('global.show')!!}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
                </div>
              </div>
                <div id="findAssets-form-poi" class="tab-pane">
                  {!! Form::open(array('id' => 'findAssets-poi-form', 'route' => 'objects.find_assets.poi')) !!}
                  <div class="form-group">
                          {!!Form::label('distance', 'Within km:')!!}
                          {!!Form::text('findAsset_poi_distance', null, ['class' => 'form-control', 'id' => 'findAsset_poi_distance'])!!}
                  </div>
                      <div class="form-group">
                              {!!Form::label('poi', 'Of:')!!}
                              <select class="form-control" id="findAsset_poi" name="findAsset_poi" tabindex="-98">
                                @foreach($poi_find as $poii)
                                  <option value="{{$poii->coordinates}}|{{$poii->name}}">{{$poii->name}}</option>
                                @endforeach
                              </select>
                      </div>
                  {!! Form::close() !!}
                  <div class="modal-footer">
                      <button type="button" id="findAssetsPoi" class="btn btn-action" onclick="app.findAssetsPOI();" data-dismiss="modal">{!!trans('global.show')!!}</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.close')!!}</button>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>
