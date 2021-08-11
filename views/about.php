<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>About</title>

    <?php
    include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

    $orion = new OrionRecord('view_count', 'record_id');
    $orion->get('page', 'about');
    $orion->views++;
    $orion->update();


    $visits = new OrionRecord('page_user_count', 'record_id');
    $visits->page = basename(__FILE__);
    $visits->ip_address = $_SERVER['REMOTE_ADDR'];
    $visits->insert();
    ?>
</head>


<body>

<div class="container-fluid" style="font-size: 25px; font-family: 'Calibri'; padding-top: 7vh">
    <div class="row">
        <div class="col-md-1 col-sm-0"></div>

        <div class="col-md-8 col-sm-12">
            <span style="font-size: 60px"><b>Data Sources</b></span>
            <p>
                <a href="https://data.sfgov.org/stories/s/Map-of-Cumulative-Cases/adm5-wq8i/" target="_blank">SFGOV - Cases By Area</a>
                <a class="green-link" href="https://data.sfgov.org/COVID-19/COVID-19-Cases-by-Geography-and-Date/d2ef-idww" target="_blank">Source</a>

                <br>

                <a href="https://covid.cdc.gov/covid-data-tracker/#cases_casesper100klast7days" target="_blank">CDC - Cases By State</a>
                <a class="green-link" href="https://data.cdc.gov/Case-Surveillance/United-States-COVID-19-Cases-and-Deaths-by-State-o/9mfq-cb36" target="_blank">Source</a>

                <br>

                <a href="https://covid19.who.int/" target="_blank">World Health Organization</a>
                <a class="green-link"  href="https://covid19.who.int/WHO-COVID-19-global-data.csv" target="_blank">(CSV Data File)</a>
            </p>
        </div>

        <div class="col-md-1 col-sm-0"></div>

    </div>
    <div class="row" style="padding-top: 7vh; padding-bottom: 7vh">
        <div class="col-md-1 col-sm-0"></div>

        <div class="col-md-8 col-sm-12" style="font-size: 20px">
            <span style="font-size: 40px"><b>About Me</b></span><br>
            I'm Raymond, a recent Computer Science graduate.<br>
            I love programming, space(x), and astrophysics.<br>
            I hope you'll find information easy to read.<br>
            <b><span style="font-size: 30px">PLEASE send feedback and suggestions to <a href="mailto: sfcovid.web@gmail.com">sfcovid.web@gmail.com</a></span></b>
        </div>

        <div class="col-md-1 col-sm-0"></div>
    </div>

</div>


</body>



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


<style>
    .green-link {
        color: #2b7e2b;
    }
</style>