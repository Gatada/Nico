<?php

@include_once('config.php');

$link = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['name']);

if(mysqli_connect_error()){
	die("Database connection error.");
}

?>