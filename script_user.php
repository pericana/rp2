<?php

require_once 'database_connect.php';
require_once 'database_user.php';

if(isset($_POST['action']) && $_POST['action'] === "updateScore"){

    $categoryName = $_POST['cat_name'];
    $userId = $_POST['user_id'];
    $score = $_POST['score'];

    updateBestResult($userId, $score, $categoryName);

    echo json_encode( "Success" );
    flush();

}else if(isset($_GET['action']) && $_GET['action'] === "get"){

    $users = getUsers();

    echo json_encode( $users );
    flush();

}else if(isset($_POST['action']) && $_POST['action'] === "makeAdmin"){

    $userId = $_POST['user_id'];

    setUserAdmin($userId);
    $users = getUsers();

    echo json_encode( $users );
    flush();

}

?>