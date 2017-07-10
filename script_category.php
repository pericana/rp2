<?php

require_once 'classes/Category.php';
require_once 'database_connect.php';
require_once 'database_category.php';

if(isset($_POST['action']) && $_POST['action'] === "add"){

    $categoryName = $_POST['name'];
    $categories = addCategory($categoryName);

    echo json_encode( $categories );
    flush();

}else if(isset($_POST['action']) && $_POST['action'] === "delete"){

    $id = $_POST['id'];
    $categories = deleteCategory($id);

    echo json_encode( $categories );
    flush();

}else if(isset($_GET['action']) && $_GET['action'] === "get"){

    $categories = getCategories();

    echo json_encode( $categories );
    flush();

}

?>