<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionBulkRecord.php');

(function () {
    $array = ['reportdate', 'hospital', 'dphcategory', 'covidstatus', '@patientcount'];
    $custom_sql = " SET patientcount = NULLIF(@patientcount, '')";


    $orion = new OrionBulkRecord('san_francisco_hospital');
    $result = $orion->insertCSV($orion->downloadFile('https://data.sfgov.org/api/views/nxjg-bhem/rows.csv'), ',', '', '\n', 1, $array, $custom_sql);

    // convert reportdate column's time format to use dashes instead of forward slashes
    $dash_time = new OrionRecord('san_francisco_hospital');
    $dash_time->query();

    $dash_time->beginTransaction();
    while ($dash_time->next()) {
        $dash_time->reportdate = date('Y-m-d', strtotime($dash_time->reportdate));
        $dash_time->update();
    }
    $dash_time->commit();

    echo $result;
})();
