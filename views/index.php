<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>US Coronavirus Cases</title>

    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php');

    $orion = new OrionRecord('view_count', 'record_id');
    $orion->get('page', 'sanfrancisco');
    $orion->views++;
    $orion->update();

    $visits = new OrionRecord('page_user_count', 'record_id');
    $visits->page = 'san_francisco.php';
    $visits->ip_address = $_SERVER['REMOTE_ADDR'];
    $visits->insert();
    ?>

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

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>


    <script src="/models/OrionAjax.js"></script>
    <script src="/controllers/san_francisco/get_sf_data.js"></script>
    <script>vue_cards();</script>
    <link rel="stylesheet" href="/views/css/sanfrancisco.css">


</head>

<body>

<div class="container-fuid">
    <div class="col-8 map" id="map" style=""></div>
</div>

<h4 style="text-align: center">
    <a href="https://data.sfgov.org/COVID-19/COVID-19-Cases-Summarized-by-Date-Transmission-and/tvq9-ec9w" target="_blank">
        There Is A 5 Day Data Lag
    </a>
</h4>
<div class="container-fluid ">
    <div class="row">
        <div class="col-md-3 col-xs-0"></div>

        <div class="col-md-6 col-xs-0 " id="vue-card">
            <div v-for="zip_code in zip_codes" :value="zip_code.data" class="card text-center">
                <div class="card-header" style="font-size: large">
                    <b>{{ zip_code.zip }}</b>
                </div>
                <div class="card-body">
                    <h5 class="card-title">New Cases: {{zip_code.ncc }}</h5>
                    <h5 class="card-title">Total Cases: {{zip_code.ccc }}</h5>
                    <h5 class="card-title">People Living in {{ zip_code.zip }}: <b>{{zip_code.ap }}</b></h5>
                    <!--<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>-->
                    <!--<a href="#" class="btn btn-success">Show details</a>-->
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xs-0"></div>
    </div>
</div>


</body>

</html>


