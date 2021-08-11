<?php
/**
 * cron job - every 2 hours
 * United States COVID-19 Cases and Deaths by State over Time
 * https://data.cdc.gov/Case-Surveillance/United-States-COVID-19-Cases-and-Deaths-by-State-o/9mfq-cb36
 * API Endpoint: https://data.cdc.gov/resource/9mfq-cb36.json
 */

(function () {
    // begin counting script duration
    $start = microtime(true);

    include('/var/www/html/models/OrionBulkRecord.php');
    date_default_timezone_set('America/Los_Angeles');

    $orion = new OrionBulkRecord('us_cdc');
    $result = $orion->parseJSON("https://data.cdc.gov/resource/9mfq-cb36.json",
        "?submission_date=".date("Y-m-d", time() - 60 * 60 * 24));

    $orion->setColumns('submission_date', 'state', 'tot_cases' , 'conf_cases', 'prob_cases',
        'new_case','pnew_case', 'tot_death', 'conf_death', 'prob_death', 'new_death', 'pnew_death',
        'created_at', 'consent_cases', 'consent_deaths');

    $count = count($result);
    for ($i = 0; $i < $count; $i++) {
        // warnings will occur since some values are missing, will appear null on table
        $orion->insertRow(
            substr($result[$i]->submission_date, 0, 10),
            $result[$i]->state,
            $result[$i]->tot_cases,
            $result[$i]->conf_cases,
            $result[$i]->prob_cases,
            $result[$i]->new_case,
            $result[$i]->pnew_case,
            $result[$i]->tot_death,
            $result[$i]->conf_death,
            $result[$i]->prob_death,
            $result[$i]->new_death,
            $result[$i]->pnew_death,
            $result[$i]->created_at,
            $result[$i]->consent_cases,
            $result[$i]->consent_deaths);
    }

    $num_inserts = $orion->insertBatch('replace');

    $end = microtime(true);
    $orion->logIssue('Schedule', $orion->getTable(), basename(__FILE__) . " completed with $num_inserts inserts.", $end - $start);
})();