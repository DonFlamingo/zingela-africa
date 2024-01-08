<style type="text/css">
    /* cyrillic-ext */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/K88pR3goAWT7BTt32Z01mxJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+0460-052F, U+20B4, U+2DE0-2DFF, U+A640-A69F;
    }
    /* cyrillic */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/RjgO7rYTmqiVp7vzi-Q5URJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
    }
    /* greek-ext */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/LWCjsQkB6EMdfHrEVqA1KRJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+1F00-1FFF;
    }
    /* greek */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/xozscpT2726on7jbcb_pAhJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+0370-03FF;
    }
    /* vietnamese */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/59ZRklaO5bWGqF5A9baEERJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+0102-0103, U+1EA0-1EF9, U+20AB;
    }
    /* latin-ext */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/u-WUoqrET9fUeobQW7jkRRJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
        unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;
    }
    /* latin */
    @font-face {
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/cJZKeOuBrn4kERxqtaUH3VtXRa8TVwTICgirnJhmVJw.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
    }
    body {
        background: #ddd;
        font-family: "Open Sans", sans-serif;
        @if ($data['format'] == 'pdf' || $data['format'] == 'pdf_land')
        font-family: 'dejavu sans';
        @endif
        height: 100%;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .container {
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px; }
    .container:before, .container:after {
        content: " ";
        display: table; }
    .container:after {
        clear: both; }
    @media (min-width: 768px) {
        .container {
            width: 750px; } }
    @media (min-width: 992px) {
        .container {
            width: 970px; } }
    @media (min-width: 1200px) {
        .container {
            width: 1170px; } }

    .container-fluid {
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px; }
    .container-fluid:before, .container-fluid:after {
        content: " ";
        display: table; }
    .container-fluid:after {
        clear: both; }

    .row {
        margin-left: -15px;
        margin-right: -15px; }
    .row:before, .row:after {
        content: " ";
        display: table; }
    .row:after {
        clear: both; }

    .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px; }

    .col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
        float: left; }

    .col-xs-1 {
        width: 8.33333%; }

    .col-xs-2 {
        width: 16.66667%; }

    .col-xs-3 {
        width: 25%; }

    .col-xs-4 {
        width: 33.33333%; }

    .col-xs-5 {
        width: 41.66667%; }

    .col-xs-6 {
        width: 50%; }

    .col-xs-7 {
        width: 58.33333%; }

    .col-xs-8 {
        width: 66.66667%; }

    .col-xs-9 {
        width: 75%; }

    .col-xs-10 {
        width: 83.33333%; }

    .col-xs-11 {
        width: 91.66667%; }

    .col-xs-12 {
        width: 100%; }

    .col-xs-pull-0 {
        right: auto; }

    .col-xs-pull-1 {
        right: 8.33333%; }

    .col-xs-pull-2 {
        right: 16.66667%; }

    .col-xs-pull-3 {
        right: 25%; }

    .col-xs-pull-4 {
        right: 33.33333%; }

    .col-xs-pull-5 {
        right: 41.66667%; }

    .col-xs-pull-6 {
        right: 50%; }

    .col-xs-pull-7 {
        right: 58.33333%; }

    .col-xs-pull-8 {
        right: 66.66667%; }

    .col-xs-pull-9 {
        right: 75%; }

    .col-xs-pull-10 {
        right: 83.33333%; }

    .col-xs-pull-11 {
        right: 91.66667%; }

    .col-xs-pull-12 {
        right: 100%; }

    .col-xs-push-0 {
        left: auto; }

    .col-xs-push-1 {
        left: 8.33333%; }

    .col-xs-push-2 {
        left: 16.66667%; }

    .col-xs-push-3 {
        left: 25%; }

    .col-xs-push-4 {
        left: 33.33333%; }

    .col-xs-push-5 {
        left: 41.66667%; }

    .col-xs-push-6 {
        left: 50%; }

    .col-xs-push-7 {
        left: 58.33333%; }

    .col-xs-push-8 {
        left: 66.66667%; }

    .col-xs-push-9 {
        left: 75%; }

    .col-xs-push-10 {
        left: 83.33333%; }

    .col-xs-push-11 {
        left: 91.66667%; }

    .col-xs-push-12 {
        left: 100%; }

    .col-xs-offset-0 {
        margin-left: 0%; }

    .col-xs-offset-1 {
        margin-left: 8.33333%; }

    .col-xs-offset-2 {
        margin-left: 16.66667%; }

    .col-xs-offset-3 {
        margin-left: 25%; }

    .col-xs-offset-4 {
        margin-left: 33.33333%; }

    .col-xs-offset-5 {
        margin-left: 41.66667%; }

    .col-xs-offset-6 {
        margin-left: 50%; }

    .col-xs-offset-7 {
        margin-left: 58.33333%; }

    .col-xs-offset-8 {
        margin-left: 66.66667%; }

    .col-xs-offset-9 {
        margin-left: 75%; }

    .col-xs-offset-10 {
        margin-left: 83.33333%; }

    .col-xs-offset-11 {
        margin-left: 91.66667%; }

    .col-xs-offset-12 {
        margin-left: 100%; }

    @media (min-width: 768px) {
        .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
            float: left; }

        .col-sm-1 {
            width: 8.33333%; }

        .col-sm-2 {
            width: 16.66667%; }

        .col-sm-3 {
            width: 25%; }

        .col-sm-4 {
            width: 33.33333%; }

        .col-sm-5 {
            width: 41.66667%; }

        .col-sm-6 {
            width: 50%; }

        .col-sm-7 {
            width: 58.33333%; }

        .col-sm-8 {
            width: 66.66667%; }

        .col-sm-9 {
            width: 75%; }

        .col-sm-10 {
            width: 83.33333%; }

        .col-sm-11 {
            width: 91.66667%; }

        .col-sm-12 {
            width: 100%; }

        .col-sm-pull-0 {
            right: auto; }

        .col-sm-pull-1 {
            right: 8.33333%; }

        .col-sm-pull-2 {
            right: 16.66667%; }

        .col-sm-pull-3 {
            right: 25%; }

        .col-sm-pull-4 {
            right: 33.33333%; }

        .col-sm-pull-5 {
            right: 41.66667%; }

        .col-sm-pull-6 {
            right: 50%; }

        .col-sm-pull-7 {
            right: 58.33333%; }

        .col-sm-pull-8 {
            right: 66.66667%; }

        .col-sm-pull-9 {
            right: 75%; }

        .col-sm-pull-10 {
            right: 83.33333%; }

        .col-sm-pull-11 {
            right: 91.66667%; }

        .col-sm-pull-12 {
            right: 100%; }

        .col-sm-push-0 {
            left: auto; }

        .col-sm-push-1 {
            left: 8.33333%; }

        .col-sm-push-2 {
            left: 16.66667%; }

        .col-sm-push-3 {
            left: 25%; }

        .col-sm-push-4 {
            left: 33.33333%; }

        .col-sm-push-5 {
            left: 41.66667%; }

        .col-sm-push-6 {
            left: 50%; }

        .col-sm-push-7 {
            left: 58.33333%; }

        .col-sm-push-8 {
            left: 66.66667%; }

        .col-sm-push-9 {
            left: 75%; }

        .col-sm-push-10 {
            left: 83.33333%; }

        .col-sm-push-11 {
            left: 91.66667%; }

        .col-sm-push-12 {
            left: 100%; }

        .col-sm-offset-0 {
            margin-left: 0%; }

        .col-sm-offset-1 {
            margin-left: 8.33333%; }

        .col-sm-offset-2 {
            margin-left: 16.66667%; }

        .col-sm-offset-3 {
            margin-left: 25%; }

        .col-sm-offset-4 {
            margin-left: 33.33333%; }

        .col-sm-offset-5 {
            margin-left: 41.66667%; }

        .col-sm-offset-6 {
            margin-left: 50%; }

        .col-sm-offset-7 {
            margin-left: 58.33333%; }

        .col-sm-offset-8 {
            margin-left: 66.66667%; }

        .col-sm-offset-9 {
            margin-left: 75%; }

        .col-sm-offset-10 {
            margin-left: 83.33333%; }

        .col-sm-offset-11 {
            margin-left: 91.66667%; }

        .col-sm-offset-12 {
            margin-left: 100%; } }
    @media (min-width: 992px) {
        .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
            float: left; }

        .col-md-1 {
            width: 8.33333%; }

        .col-md-2 {
            width: 16.66667%; }

        .col-md-3 {
            width: 25%; }

        .col-md-4 {
            width: 33.33333%; }

        .col-md-5 {
            width: 41.66667%; }

        .col-md-6 {
            width: 50%; }

        .col-md-7 {
            width: 58.33333%; }

        .col-md-8 {
            width: 66.66667%; }

        .col-md-9 {
            width: 75%; }

        .col-md-10 {
            width: 83.33333%; }

        .col-md-11 {
            width: 91.66667%; }

        .col-md-12 {
            width: 100%; }

        .col-md-pull-0 {
            right: auto; }

        .col-md-pull-1 {
            right: 8.33333%; }

        .col-md-pull-2 {
            right: 16.66667%; }

        .col-md-pull-3 {
            right: 25%; }

        .col-md-pull-4 {
            right: 33.33333%; }

        .col-md-pull-5 {
            right: 41.66667%; }

        .col-md-pull-6 {
            right: 50%; }

        .col-md-pull-7 {
            right: 58.33333%; }

        .col-md-pull-8 {
            right: 66.66667%; }

        .col-md-pull-9 {
            right: 75%; }

        .col-md-pull-10 {
            right: 83.33333%; }

        .col-md-pull-11 {
            right: 91.66667%; }

        .col-md-pull-12 {
            right: 100%; }

        .col-md-push-0 {
            left: auto; }

        .col-md-push-1 {
            left: 8.33333%; }

        .col-md-push-2 {
            left: 16.66667%; }

        .col-md-push-3 {
            left: 25%; }

        .col-md-push-4 {
            left: 33.33333%; }

        .col-md-push-5 {
            left: 41.66667%; }

        .col-md-push-6 {
            left: 50%; }

        .col-md-push-7 {
            left: 58.33333%; }

        .col-md-push-8 {
            left: 66.66667%; }

        .col-md-push-9 {
            left: 75%; }

        .col-md-push-10 {
            left: 83.33333%; }

        .col-md-push-11 {
            left: 91.66667%; }

        .col-md-push-12 {
            left: 100%; }

        .col-md-offset-0 {
            margin-left: 0%; }

        .col-md-offset-1 {
            margin-left: 8.33333%; }

        .col-md-offset-2 {
            margin-left: 16.66667%; }

        .col-md-offset-3 {
            margin-left: 25%; }

        .col-md-offset-4 {
            margin-left: 33.33333%; }

        .col-md-offset-5 {
            margin-left: 41.66667%; }

        .col-md-offset-6 {
            margin-left: 50%; }

        .col-md-offset-7 {
            margin-left: 58.33333%; }

        .col-md-offset-8 {
            margin-left: 66.66667%; }

        .col-md-offset-9 {
            margin-left: 75%; }

        .col-md-offset-10 {
            margin-left: 83.33333%; }

        .col-md-offset-11 {
            margin-left: 91.66667%; }

        .col-md-offset-12 {
            margin-left: 100%; } }
    @media (min-width: 1200px) {
        .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12 {
            float: left; }

        .col-lg-1 {
            width: 8.33333%; }

        .col-lg-2 {
            width: 16.66667%; }

        .col-lg-3 {
            width: 25%; }

        .col-lg-4 {
            width: 33.33333%; }

        .col-lg-5 {
            width: 41.66667%; }

        .col-lg-6 {
            width: 50%; }

        .col-lg-7 {
            width: 58.33333%; }

        .col-lg-8 {
            width: 66.66667%; }

        .col-lg-9 {
            width: 75%; }

        .col-lg-10 {
            width: 83.33333%; }

        .col-lg-11 {
            width: 91.66667%; }

        .col-lg-12 {
            width: 100%; }

        .col-lg-pull-0 {
            right: auto; }

        .col-lg-pull-1 {
            right: 8.33333%; }

        .col-lg-pull-2 {
            right: 16.66667%; }

        .col-lg-pull-3 {
            right: 25%; }

        .col-lg-pull-4 {
            right: 33.33333%; }

        .col-lg-pull-5 {
            right: 41.66667%; }

        .col-lg-pull-6 {
            right: 50%; }

        .col-lg-pull-7 {
            right: 58.33333%; }

        .col-lg-pull-8 {
            right: 66.66667%; }

        .col-lg-pull-9 {
            right: 75%; }

        .col-lg-pull-10 {
            right: 83.33333%; }

        .col-lg-pull-11 {
            right: 91.66667%; }

        .col-lg-pull-12 {
            right: 100%; }

        .col-lg-push-0 {
            left: auto; }

        .col-lg-push-1 {
            left: 8.33333%; }

        .col-lg-push-2 {
            left: 16.66667%; }

        .col-lg-push-3 {
            left: 25%; }

        .col-lg-push-4 {
            left: 33.33333%; }

        .col-lg-push-5 {
            left: 41.66667%; }

        .col-lg-push-6 {
            left: 50%; }

        .col-lg-push-7 {
            left: 58.33333%; }

        .col-lg-push-8 {
            left: 66.66667%; }

        .col-lg-push-9 {
            left: 75%; }

        .col-lg-push-10 {
            left: 83.33333%; }

        .col-lg-push-11 {
            left: 91.66667%; }

        .col-lg-push-12 {
            left: 100%; }

        .col-lg-offset-0 {
            margin-left: 0%; }

        .col-lg-offset-1 {
            margin-left: 8.33333%; }

        .col-lg-offset-2 {
            margin-left: 16.66667%; }

        .col-lg-offset-3 {
            margin-left: 25%; }

        .col-lg-offset-4 {
            margin-left: 33.33333%; }

        .col-lg-offset-5 {
            margin-left: 41.66667%; }

        .col-lg-offset-6 {
            margin-left: 50%; }

        .col-lg-offset-7 {
            margin-left: 58.33333%; }

        .col-lg-offset-8 {
            margin-left: 66.66667%; }

        .col-lg-offset-9 {
            margin-left: 75%; }

        .col-lg-offset-10 {
            margin-left: 83.33333%; }

        .col-lg-offset-11 {
            margin-left: 91.66667%; }

        .col-lg-offset-12 {
            margin-left: 100%; } }
    table {
        background-color: transparent; }

    caption {
        padding-top: 8px;
        padding-bottom: 8px;
        color: #777777;
        text-align: left; }

    th {
        text-align: left; }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px; }
    .table > thead { display: table-header-group }
    .table > tfoot { display: table-row-group }
    .table tr { page-break-inside: avoid }

    .table > thead > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > th,
    .table > tbody > tr > td,
    .table > tfoot > tr > th,
    .table > tfoot > tr > td {
        padding: 8px;
        line-height: 1.42857;
        vertical-align: top;
        border-top: 1px solid #ddd; }
    .table > thead > tr > th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd; }
    .table > caption + thead > tr:first-child > th,
    .table > caption + thead > tr:first-child > td,
    .table > colgroup + thead > tr:first-child > th,
    .table > colgroup + thead > tr:first-child > td,
    .table > thead:first-child > tr:first-child > th,
    .table > thead:first-child > tr:first-child > td {
        border-top: 0; }
    .table > tbody + tbody {
        border-top: 2px solid #ddd; }
    .table .table {
        background-color: #fff; }

    .table-condensed > thead > tr > th,
    .table-condensed > thead > tr > td,
    .table-condensed > tbody > tr > th,
    .table-condensed > tbody > tr > td,
    .table-condensed > tfoot > tr > th,
    .table-condensed > tfoot > tr > td {
        padding: 5px; }

    .table-bordered {
        border: 1px solid #ddd; }
    .table-bordered > thead > tr > th,
    .table-bordered > thead > tr > td,
    .table-bordered > tbody > tr > th,
    .table-bordered > tbody > tr > td,
    .table-bordered > tfoot > tr > th,
    .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd; }
    .table-bordered > thead > tr > th,
    .table-bordered > thead > tr > td {
        border-bottom-width: 2px; }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f9f9f9; }

    .table-hover > tbody > tr:hover {
        background-color: #f5f5f5; }

    table col[class*="col-"] {
        position: static;
        float: none;
        display: table-column; }

    table td[class*="col-"],
    table th[class*="col-"] {
        position: static;
        float: none;
        display: table-cell; }

    .table > thead > tr > td.active,
    .table > thead > tr > th.active, .table > thead > tr.active > td, .table > thead > tr.active > th,
    .table > tbody > tr > td.active,
    .table > tbody > tr > th.active,
    .table > tbody > tr.active > td,
    .table > tbody > tr.active > th,
    .table > tfoot > tr > td.active,
    .table > tfoot > tr > th.active,
    .table > tfoot > tr.active > td,
    .table > tfoot > tr.active > th {
        background-color: #f5f5f5; }

    .table-hover > tbody > tr > td.active:hover,
    .table-hover > tbody > tr > th.active:hover, .table-hover > tbody > tr.active:hover > td, .table-hover > tbody > tr:hover > .active, .table-hover > tbody > tr.active:hover > th {
        background-color: #e8e8e8; }

    .table > thead > tr > td.success,
    .table > thead > tr > th.success, .table > thead > tr.success > td, .table > thead > tr.success > th,
    .table > tbody > tr > td.success,
    .table > tbody > tr > th.success,
    .table > tbody > tr.success > td,
    .table > tbody > tr.success > th,
    .table > tfoot > tr > td.success,
    .table > tfoot > tr > th.success,
    .table > tfoot > tr.success > td,
    .table > tfoot > tr.success > th {
        background-color: #dff0d8; }

    .table-hover > tbody > tr > td.success:hover,
    .table-hover > tbody > tr > th.success:hover, .table-hover > tbody > tr.success:hover > td, .table-hover > tbody > tr:hover > .success, .table-hover > tbody > tr.success:hover > th {
        background-color: #d0e9c6; }

    .table > thead > tr > td.info,
    .table > thead > tr > th.info, .table > thead > tr.info > td, .table > thead > tr.info > th,
    .table > tbody > tr > td.info,
    .table > tbody > tr > th.info,
    .table > tbody > tr.info > td,
    .table > tbody > tr.info > th,
    .table > tfoot > tr > td.info,
    .table > tfoot > tr > th.info,
    .table > tfoot > tr.info > td,
    .table > tfoot > tr.info > th {
        background-color: #d9edf7; }

    .table-hover > tbody > tr > td.info:hover,
    .table-hover > tbody > tr > th.info:hover, .table-hover > tbody > tr.info:hover > td, .table-hover > tbody > tr:hover > .info, .table-hover > tbody > tr.info:hover > th {
        background-color: #c4e3f3; }

    .table > thead > tr > td.warning,
    .table > thead > tr > th.warning, .table > thead > tr.warning > td, .table > thead > tr.warning > th,
    .table > tbody > tr > td.warning,
    .table > tbody > tr > th.warning,
    .table > tbody > tr.warning > td,
    .table > tbody > tr.warning > th,
    .table > tfoot > tr > td.warning,
    .table > tfoot > tr > th.warning,
    .table > tfoot > tr.warning > td,
    .table > tfoot > tr.warning > th {
        background-color: #fcf8e3; }

    .table-hover > tbody > tr > td.warning:hover,
    .table-hover > tbody > tr > th.warning:hover, .table-hover > tbody > tr.warning:hover > td, .table-hover > tbody > tr:hover > .warning, .table-hover > tbody > tr.warning:hover > th {
        background-color: #faf2cc; }

    .table > thead > tr > td.danger,
    .table > thead > tr > th.danger, .table > thead > tr.danger > td, .table > thead > tr.danger > th,
    .table > tbody > tr > td.danger,
    .table > tbody > tr > th.danger,
    .table > tbody > tr.danger > td,
    .table > tbody > tr.danger > th,
    .table > tfoot > tr > td.danger,
    .table > tfoot > tr > th.danger,
    .table > tfoot > tr.danger > td,
    .table > tfoot > tr.danger > th {
        background-color: #f2dede; }

    .table-hover > tbody > tr > td.danger:hover,
    .table-hover > tbody > tr > th.danger:hover, .table-hover > tbody > tr.danger:hover > td, .table-hover > tbody > tr:hover > .danger, .table-hover > tbody > tr.danger:hover > th {
        background-color: #ebcccc; }

    .table-responsive {
        overflow-x: auto;
        min-height: 0.01%; }
    @media screen and (max-width: 767px) {
        .table-responsive {
            width: 100%;
            margin-bottom: 15px;
            overflow-y: hidden;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            border: 1px solid #ddd; }
        .table-responsive > .table {
            margin-bottom: 0; }
        .table-responsive > .table > thead > tr > th,
        .table-responsive > .table > thead > tr > td,
        .table-responsive > .table > tbody > tr > th,
        .table-responsive > .table > tbody > tr > td,
        .table-responsive > .table > tfoot > tr > th,
        .table-responsive > .table > tfoot > tr > td {
            white-space: nowrap; }
        .table-responsive > .table-bordered {
            border: 0; }
        .table-responsive > .table-bordered > thead > tr > th:first-child,
        .table-responsive > .table-bordered > thead > tr > td:first-child,
        .table-responsive > .table-bordered > tbody > tr > th:first-child,
        .table-responsive > .table-bordered > tbody > tr > td:first-child,
        .table-responsive > .table-bordered > tfoot > tr > th:first-child,
        .table-responsive > .table-bordered > tfoot > tr > td:first-child {
            border-left: 0; }
        .table-responsive > .table-bordered > thead > tr > th:last-child,
        .table-responsive > .table-bordered > thead > tr > td:last-child,
        .table-responsive > .table-bordered > tbody > tr > th:last-child,
        .table-responsive > .table-bordered > tbody > tr > td:last-child,
        .table-responsive > .table-bordered > tfoot > tr > th:last-child,
        .table-responsive > .table-bordered > tfoot > tr > td:last-child {
            border-right: 0; }
        .table-responsive > .table-bordered > tbody > tr:last-child > th,
        .table-responsive > .table-bordered > tbody > tr:last-child > td,
        .table-responsive > .table-bordered > tfoot > tr:last-child > th,
        .table-responsive > .table-bordered > tfoot > tr:last-child > td {
            border-bottom: 0; } }


    .panel {
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid transparent;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05); }

    .panel-body {
        padding: 15px; }
    .panel-body:before, .panel-body:after {
        content: " ";
        display: table; }
    .panel-body:after {
        clear: both; }

    .panel-heading {
        padding: 10px 15px;
        border-bottom: 1px solid transparent;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px; }
    .panel-heading > .dropdown .dropdown-toggle {
        color: inherit; }

    .panel-title {
        margin-top: 0;
        margin-bottom: 0;
        font-size: 16px;
        color: inherit; }
    .panel-title > a,
    .panel-title > small,
    .panel-title > .small,
    .panel-title > small > a,
    .panel-title > .small > a {
        color: inherit; }

    .panel-footer {
        padding: 10px 15px;
        background-color: #f5f5f5;
        border-top: 1px solid #ddd;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px; }

    .panel > .list-group,
    .panel > .panel-collapse > .list-group {
        margin-bottom: 0; }
    .panel > .list-group .list-group-item,
    .panel > .panel-collapse > .list-group .list-group-item {
        border-width: 1px 0;
        border-radius: 0; }
    .panel > .list-group:first-child .list-group-item:first-child,
    .panel > .panel-collapse > .list-group:first-child .list-group-item:first-child {
        border-top: 0;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px; }
    .panel > .list-group:last-child .list-group-item:last-child,
    .panel > .panel-collapse > .list-group:last-child .list-group-item:last-child {
        border-bottom: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px; }
    .panel > .panel-heading + .panel-collapse > .list-group .list-group-item:first-child {
        border-top-right-radius: 0;
        border-top-left-radius: 0; }

    .panel-heading + .list-group .list-group-item:first-child {
        border-top-width: 0; }

    .list-group + .panel-footer {
        border-top-width: 0; }
    .panel > .table,
    .panel > .table-responsive > .table,
    .panel > .panel-collapse > .table {
        margin-bottom: 0; }
    .panel > .table caption,
    .panel > .table-responsive > .table caption,
    .panel > .panel-collapse > .table caption {
        padding-left: 15px;
        padding-right: 15px; }
    .panel > .table:first-child,
    .panel > .table-responsive:first-child > .table:first-child {
        border-top-right-radius: 3px;
        border-top-left-radius: 3px; }
    .panel > .table:first-child > thead:first-child > tr:first-child,
    .panel > .table:first-child > tbody:first-child > tr:first-child,
    .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child,
    .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child {
        border-top-left-radius: 3px;
        border-top-right-radius: 3px; }
    .panel > .table:first-child > thead:first-child > tr:first-child td:first-child,
    .panel > .table:first-child > thead:first-child > tr:first-child th:first-child,
    .panel > .table:first-child > tbody:first-child > tr:first-child td:first-child,
    .panel > .table:first-child > tbody:first-child > tr:first-child th:first-child,
    .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child td:first-child,
    .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child th:first-child,
    .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child td:first-child,
    .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child th:first-child {
        border-top-left-radius: 3px; }
    .panel > .table:first-child > thead:first-child > tr:first-child td:last-child,
    .panel > .table:first-child > thead:first-child > tr:first-child th:last-child,
    .panel > .table:first-child > tbody:first-child > tr:first-child td:last-child,
    .panel > .table:first-child > tbody:first-child > tr:first-child th:last-child,
    .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child td:last-child,
    .panel > .table-responsive:first-child > .table:first-child > thead:first-child > tr:first-child th:last-child,
    .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child td:last-child,
    .panel > .table-responsive:first-child > .table:first-child > tbody:first-child > tr:first-child th:last-child {
        border-top-right-radius: 3px; }
    .panel > .table:last-child,
    .panel > .table-responsive:last-child > .table:last-child {
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px; }
    .panel > .table:last-child > tbody:last-child > tr:last-child,
    .panel > .table:last-child > tfoot:last-child > tr:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child {
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px; }
    .panel > .table:last-child > tbody:last-child > tr:last-child td:first-child,
    .panel > .table:last-child > tbody:last-child > tr:last-child th:first-child,
    .panel > .table:last-child > tfoot:last-child > tr:last-child td:first-child,
    .panel > .table:last-child > tfoot:last-child > tr:last-child th:first-child,
    .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child td:first-child,
    .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child th:first-child,
    .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child td:first-child,
    .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child th:first-child {
        border-bottom-left-radius: 3px; }
    .panel > .table:last-child > tbody:last-child > tr:last-child td:last-child,
    .panel > .table:last-child > tbody:last-child > tr:last-child th:last-child,
    .panel > .table:last-child > tfoot:last-child > tr:last-child td:last-child,
    .panel > .table:last-child > tfoot:last-child > tr:last-child th:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child td:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tbody:last-child > tr:last-child th:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child td:last-child,
    .panel > .table-responsive:last-child > .table:last-child > tfoot:last-child > tr:last-child th:last-child {
        border-bottom-right-radius: 3px; }
    .panel > .panel-body + .table,
    .panel > .panel-body + .table-responsive,
    .panel > .table + .panel-body,
    .panel > .table-responsive + .panel-body {
        border-top: 1px solid #ddd; }
    .panel > .table > tbody:first-child > tr:first-child th,
    .panel > .table > tbody:first-child > tr:first-child td {
        border-top: 0; }
    .panel > .table-bordered,
    .panel > .table-responsive > .table-bordered {
        border: 0; }
    .panel > .table-bordered > thead > tr > th:first-child,
    .panel > .table-bordered > thead > tr > td:first-child,
    .panel > .table-bordered > tbody > tr > th:first-child,
    .panel > .table-bordered > tbody > tr > td:first-child,
    .panel > .table-bordered > tfoot > tr > th:first-child,
    .panel > .table-bordered > tfoot > tr > td:first-child,
    .panel > .table-responsive > .table-bordered > thead > tr > th:first-child,
    .panel > .table-responsive > .table-bordered > thead > tr > td:first-child,
    .panel > .table-responsive > .table-bordered > tbody > tr > th:first-child,
    .panel > .table-responsive > .table-bordered > tbody > tr > td:first-child,
    .panel > .table-responsive > .table-bordered > tfoot > tr > th:first-child,
    .panel > .table-responsive > .table-bordered > tfoot > tr > td:first-child {
        border-left: 0; }
    .panel > .table-bordered > thead > tr > th:last-child,
    .panel > .table-bordered > thead > tr > td:last-child,
    .panel > .table-bordered > tbody > tr > th:last-child,
    .panel > .table-bordered > tbody > tr > td:last-child,
    .panel > .table-bordered > tfoot > tr > th:last-child,
    .panel > .table-bordered > tfoot > tr > td:last-child,
    .panel > .table-responsive > .table-bordered > thead > tr > th:last-child,
    .panel > .table-responsive > .table-bordered > thead > tr > td:last-child,
    .panel > .table-responsive > .table-bordered > tbody > tr > th:last-child,
    .panel > .table-responsive > .table-bordered > tbody > tr > td:last-child,
    .panel > .table-responsive > .table-bordered > tfoot > tr > th:last-child,
    .panel > .table-responsive > .table-bordered > tfoot > tr > td:last-child {
        border-right: 0; }
    .panel > .table-bordered > thead > tr:first-child > td,
    .panel > .table-bordered > thead > tr:first-child > th,
    .panel > .table-bordered > tbody > tr:first-child > td,
    .panel > .table-bordered > tbody > tr:first-child > th,
    .panel > .table-responsive > .table-bordered > thead > tr:first-child > td,
    .panel > .table-responsive > .table-bordered > thead > tr:first-child > th,
    .panel > .table-responsive > .table-bordered > tbody > tr:first-child > td,
    .panel > .table-responsive > .table-bordered > tbody > tr:first-child > th {
        border-bottom: 0; }
    .panel > .table-bordered > tbody > tr:last-child > td,
    .panel > .table-bordered > tbody > tr:last-child > th,
    .panel > .table-bordered > tfoot > tr:last-child > td,
    .panel > .table-bordered > tfoot > tr:last-child > th,
    .panel > .table-responsive > .table-bordered > tbody > tr:last-child > td,
    .panel > .table-responsive > .table-bordered > tbody > tr:last-child > th,
    .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > td,
    .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > th {
        border-bottom: 0; }
    .panel > .table-responsive {
        border: 0;
        margin-bottom: 0; }

    .panel-group {
        margin-bottom: 20px; }
    .panel-group .panel {
        margin-bottom: 0;
        border-radius: 4px; }
    .panel-group .panel + .panel {
        margin-top: 5px; }
    .panel-group .panel-heading {
        border-bottom: 0; }
    .panel-group .panel-heading + .panel-collapse > .panel-body,
    .panel-group .panel-heading + .panel-collapse > .list-group {
        border-top: 1px solid #ddd; }
    .panel-group .panel-footer {
        border-top: 0; }
    .panel-group .panel-footer + .panel-collapse .panel-body {
        border-bottom: 1px solid #ddd; }

    .panel-default {
        border-color: #ddd; }
    .panel-default > .panel-heading {
        color: #333333;
        background-color: #f5f5f5;
        border-color: #ddd; }
    .panel-default > .panel-heading + .panel-collapse > .panel-body {
        border-top-color: #ddd; }
    .panel-default > .panel-heading .badge {
        color: #f5f5f5;
        background-color: #333333; }
    .panel-default > .panel-footer + .panel-collapse > .panel-body {
        border-bottom-color: #ddd; }

    .reports {
        background-color: #ffffff;
        color: #787878;
        font-size: 12px; }
    .reports header {
        margin-top: 20px;
        margin-bottom: 20px; }
    .reports header .header-left {
        display: inline-block;
        width: 246px;
        height: 49px; }
    .reports header .header-left .report-wrap {
        width: 171px;
        height: 49px;
        border-bottom: 1px solid #CECCCD;
        float: left; }
    .reports header .header-left .report-curve {
        float: left; }
    .reports header .header-right {
        position: absolute;
        right: 15px;
        left: 261px;
        display: inline-block;
        height: 49px;
        border-top: 1px solid #CECCCD; }
    .reports .panel {
        margin-top: 20px; }
    .reports .panel .panel-heading {
        background-color: #ffffff;
        padding: 7px 7px 7px 7px;
        line-height: 2.49; }
    .reports .panel .panel-heading .btn {
        font-size: 12px;
        color: #787878;
        min-width: 75px;
        line-height: normal; }
    .reports .panel .panel-body {
        font-size: 11px; }
    .reports .panel .panel-body.no-padding {
        padding-left: 0;
        padding-right: 0;
        padding-top: 0; }
    .reports .panel .panel-body ~ .panel-body {
        border-top: 1px solid #ddd; }
    .reports .panel .panel-body .table-speed thead tr th,
    .reports .panel .panel-body .table-speed thead tr td {
        background-color: #EEE;
        color: #323232;
        font-weight: normal;
        text-align: center;
        font-size: 10px;
        padding: 3px; }
    .reports .panel .panel-body .table-speed tbody tr td {
        text-align: center;
        font-size: 10px;
        padding: 3px; }
    .reports .panel .panel-body .table-speed tbody tr:hover td {
        background-color: #1A99BC;
        color: #FFF; }
    .reports .table th,
    .reports .table td {
        border: 0px;
        padding: 4px; }

    .report-bars {
        display: inline-block;
        width: 11px;
        height: 10px;
        margin-right: 4px;
        background: url({{ asset('assets/image/report_bars.jpg') }}) no-repeat center center; }

    .report-logo {
        width: 171px;
        height: 40px; }

    .report-logo img {
        max-width: 100%;
    }

    .report-curve {
        width: 75px;
        height: 49px;
        background: url({{ asset('assets/image/report_curve.jpg') }}) no-repeat center center; }

    .graph-1-wrap {
        width: 100%;
        height: 150px;
        overflow-x: hidden;
        overflow-y: hidden; }
    .graph-1-wrap .graph-1 {
        width: 100%;
        height: 150px;
        padding-right: 13px !important; }
    .graph-1-wrap .graph-1-controls {
        position: absolute;
        top: 10px;
        right: 7px; }
    .graph-1-wrap .graph-1-controls .btn {
        border-color: #AAAAAA; }
    .graph-1-wrap .graph-1-controls a {
        color: #AAAAAA; }
    .graph-1-wrap .graph-1-controls a:hover {
        color: #1C98BC; }

    .tab-pane .graph-1-wrap {
        margin-top: 15px;
        width: 100%;
        height: 170px;
        overflow-x: hidden;
        overflow-y: hidden; }
    .tab-pane .graph-1-wrap .graph-1 {
        width: 100%;
        height: 170px;
        padding-right: 23px !important; }

    .pie-1,
    .pie-2,
    .pie-3 {
        width: 100%;
        height: 300px; }

    .graph-2-wrap {
        width: 100%;
        height: 300px;
        overflow-x: hidden;
        overflow-y: hidden; }
    .graph-2-wrap .graph-2 {
        width: 100%;
        height: 300px; }

    .graph-3-wrap {
        width: 100%;
        height: 300px;
        overflow-x: hidden;
        overflow-y: hidden; }
    .graph-3-wrap .graph-3 {
        width: 100%;
        height: 300px; }

    .valueLabels {
        font-size: 100%;
        color: #FFFFFF; }

    div.valueLabelLight {
        display: none;
        border: none;
        position: absolute; }

    div.valueLabel {
        position: absolute;
        border: none;
        margin-left: -25px;
        margin-top: -2px; }

    table.table-right th, table.table-left th {
        width: 50%;
    }

    table.table-right {
        width: 49%;
        float: right;
    }

    table.table-left {
        width: 49%;
        float: left;
    }
    .pull-right {
        float: right;
    }
</style>