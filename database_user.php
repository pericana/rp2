<?php
    require_once ('classes/User.php');
    require_once ('user_session.php');

function registration($user, $password) {

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM korisnici WHERE user=:user OR email=:email");
        $statement->bindParam(':user', $user->name, PDO::PARAM_STR);
        $statement->bindParam(':email', $user->email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchObject();
        if ($result) {
            if($result->name == $user->name){
                return "Korisničko ime je zauzeto, probajte sa nekim drugim.";
            }else{
                return "Email je zauzet, probajte sa nekim drugim.";
            }
        }else{
            $statement = $connection->prepare("INSERT INTO korisnici
                                                (user, pass, email, userType) VALUES
                                                (:user, :pass, :email, :userType)");

            $statement->bindParam(':user', $user->name, PDO::PARAM_STR);
            $statement->bindParam(':pass', $password, PDO::PARAM_STR);
            $statement->bindParam(':email', $user->email, PDO::PARAM_STR);
            $statement->bindParam(':userType', $user->userType, PDO::PARAM_INT);
            $statement->execute();

            $user->id = $connection->lastInsertId();

            setSessionUser($user);
            header("Location: index.php");
        }
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function login($nameOrEmail, $password) {

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM korisnici WHERE user=:user OR email=:email");
        $statement->bindParam(':user', $nameOrEmail, PDO::PARAM_STR);
        $statement->bindParam(':email', $nameOrEmail, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchObject();
        if (!$result) {
            return "Korisničko ime i email ne postoje u bazi.";
        }else if ( $result->pass !==  $password) {
            return "Lozinka netočna " . $result->pass . " vs " . $password;
        }else{
            $user = new User();
            $user->id = $result->id;
            $user->name = $result->user;
            $user->email = $result->email;
            $user->userType = $result->userType;
            $user->bestScore = $result->bestResults;
            $user->bestScoreCategoryName = $result->bestResultsCategory;
            setSessionUser($user);
            header("Location: index.php");
        }
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function updateBestResult($userId, $score, $categoryName) {

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM korisnici WHERE id=:userId");
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchObject();

        $scoreFromDB = $result->bestResults;
        echo "stas";
        if($score > $scoreFromDB){
            echo "stas2 " . $score;
            $statement = $connection->prepare("UPDATE korisnici SET bestResults = :bestResults, bestResultsCategory = :category WHERE id=:userId");
            $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
            $statement->bindParam(':bestResults', $score, PDO::PARAM_INT);
            $statement->bindParam(':category', $categoryName, PDO::PARAM_STR);
            $statement->execute();
        }
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function getUsers() {

    global $connection;

    try {
        $statement = $connection->prepare("SELECT * FROM korisnici WHERE id !=:userId");
        $statement->bindParam(':userId', getSessionUser()->id, PDO::PARAM_INT);
        $statement->execute();

        $resultArray = Array();
        while($item = $statement->fetchObject()){
            $user = new User();
            $user->id = $item->id;
            $user->name = $item->user;
            $user->email = $item->email;
            $user->userType = $item->userType;
            $user->bestScore = $item->bestResults;
            $user->bestScoreCategoryName = $item->bestResultsCategory;

            array_push($resultArray, $user);
        }
        return $resultArray;
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function setUserAdmin($userId) {

    global $connection;

    try {
        $statement = $connection->prepare("UPDATE korisnici SET userType = 1 WHERE id = :userId");
        $statement->bindParam(':userId', $userId,PDO::PARAM_INT);
        $statement->execute();
    }
    catch(PDOException $e) {
        echo $e;
    }
}