<?php
include($_SERVER['DOCUMENT_ROOT'] . '/models/OrionBulkRecord.php');

(function () {
    $array = ['specimen_collection_date', 'case_disposition', 'transmission_category', '@case_count', 'last_updated_at'];
    $custom_sql = " SET case_count = NULLIF(@case_count, '')";


    $orion = new OrionBulkRecord('san_francisco_case_count');
    $result = $orion->insertCSV($orion->downloadFile('https://data.sfgov.org/api/views/tvq9-ec9w/rows.csv'), ',', '', '\n', 1, $array, $custom_sql);

    // convert specimen_collection_date column's time format to use dashes instead of forward slashes
    $dash_time = new OrionRecord('san_francisco_case_count');
    $dash_time->query();

    $dash_time->beginTransaction();
    while ($dash_time->next()) {
        $dash_time->specimen_collection_date = date('Y-m-d', strtotime($dash_time->specimen_collection_date));
        $dash_time->update();
    }
    $dash_time->commit();

    echo $result;
})();
