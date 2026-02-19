<?php
    session_start();
    session_destroy();

    header("Location: /prevent-care/index.php");
    exit();
?>