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

    <div id="main">

        <p style="text-align: center; width: 100%; font-size: 30px; margin-top: 20px;"> Dobro došli </p>
        <p style="text-align: center; width: 100%; margin-top: 20px;"> Ovo je nekakav opis kviza </p>


    </div>

</div>