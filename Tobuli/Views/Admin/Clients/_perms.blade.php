<table class="table">
    <thead>
    <th style="text-align: left">{{ trans('front.permission') }}</th>
    <th style="text-align: center">{{ trans('front.view') }}</th>
    <th style="text-align: center">{{ trans('global.edit') }}</th>
    <th style="text-align: center">{{ trans('global.delete') }}</th>
    </thead>
    <tbody>
    @foreach($perms as $perm => $modes)
        <tr>
            <td>{{ trans('front.'.$perm) }}</td>
            <td style="text-align: center">
                <div class="checkbox">
                    @if ($modes['view'])
                        {!! Form::checkbox("perms[$perm][view]", 1, (isset($item) && !is_null($item) ? $item->perm($perm, 'view') : (isset($def_perms[$perm]) && getArrValue($def_perms[$perm], 'view'))), ['class' => 'perm_checkbox perm_view'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                    @else
                        {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                    @endif
                    {!! Form::label(null, null) !!}
                </div>
            </td>
            <td style="text-align: center">
                <div class="checkbox">
                    @if ($modes['edit'])
                        {!! Form::checkbox("perms[$perm][edit]", 1, (isset($item) && !is_null($item) ? $item->perm($perm, 'edit') : (isset($def_perms[$perm]) && getArrValue($def_perms[$perm], 'edit'))), ['class' => 'perm_checkbox perm_edit'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                    @else
                        {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                    @endif
                    {!! Form::label(null, null) !!}
                </div>
            </td>
            <td style="text-align: center">
                <div class="checkbox">
                    @if ($modes['remove'])
                        {!! Form::checkbox("perms[$perm][remove]", 1, (isset($item) && !is_null($item) ? $item->perm($perm, 'remove') : (isset($def_perms[$perm]) && getArrValue($def_perms[$perm], 'remove'))), ['class' => 'perm_checkbox perm_remove'] + (!empty($plan) ? ['disabled' => 'disabled'] : [])) !!}
                    @else
                        {!! Form::checkbox('', 0, 0, ['disabled' => 'disabled']) !!}
                    @endif
                    {!! Form::label(null, null) !!}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    $(document).on('change', 'input.perm_checkbox', function() {
        checkPerm($(this));
    });
</script>