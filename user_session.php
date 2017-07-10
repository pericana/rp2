<?php
require_once ('classes/User.php');

session_start();

function setSessionUser ($user) {
    $_SESSION["user"] = $user;
}

function getSessionUser () {
    return $_SESSION["user"];
}

function isUserLogined(){
    if(!isset($_SESSION['user'])){
        return -1;
    }
    $user = $_SESSION["user"];
    if($user){
        return $user->userType;
    }
    return -1;
}

?>