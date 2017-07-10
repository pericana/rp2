<?php

require_once 'database_connect.php';
require_once 'database_question.php';

if(isset($_GET['action']) && $_GET['action'] === "get"){

    $categoryId = $_GET['cat_id'];

    $questions = getQuestionByCategoryId($categoryId);

    echo json_encode( $questions );
    flush();

}

?>