<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" name="viewport">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coronavirus Cases</title>

    <script src="/models/OrionAjax.js"></script>
    <script src="/controllers/us/get_state_data_ajax.js"></script>
    <script src="/controllers/us/state_name.js"></script>
    <link rel="stylesheet" type="text/css" href="/views/css/map_table.css">


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



    <!-- maps -->
    <script type="text/javascript" src="js/jquery.vmap.js"></script>
    <script type="text/javascript" src="js/maps/jquery.vmap.world.js" charset="utf-8"></script>


</head>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php'); ?>
<?php
$orion = new OrionRecord('view_count', 'record_id');
$orion->get('page', 'statedetail');
$orion->views++;
$orion->update();

$visits = new OrionRecord('page_user_count', 'record_id');
$visits->page = basename(__FILE__);
$visits->ip_address = $_SERVER['REMOTE_ADDR'];
$visits->insert();
?>


<body>


<p class="state-name"> <?php echo $_GET['state'] ?> </p>

<br>
<div class="col-md-4">
    <input type="text" id="searchbox" class="form-control" placeholder="Search Date Within Last 90 Days"><br>
</div>
<div class="col-md-4"></div>
<div class="col-md-4"></div>



<!-- main table -->
<div class="container-fluid">
    <table id="parent-table" class="table table-bordered display nowrap" style="overflow: hidden; font-size: 15px; width:100%">
        <thead>
        <tr>
            <th style="position: sticky; top: 0; background: white" scope="col">Date</th>
            <th style="position: sticky; top: 0; background: white" scope="col">New Cases</th>
            <th style="position: sticky; top: 0; background: white" scope="col">Total Cases</th>
            <th style="position: sticky; top: 0; background: white" scope="col">New Deaths</th>
            <th style="position: sticky; top: 0; background: white" scope="col">Total Deaths</th>
        </tr>
        </thead>
    </table>
</div>



</body>
</html>



<script>
    getData("<?php echo $_GET['state'] ?>");
</script>

