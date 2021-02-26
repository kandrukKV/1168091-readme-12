<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:' . 'index.php');
    exit();
}

include_once ('helpers.php');
include_once ('functions.php');

if (isset($_GET['post_id']) && !empty($_GET['post_id'])) {
    $con = connect_to_database();

    
}





$user_id = $_SESSION['user_id'];



