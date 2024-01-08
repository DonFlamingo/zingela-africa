<?php

/**
 * @param $title
 * @param string $extra
 * @return string
 */
function tableHeader($title, $extra = '') {
    $html = '<th class="sorting_disabled" '.$extra.'>
                '. trans($title) .'
            </th>';

    return $html;
}

/**
 * @param $input
 * @param $field
 * @param null $title
 * @param string $extra
 * @return string
 */
function tableHeaderSort($sorting, $field, $title = NULL, $extra = '') {
    $html = '<th class="sorting'.
        ($sorting['sort_by'] == $field ? '_'.$sorting['sort'] : '') # If header active class "sorting_desc" or "sortinc_asc"
        .'" data-id="'.$field.'" '.$extra.'>
                '. trans(!is_null($title) ? $title : "validation.attributes.{$field}") .'
            </th>';

    return $html;
}

/**
 * @return string
 */
function tableHeaderCheckall($actions = []) {
    $checkboxHtml =
                '<div class="checkbox">
                    <input type="checkbox" data-toggle="checkbox">
                    <label></label>
                </div>';

    $actionHtml = '';
    if ( $actions ) {
        $actionHtml .=
            '<div class="btn-group dropdown">
                    <i class="btn icon multi-edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ></i>
                    <ul class="dropdown-menu">';

        foreach ($actions as $key => $text) {
            $actionHtml .= '<li><a href="javascript:" data-multi="'.$key.'">'.$text.'</a></li>';
        }

        $actionHtml .= '</ul></div>';
    }

    $html = '<th class="table-checkbox sorting_disabled" role="columnheader" rowspan="1" colspan="1">'
        . $checkboxHtml
        . $actionHtml
        . '</th>';

    return $html;
}