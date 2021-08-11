<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <title>Coronavirus Disease 2019</title>

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


    <link rel="stylesheet" href="/css/frontpage.css">
    <?php ini_set('display_errors', 0);
    error_reporting(0); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php'); ?>

</head>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . '/views/include/header.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

    $visits = new OrionRecord('page_user_count', 'record_id');
    $visits->page = basename(__FILE__);
    $visits->ip_address = $_SERVER['REMOTE_ADDR'];
    $visits->insert();
?>


<div style="margin: auto">
    <p class="big-not-found">404</p>
    <p class="not-found">NOT FOUND</p>

    <div class="spacer"></div>

    <p class="not-found">This link does not exist</p>
</div>

<style>
    .big-not-found {
        font-size: 150px;
        color: #484343;
        text-align: center;
        font-family: "Roboto",sans-serif
    }

    .not-found {
        font-size: 50px;
        color: #484343;
        text-align: center;
        font-family: "Roboto",sans-serif
    }

    .spacer {
        height: 20vh;
        width: 100vw;
    }
</style>