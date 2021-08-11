<?php

require_once "OrionRecord.php";

/**
 * Handle comments and posts. Currently uses OrionRecord but will require additional work for
 * reply functionality etc
 */
class OrionPost extends OrionRecord {

    function __construct()
    {
        parent::__construct('posts', 'record_id');
    }



    function isLoggedIn()
    {
        return session_status() === 2 && isset($_SESSION['username']);
    }



    function addComment()
    {
        if (!$this->isLoggedIn()) {
            return 100;
        }

        $this->username = $_SESSION['username'];
        return $this->insert();
    }
}