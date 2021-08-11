<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . "/controllers/redirect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionRecord.php');

$visitor = new OrionRecord('visitors_unique');
$visitor->ip_address = $_SERVER['REMOTE_ADDR'];
$visitor->insert('ignore');
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>






    <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
        <ul class="navbar-nav">
	    <li class="nav-item active">
                <a class="nav-link" href="/views/about.php"><b>About</b><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link " href="/views/index.php">San Francisco</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/views/us.php">States<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="/views/global.php">Global<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown show">
                <?php
                if (!isset($_SESSION['username'])) {
                    echo '<a class="nav-link" href="/views/user/login.php" id="dropdown08" aria-expanded="true">Login</a>';
                }
                else {
                    echo '<a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown08" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">'.$_SESSION["username"].'</a>';
                }
                ?>
                <div class="dropdown-menu hidden" aria-labelledby="dropdown08">
                    <?php
                    if (!isset($_SESSION['username'])) {
                        echo "<a class='dropdown-item' href='/views/user/login.php'>Login</a>";
                    }
                    else {
                        echo "<a class='dropdown-item' href='/controllers/user/authenticate/logout.php'>Logout</a>";
                    }
                    ?>
                </div>
            </li>



        </ul>
    </div>
</nav>

<!--
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <a class="navbar-brand" href="/views/index.php">SF Covid</a>
        &nbsp;&nbsp;&nbsp;
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item active">
                <a class="nav-link" href="/views/index.php">US <span class="sr-only">(current)</span></a>
            </li>
            &nbsp;&nbsp;&nbsp;
            <li class="nav-item active">
                <a class="nav-link" href="/views/global.php">World</a>
            </li>
        </ul>



    </div>
</nav>

-->

<style>
    .navbar-toggler {
        border: 0;
    }


</style>
