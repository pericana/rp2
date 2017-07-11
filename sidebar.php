<div id="sidebar">

    <ul>

        <li><a href="playQuiz.php">Igraj kviz</a></li>
        <?php if(getSessionUser()->userType == 1){ ?><li><a href="addQuestion.php">Dodaj pitanje</a></li> <?php } ?>
        <?php if(getSessionUser()->userType == 1){ ?><li><a href="addCategory.php">Kategorije</a></li> <?php } ?>
        <?php if(getSessionUser()->userType == 1){ ?><li><a href="users.php">Korisnici</a></li> <?php } ?>
        <li><a href="logout.php">Odjavi se</a></li>

    </ul>

</div>