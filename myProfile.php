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

        <p id="leftMyProfile">Korisničko ime:</p>
        <p id="rightMyProfile"><?php echo getSessionUser()->name; ?></p>
        <p id="leftMyProfile">Email:</p>
        <p id="rightMyProfile"><?php echo getSessionUser()->email; ?></p>
        <p id="leftMyProfile">Tip korisnika:</p>
        <p id="rightMyProfile"><?php
            if(getSessionUser()->userType == 1){
                echo "Administrator";
            } else{
                echo "Korisnik";
            }
         ?></p>
        <?php if(getSessionUser()->bestScore > 0){ ?>
            <p id="leftMyProfile">Najbolji rezultat:</p>
            <p id="rightMyProfile"><?php echo getSessionUser()->bestScore; ?></p>
            <p id="leftMyProfile">Kategorja najboljeg rezultata:</p>
            <p id="rightMyProfile"><?php echo getSessionUser()->bestScoreCategoryName; ?></p>
        <?php }?>


    </div>

</div>