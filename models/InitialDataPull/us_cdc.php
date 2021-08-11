<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionBulkRecord.php');


(function () {
    $array = ['submission_date', 'state', '@tot_cases', '@conf_cases', '@prob_cases', '@new_case',
        '@pnew_case', '@tot_death', '@conf_death', '@prob_death', '@new_death', '@pnew_death',
        'created_at', 'consent_cases', 'consent_deaths'];
    $custom_sql = " SET tot_cases = NULLIF(@tot_cases, ''), 
                    conf_cases = NULLIF(@conf_cases, ''),
                    prob_cases = NULLIF(@prob_cases, ''),
                    new_case = NULLIF(@new_case, ''),
                    pnew_case = NULLIF(@pnew_case, ''),
                    tot_death = NULLIF(@tot_death, ''),
                    conf_death = NULLIF(@conf_death, ''),
                    prob_death = NULLIF(@prob_death, ''),
                    new_death = NULLIF(@new_death, ''),
                    pnew_death = NULLIF(@pnew_death, ''),
                    created_at = NULLIF(@created_at, ''),
                    consent_cases = NULLIF(@consent_cases, ''),
                    consent_deaths = NULLIF(@consent_deaths, '')";

    $orion = new OrionBulkRecord('us_cdc');
    $result = $orion->insertCSV($orion->downloadFile('https://data.cdc.gov/api/views/9mfq-cb36/rows.csv'),
        ',', '', '\n', 1, $array, $custom_sql);
})();


// convert time from m/d/Y to Y-m-d format
(function () {
    $dash_time = new OrionRecord('us_cdc');
    $dash_time->query();

    $transaction_count = 0;
    $dash_time->beginTransaction();

    while ($dash_time->next()) {
        $transaction_count++;

        if ($transaction_count == 100) {
            $transaction_count = 0;
            $dash_time->commit();
            $dash_time->beginTransaction();
        }

        $dash_time->submission_date = date('Y-m-d', strtotime($dash_time->submission_date));
        $dash_time->update();
    }

    $dash_time->commit();
})();


