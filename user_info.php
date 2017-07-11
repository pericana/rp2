<div id="user_info">
    <a href="myProfile.php"><?php echo getSessionUser()->name; ?></a>
    <div style="height: 40px; width: 1px; float: left; background: white"></div>
    <a href="index.php">Naslovna</a>
    <a href="playQuiz.php">Igraj kviz</a>
    <?php if(getSessionUser()->userType == 1){ ?><a href="addQuestion.php">Dodaj pitanje</a> <?php } ?>
    <?php if(getSessionUser()->userType == 1){ ?><a href="addCategory.php">Kategorije</a> <?php } ?>
    <?php if(getSessionUser()->userType == 1){ ?><a href="users.php">Korisnici</a> <?php } ?>
    <a style="float: right; margin-right: 30px;" href="logout.php">Odjavi se</a>
</div>