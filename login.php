<?php
    require_once ('user_session.php');
    require_once ('database_connect.php');
    require_once('database_user.php');

    if(isUserLogined() != -1){
        header("Location: index.php");
    }

    $errors = array();

    if(isset($_POST['submit'])){

        if(!isset($_POST['user']) || strlen($_POST['user']) < 1){
            array_push($errors, "Unesite ime");
        }
        if(!isset($_POST['pass']) || strlen($_POST['pass']) < 1 ){
            array_push($errors, "Unesite lozinku");
        }
        if(sizeof($errors) == 0){

            $userNameOrEmail = htmlentities($_POST['user']);
            $password = htmlentities($_POST['pass']);
            $cryptedPassword = sha1($password);

            $returnMessage = login($userNameOrEmail, $cryptedPassword);
            if(strlen($returnMessage) > 0){
                array_push($errors, $returnMessage);
            }

        }

    }

?>

<?php
    $title = "Prijava";
    require_once "header.php";
?>

    <div id="error" >
        <?php
        if(sizeof($errors) != 0){
            foreach ($errors as $error){
                echo $error . "</br>";
            }
        }
        ?>
    </div>
    <div id="center">
        <div id="login">

            <h1>Prijava</h1>

            <form method="post" action="login.php">
                <table>

                    <tr>
                        <td><label for="user">Korisnicko ime ili email:</label></td>
                        <td><input name="user" type="text" id="user" placeholder="Korisnicko ime ili email"></td>
                    </tr>

                    <tr>
                        <td><label for="pass">Lozinka:</label></td>
                        <td><input name="pass" type="password" id="pass" placeholder="Lozinka"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" value="Prijava" name="submit"></td>
                    </tr>

                </table>
            </form>

            <a href="registration.php" style="margin-left: 158px" >Ako nemate korisnicki racun, stvorite novi ovdje.</a>

        </div>

    </div>
</div>
