<?php

function connect()
{
    $config = include('config/config.inc.php');

    $hostName = 'localhost';
    $username = $config->username;
    $password = $config->password;
    $databaseName = $config->databaseName;
    $mysqli = new mysqli($hostName, $username, $password, $databaseName);
    if ($mysqli->connect_errno) {
        die("Couldn't connect to MySQL");
    }
    return $mysqli;
}