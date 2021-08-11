<?php
include('/var/www/html/models/OrionRecord.php');

(function() {
    $orion = new OrionRecord('view_count', 'record_id');
    $orion->get('1000000005');
    $orion->views++;
    $orion->update();
})();