<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

if (!isset($_GET['state'])) {
    redirectTo('/views/include/404.php');
}

$order = 'desc';
if (isset($_GET['order'])) {
    $order = $_GET['order'];
}

recentCases($order);
function recentCases($order) {

    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionRecord('us_cdc');
    $orion->addQuery('state', '=', $_GET['state']);
    $orion->orderBy('submission_date', $order);
    //$orion->limit(90);
    $orion->query('assoc', 'submission_date', 'new_case', 'tot_cases', 'new_death', 'tot_death');

    $tries = 0;
    $results = $orion->getResult();

    echo json_encode($results, JSON_NUMERIC_CHECK);
    //return json_encode($results);
}