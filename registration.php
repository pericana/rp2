<?php
    require_once ('user_session.php');
    require_once ('database_connect.php');
    require_once('database_user.php');

    if(isUserLogined() != -1){
        header("Location: index.php");
    }

    $errors = array();

    if(isset($_POST['submit'])){

        if(!isset($_POST['user']) || strlen($_POST['user']) === 0 || strlen($_POST['user']) > 100){
            array_push($errors, "Unesite ime koje ima između 1 i 100 znakova.");
        }
        if(!isset($_POST['email']) || strlen($_POST['email']) === 0 || strlen($_POST['email']) > 100 || !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            array_push($errors, "Unesite ispravan email koji ima između 1 i 100 znakova.");
        }
        if(!isset($_POST['pass']) || !isset($_POST['pass_2']) || strlen($_POST['pass']) < 5 || $_POST['pass'] != $_POST['pass_2']){
            array_push($errors, "Unesite lozinke koje se podudaraju i imaju više od 4 znaka.");
        }
        if(sizeof($errors) == 0){
            $user = new User();

            $user->name = htmlentities($_POST['user']);
            $user->email = htmlentities($_POST['email']);
            $user->userType = 2;
            $password = htmlentities($_POST['pass']);
            $cryptedPassword = sha1($password);

            $returnMessage = registration($user, $cryptedPassword);
            if(strlen($returnMessage) > 0){
                array_push($errors, $returnMessage);
            }

        }

    }

?>

<?php
    $title = "Registacija";
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

            <h1>Registracija</h1>

            <form method="post" action="registration.php">

                <table>

                    <tr>
                        <td><label for="user">Korisnicko ime:</label></td>
                        <td><input name="user" type="text" id="user" placeholder="Korisnicko ime"></td>
                    </tr>

                    <tr>
                        <td><label for="email">Email:</label></td>
                        <td><input name="email" type="text" id="email" placeholder="Email"></td>
                    </tr>

                    <tr>
                        <td><label for="pass">Lozinka:</label></td>
                        <td><input name="pass" type="password" id="pass" placeholder="Lozinka"><br/></td>
                    </tr>

                    <tr>
                        <td><label for="pass_2">Lozinka ponovo:</label></td>
                        <td><input name="pass_2" type="password" id="pass_2" placeholder="Lozinka"><br/></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" value="Registraj me" name="submit"></td>
                    </tr>

                </table>

            </form>

        </div>
    </div>

</div>
