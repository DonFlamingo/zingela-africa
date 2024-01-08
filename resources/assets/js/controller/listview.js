function ListView() {
    var
        _this = this,
        listViewTimeout = null,
        loadFails = 0;

    _this.init = function() {

    };

    _this.list = function() {
        $.ajax({
            type: 'GET',
            dataType: "html",
            url: app.urls.listViewItems,
            timeout: 60000,
            beforeSend: function() {
            },
            success: function(res){
                $('#listview').html(res);

                clearTimeout(listViewTimeout);
                listViewTimeout = setTimeout(function(){
                    _this.list();
                }, 10000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401) {
                    return window.location.reload();
                }
                loadFails++;
                if (loadFails >= 5) {
                    loadFails = 0;
                }
                else {
                    _this.list();
                }
            }
        });
    };
}
function listview_settings_create_modal_callback() {
    app.listView.list();
}

function removeListField(el, closest) {
    $(el).closest( closest ).remove();
}
function addListField(el,unique_list) {
    var _key = $(el).val();

    if (!_key) return;

    i_listview++;

    var field = fields[_key];
    var _colors = false;

    var $panel = $('<div class="panel panel-default"><div class="panel-heading" role="tab" id="listfield'+i_listview+'"></div><div id="fieldsettings'+i_listview+'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="listfield'+i_listview+'"></div></div>');

    if ( field.type && numeric_sensors.indexOf(field.type) !== -1 )
        _colors = true;

    var $actions = $('<div class="pull-right"></div>');

    if (_colors)
        $actions.append('<a role="button" data-toggle="collapse" href="#fieldsettings'+i_listview+'" aria-expanded="false" ><i class="fa fa-cog fa-lg" aria-hidden="true"></i></a> ');

    $actions.append('<a role="button" href="javascript:" onClick="removeListField(this, \'.panel\');"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a> ');

    $panel.find('.panel-heading').append( $actions );
    $panel.find('.panel-heading').append( '<h4 class="panel-title">'+field.title+'</h4>' );

    var $body = $('<div class="panel-body"></div>');

    $body.append('<input type="hidden" name="columns['+i_listview+'][field]" value="'+field.field+'">');
    $body.append('<input type="hidden" name="columns['+i_listview+'][class]" value="'+field.class+'">');
    if ( field.type ) {
        $body.append('<input type="hidden" name="columns['+i_listview+'][type]" value="'+field.type+'">');
    }

    if (_colors)
        $body.append('<table class="table"><tr><th>'+window.lang.from+'</th><th>'+window.lang.to+'</th><th>'+window.lang.color+'</th><th><a role="button" href="javascript:" onClick="addListColorField(this, '+i_listview+');"> <i class="fa fa-plus-square" aria-hidden="true"></i> '+window.lang.add+' </a> </th></tr></table>');

    $panel.find('.panel-collapse').append($body);

    $('#'+unique_list).append($panel).collapse().sortable();
}
function addListColorField(el, i) {
    j_listview++;

    var $row = $('<tr></tr>');

    $row.append('<td><input class="form-control" name="columns['+i+'][color]['+j_listview+'][from]" type="text" value="0"></td>');
    $row.append('<td><input class="form-control" name="columns['+i+'][color]['+j_listview+'][to]" type="text" value="0"></td>');
    $row.append('<td><input class="form-control colorpicker" name="columns['+i+'][color]['+j_listview+'][color]" type="text" value="#000000"></td>');
    $row.append('<td><a role="button" href="javascript:" onClick="removeListField(this, \'tr\');"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a></td>');

    $(el).closest('table').append($row);

    $('.colorpicker').colorpicker();
}