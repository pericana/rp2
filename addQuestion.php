<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php");
    }

    if(isUserLogined() != 1){
        header("Location: index.php");
    }

    require_once 'classes/Question.php';
    require_once 'database_connect.php';
    require_once 'database_question.php';

    $errors = array();

    if(isset($_POST['submit'])){

        if(!isset($_POST['questionType'])){
            array_push($errors, "Odaberite tip pitanja.");
        }
        if(!isset($_POST['questionText']) || strlen($_POST['questionText']) === 0){
            array_push($errors, "Unesite pitanje.");
        }
        if(!isset($_POST['questionExplanation']) || strlen($_POST['questionExplanation']) === 0){
            array_push($errors, "Unesite objašnjenje.");
        }
        if(!isset($_POST['score']) || intval($_POST['score']) < 10 || intval($_POST['score']) > 100){
            array_push($errors, "Unesite broj bodova između 10 i 100.");
        }

        $questionType = $_POST['questionType'];
        $fullPath = "";

        if($questionType === "2"){
            if(!isset($_POST['answer1']) || strlen($_POST['answer1']) === 0
                || !isset($_POST['answer2']) || strlen($_POST['answer2']) === 0
                || !isset($_POST['answer3']) || strlen($_POST['answer3']) === 0
                || !isset($_POST['answer4']) || strlen($_POST['answer4']) === 0){
                array_push($errors, "Unesite sve četiri opcije odgovora.");
            }
            if(!isset($_POST['answer_radio']) || intval($_POST['answer_radio']) < 1 || intval($_POST['answer_radio']) > 4){
                array_push($errors, "Odaberite točan odgovor.");
            }
        }else if($questionType === "1"){
            if(!isset($_POST['correctAnswer']) || strlen($_POST['correctAnswer']) === 0){
                array_push($errors, "Unesite točan odgovor.");
            }
        }else if($questionType === "3"){
            if(!isset($_POST['correctAnswer']) || strlen($_POST['correctAnswer']) === 0){
                array_push($errors, "Unesite točan odgovor.");
            }
            if(!isset($_FILES["imageId"]['name']) || strlen($_FILES["imageId"]['name']) < 1){
                array_push($errors, "Odaberite sliku.");
            }
            $path = "slikeZaPitanja/";
            $imageName = basename($_FILES["imageId"]["name"]);
            $imageFileType = pathinfo($imageName,PATHINFO_EXTENSION);
            $randomKey = rand(0, 1000);
            $fullPath = $path  . round(microtime(true) * 1000) . "_" . $randomKey . "." . $imageFileType;

            if ($_FILES["imageId"]["size"] > 1000000  ) {
                array_push($errors, "Slika je veca od 1MB.");
            }else if (move_uploaded_file($_FILES["imageId"]["tmp_name"], $fullPath)) {
                // slika je na serveru
            }
            else {
                array_push($errors, "Nije uspio upload slike");
            }
        }

        if(sizeof($errors) == 0){
            $question = new Question();
            $question->question = htmlentities($_POST['questionText']);
            $question->questionType = htmlentities($_POST['questionType']);
            $question->categoryId = htmlentities($_POST['category']);
            if(isset($_POST['correctAnswer'])){
              $question->correctAnswer = htmlentities($_POST['correctAnswer']);
            }else if($questionType === '4'){
              $isCorrect = htmlentities($_POST['correctSelect']);
              if($isCorrect === '1'){
                $question->correctAnswer = "true";
              }else{
                $question->correctAnswer = "false";
              }
            }else{
              $question->correctAnswer = "";
            }
            $question->imageForQuestion = $fullPath;
            $question->questionScore = htmlentities($_POST['score']);
            $question->questionExplanation = htmlentities($_POST['questionExplanation']);

            $answers = array();
            $correct = 0;

            if($question->questionType == '2'){
              $answers[0] = htmlentities($_POST['answer1']);
              $answers[1] = htmlentities($_POST['answer2']);
              $answers[2] = htmlentities($_POST['answer3']);
              $answers[3] = htmlentities($_POST['answer4']);
              $correct = intval(htmlentities($_POST['answer_radio'])) - 1;
            }

            $returnMessage = addQuestion($question, $answers, $correct);
            if(strlen($returnMessage) > 0){
                array_push($errors, $returnMessage);
            }

        }

    }

?>

<?php
    $title = "Dodajte pitanje";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "sidebar.php";
    ?>

    <div id="main">

        <div id="error" >
            <?php
            if(sizeof($errors) != 0){
                foreach ($errors as $error){
                    echo $error . "</br>";
                }
            }
            ?>
        </div>

        <h1> Dodajte pitanje </h1>

        <form method="post" action="addQuestion.php" enctype="multipart/form-data">
            <label for="questionType">Odaberite tip pitanja:</label>
            <select name="questionType" id="questionType">
                <option disabled selected value> -- izaberite opciju -- </option>
                <option value="1">Samo pitanje</option>
                <option value="2">Pitanje sa ponuđenim odgovorima</option>
                <option value="3">Pitanje sa slikom</option>
                <option value="4">Tocno - netocno</option>
            </select>

            <div id="questionArea">

            </div>
        </form>

    </div>

</div>

<script>

    var categories;
    getCategories();

    function getCategories(){

        $.ajax({
            url : "script_category.php",

            data :
                {
                    action : "get"
                },

            type: "GET",

            dataType : "json",

            success : function(data)
            {
                categories = data;
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }

    $("#questionType").change(function () {
        $("#questionArea").empty();
        var questionTekstLabel = $("<label for='questionText'>Unesite pitanje</label>");
        var questionTekst = $("<input type='text' id='questionText' name='questionText' placeholder='Upišite pitanje' />");

        var scoreLabel = $("<label for='score'>Unesite broj bodova (10 - 100)</label>");
        var score = $("<input type='text' id='score' name='score' placeholder='Upišite broj bodova (10 - 100)' />");

        var categoryLabel = $("<label for='category'>Odaberite kategoriju</label>");
        var categorySelect = $("<select id='category' name='category' />");
        for(var i = 0; i < categories.length; i++){
            var option = $("<option></option>");
            option.val(categories[i].id);
            option.html(categories[i].category);
            categorySelect.append(option);
        }

        $("#questionArea").append(categoryLabel);
        $("#questionArea").append(categorySelect);
        $("#questionArea").append($("<br/>"));
        $("#questionArea").append(questionTekstLabel);
        $("#questionArea").append(questionTekst);
        $("#questionArea").append($("<br/>"));
        $("#questionArea").append(scoreLabel);
        $("#questionArea").append(score);

        if($("#questionType option:selected").val() === "1" ){
            var correctAnswerLabel = $("<label for='correctAnswer'>Unesite točan odgovor</label>");
            var correctAnswer = $("<input type='text' id='correctAnswer' name='correctAnswer' placeholder='Upišite točan odgovor' />");
            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(correctAnswerLabel);
            $("#questionArea").append(correctAnswer);
        }else if($("#questionType option:selected").val() === "2"){
            var answer1Label = $("<label for='answer1'>Unesite prvi ponuđeni odgovor</label>");
            var answer2Label = $("<label for='answer2'>Unesite drugi ponuđeni odgovor</label>");
            var answer3Label = $("<label for='answer3'>Unesite treći ponuđeni odgovor</label>");
            var answer4Label = $("<label for='answer4'>Unesite četvrti ponuđeni odgovor</label>");

            var answer1 = $("<input type='text' id='answer1' name='answer1' placeholder='Upišite prvi odgovor' />");
            var answer2 = $("<input type='text' id='answer2' name='answer2' placeholder='Upišite drugi odgovor' />");
            var answer3 = $("<input type='text' id='answer3' name='answer3' placeholder='Upišite treći odgovor' />");
            var answer4 = $("<input type='text' id='answer4' name='answer4' placeholder='Upišite četvrti odgovor' />");

            var radio1 = $("<input type='radio' id='answer1_radio' name='answer_radio' value='1' />");
            var radio2 = $("<input type='radio' id='answer2_radio' name='answer_radio' value='2' />");
            var radio3 = $("<input type='radio' id='answer3_radio' name='answer_radio' value='3' />");
            var radio4 = $("<input type='radio' id='answer4_radio' name='answer_radio' value='4' />");

            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(answer1Label);
            $("#questionArea").append(answer1);
            $("#questionArea").append("Tocan?");
            $("#questionArea").append(radio1);
            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(answer2Label);
            $("#questionArea").append(answer2);
            $("#questionArea").append("Tocan?");
            $("#questionArea").append(radio2);
            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(answer3Label);
            $("#questionArea").append(answer3);
            $("#questionArea").append("Tocan?");
            $("#questionArea").append(radio3);
            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(answer4Label);
            $("#questionArea").append(answer4);
            $("#questionArea").append("Tocan?");
            $("#questionArea").append(radio4);
        }else if($("#questionType option:selected").val() === "4"){
            var correctLabel = $("<label for='correctSelect'>Je li trvdnja točna</label>");
            var correctSelect = $("<select id='correctSelect' name='correctSelect' />");
            var optionCorrect = $("<option></option>");
            optionCorrect.val("1");
            optionCorrect.html("Točno");
            correctSelect.append(optionCorrect);
            var optionIncorrect = $("<option></option>");
            optionIncorrect.val("0");
            optionIncorrect.html("Netočno");
            correctSelect.append(optionIncorrect);

            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(correctLabel);
            $("#questionArea").append(correctSelect);
        }else{
            var correctAnswerLabel = $("<label for='correctAnswer'>Unesite točan odgovor</label>");
            var correctAnswer = $("<input type='text' id='correctAnswer' name='correctAnswer' placeholder='Upišite točan odgovor' />");

            var imageLabel = $("<label for='imageId'>Odaberite sliku (new veću od 1MB)</label>");
            var image = $("<input type='file' id='imageId' name='imageId' accept='image/*' />");

            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(correctAnswerLabel);
            $("#questionArea").append(correctAnswer);
            $("#questionArea").append($("<br/>"));
            $("#questionArea").append(imageLabel);
            $("#questionArea").append(image);

        }

        var explanationLabel = $("<label for='questionExplanation'>Unesite objašnjenje</label>");
        var explanation = $("<textarea rows='5' colls='20' name='questionExplanation' id='questionExplanation'></textarea>");
        $("#questionArea").append($("<br/>"));
        $("#questionArea").append(explanationLabel);
        $("#questionArea").append(explanation);

        $("#questionArea").append($("<br/>"));
        var submit = $("<input type='submit' id='submit' name='submit' value='Dodaj pitanje' />");
        $("#questionArea").append(submit);

    });
</script>
