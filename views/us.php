<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>US Coronavirus Cases</title>

    <?php
    include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

    $orion = new OrionRecord('view_count', 'record_id');
    $orion->get('page', 'us');
    $orion->views++;
    $orion->update();

    $visits = new OrionRecord('page_user_count', 'record_id');
    $visits->page = basename(__FILE__);
    $visits->ip_address = $_SERVER['REMOTE_ADDR'];
    $visits->insert();
    ?>
</head>


<body>

<div class="container-fluid flex-wrap"
     style="font-size: 25px; font-weight: bold; font-family: 'Calibri Light'; padding-bottom: 1vh;">
    <div class="row">
        <div class="col-6 d-flex justify-content-end" id="new_cases_total"></div>
        <div class="col-6 d-flex justify-content-start">New Cases on <?php date_default_timezone_set('America/Los_Angeles'); echo date('F d, Y', time() - 86400); ?></div>
    </div>
    <div class="row">
        <div class="col-6 d-flex justify-content-end" id="cumulative_total"></div>
        <div class="col-6 d-flex justify-content-start"><?php echo intval((time() - strtotime("2020-01-22")) / (60 * 60 * 24)); ?>
            Day Cumulative Cases
        </div>
    </div>
</div>

<!-- map -->
<div class="container-fluid">
    <div class="row">
        <!--<div class="col-md-3 col-xs-4"></div>-->
        <div id="regions_div" style="width: 100vw; height: 80vh; margin: auto"></div>
        <!--<div class="card col-md-2"></div>-->
    </div>

    <div class="row">
        <div style="margin: auto">
            <a href="#us-cases">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-chevron-double-down" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M1.646 6.646a.5.5 0 0 1 .708 0L8 12.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                    <path fill-rule="evenodd"
                          d="M1.646 2.646a.5.5 0 0 1 .708 0L8 8.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

    </div>
</div>

<!-- search box -->
<br>


<a id="us-cases"></a>
    <div class="col-md-4">
        <input type="text" id="searchbox" class="form-control" placeholder="Search State"><br>
    </div>
    <div class="col-md-4"></div>
    <div class="col-md-4"></div>


    <!-- main table -->
    <div class="container-fluid">
        <table id="parent-table" class="table table-bordered display nowrap"
               style="overflow: hidden; font-size: 15px; width:100%">

        </table>
    </div>

</body>
<?php //include($_SERVER['DOCUMENT_ROOT'] . '/views/include/footer.php'); ?>


</html>


<!-- Bootstrap and related JS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>

<!-- Datatables -->
<script type="text/javascript"
        src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.3/fc-3.3.2/fh-3.1.7/kt-2.5.3/r-2.2.6/rg-1.1.2/rr-1.2.7/sc-2.0.3/sl-1.3.1/datatables.min.js"></script>
<link rel="stylesheet" type="text/css"
      href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.3/fc-3.3.2/fh-3.1.7/kt-2.5.3/r-2.2.6/rg-1.1.2/rr-1.2.7/sc-2.0.3/sl-1.3.1/datatables.min.css"/>

<!-- Geocharts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<link rel="stylesheet" type="text/css" href="/views/css/map_table.css">
<script src="/models/OrionAjax.js"></script>
<script src="/controllers/us/get_us_data_ajax.js"></script>
