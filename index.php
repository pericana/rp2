<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php");
    }



?>

<?php
    $title = "Naslovna";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "user_info.php";
    ?>

    <?php
        require_once "sidebar.php";
    ?>

    <div id="main">

        <p> Dobro došli </p>

    </div>

</div>