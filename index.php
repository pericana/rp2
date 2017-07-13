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
        <p style="text-align: center; width: 100%; margin-top: 20px;"> Kviz se sastoji od 10 pitanja, odabrano po kateogrijama. Pitanja su tekstualna i/ili slikovna, a na svako je pitanje samo jedan odgovor točan. Za sve koji žele znati više, pitanja sadrže i objašnjenja, a nakon svakog pitanja možete vidjeti dobiveni broj bodova. Pamti se Vaš najbolji rezultat, i u kojoj je kategoriji rezultat postignut. Sretno!! 🙂
 </p>


    </div>

</div>
