<?php

redirectTo('/views/index.php');

function redirectTo($page) {
    echo "<script type='text/javascript'>window.top.location='$page'</script>"; exit;
}