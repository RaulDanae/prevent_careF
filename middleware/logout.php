<?php
    session_start();
    session_destroy();

    header("Location: /prevent_care/index.php");
    exit();
?>