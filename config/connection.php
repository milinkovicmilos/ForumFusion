<?php

include_once("config.php");

try {
    $dbc = new PDO(DBMS . ":host=" . HOST . ";dbname=" . DBNAME . ";charset=utf8", USERNAME, PASSWORD);
    $dbc->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $ex) {
    echo $ex->getMessage();
}