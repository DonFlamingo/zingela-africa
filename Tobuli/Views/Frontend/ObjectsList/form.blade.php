<?php $unique_list = 'listfields-' . time(); ?>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('groupby', trans('front.groupby')) !!}
            {!! Form::select('groupby', ['protocol' => trans('front.protocol'), 'group' => trans('validation.attributes.group_id') ], $settings['groupby'], ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="add-column">{{ trans('front.add_column') }}</label>
            <select class="form-control" onchange="addListField(this, '{{ $unique_list }}');" id="add-column">
                @foreach($fields as $key => $field)
                <option value="{{ $key }}">{{ $field['title'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<hr>

<div class="panel-group" id="{{ $unique_list }}" role="tablist" aria-multiselectable="true">
    <?php $i = 0; $j = 0; ?>
    @foreach($settings['columns'] as $key => $column)
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="listfield{{ $i }}">
            <div class="pull-right">
                @if(!empty($column['type']) && in_array($column['type'], $numeric_sensors))
                <a role="button" data-toggle="collapse" href="#fieldsettings{{ $i }}" aria-expanded="false" >
                    <i class="fa fa-cog fa-lg" aria-hidden="true"></i>
                </a>
                @endif
                <a role="button" href="javascript:" onClick="removeListField(this, '.panel');">
                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                </a>
            </div>
            <h4 class="panel-title">{{ $column['title'] }}</h4>
        </div>
        <div id="fieldsettings{{ $i }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="listfield{{ $i }}">
            <div class="panel-body">
                {!!Form::hidden('columns['.$i.'][field]', $column['field'])!!}
                {!!Form::hidden('columns['.$i.'][class]', $column['class'])!!}

                @if(!empty($column['type']))
                {!!Form::hidden('columns['.$i.'][type]', $column['type'])!!}
                @endif

                <table class="table">
                    <tr>
                        <th>{{ trans('front.from') }}</th>
                        <th>{{ trans('front.to') }}</th>
                        <th>{{ trans('validation.attributes.color') }}</th>
                        <th>
                            <a role="button" href="javascript:" onClick="addListColorField(this, {{ $i }});">
                                <i class="fa fa-plus-square" aria-hidden="true"></i> {{ trans('global.add') }}
                            </a>
                        </th>
                    </tr>
                    @unless(empty($column['color']))
                        @foreach($column['color'] as $j => $color)
                        <tr>
                            <td>{!!Form::text('columns['.$i.'][color]['.$j.'][from]', $color['from'], ['class' => 'form-control'])!!}</td>
                            <td>{!!Form::text('columns['.$i.'][color]['.$j.'][to]', $color['to'], ['class' => 'form-control'])!!}</td>
                            <td>{!!Form::text('columns['.$i.'][color]['.$j.'][color]', $color['color'], ['class' => 'form-control colorpicker'])!!}</td>
                            <td>
                                <a role="button" href="javascript:" onClick="removeListField(this, 'tr');">
                                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $j++; ?>
                        @endforeach
                    @endunless
                </table>
            </div>
        </div>
    </div>
    <?php $i++; ?>
    @endforeach
</div>

<script>
  var i_listview  = {{ $i }};
  var j_listview = {{ $j }};
  var numeric_sensors = JSON.parse('{!! json_encode($numeric_sensors) !!}');
  var fields = JSON.parse('{!! json_encode($fields) !!}');

  $( function() {
    $( "#{{ $unique_list }}" ).collapse().sortable({
        handle: '.panel-heading'
    });
    $('.colorpicker').colorpicker();
  } );
</script>