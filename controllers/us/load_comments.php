<?php

include_once($_SERVER['DOCUMENT_ROOT'].'\models\OrionPosts.php');

$comments = new OrionPost();
$comments->addQuery('related_table', '=','us_cdc');
$comments->addQuery('content', '!=', '');
$comments->orderBy('created', 'descending');
$comments->query('assoc');

echo json_encode($comments->getResult());