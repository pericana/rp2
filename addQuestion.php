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
        require_once "user_info.php";
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

        <h3> Dodajte pitanje </h3>

        <form method="post" action="addQuestion.php" enctype="multipart/form-data">
            <label for="questionType">Odaberite tip pitanja:</label>
            <select name="questionType" id="questionType" style="margin-left: 117px; width: 300px">
                <option disabled selected value> -- izaberite opciju -- </option>
                <option value="1">Samo pitanje</option>
                <option value="2">Pitanje sa ponuđenim odgovorima</option>
                <option value="3">Pitanje sa slikom</option>
                <option value="4">Tocno - netocno</option>
            </select>

            <table id="questionAreaTable">

            </table>
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
        $("#questionAreaTable").empty();

        var trQuestion = $("<tr></tr>");
        var tdQquestionTekstLabel = $("<td><p>Unesite pitanje</p></td>");
        var tdQuestionTekst = $("<td><textarea rows='5' colls='20' name='questionText' id='questionText' placeholder='Upišite pitanje'></textarea></td>");
        trQuestion.append(tdQquestionTekstLabel);
        trQuestion.append(tdQuestionTekst);

        var trScore = $("<tr></tr>");
        var tdScoreLabel = $("<td><p>Unesite broj bodova (10 - 100)</p></td>");
        var tdScore = $("<td><input type='text' id='score' name='score' placeholder='Upišite broj bodova (10 - 100)' /></td>");
        trScore.append(tdScoreLabel);
        trScore.append(tdScore);

        var trCategory = $("<tr></tr>");
        var tdCategoryLabel = $("<td><p>Odaberite kategoriju</p></td>");
        var tdCategorySelect = $("<td></td>");
        var categorySelect = $("<select id='category' name='category' />");
        tdCategorySelect.append(categorySelect);
        for(var i = 0; i < categories.length; i++){
            var option = $("<option></option>");
            option.val(categories[i].id);
            option.html(categories[i].category);
            categorySelect.append(option);
        }
        trCategory.append(tdCategoryLabel);
        trCategory.append(tdCategorySelect);

        $("#questionAreaTable").append(trQuestion);
        $("#questionAreaTable").append(trScore);
        $("#questionAreaTable").append(trCategory);

        if($("#questionType option:selected").val() === "1" ){
            var trCorectAnswer = $("<tr></tr>");
            var tdCorrectAnswerLabel = $("<td><p>Unesite točan odgovor</p></td>");
            var tdCorrectAnswer = $("<td><input type='text' id='correctAnswer' name='correctAnswer' placeholder='Upišite točan odgovor' /></td>");
            trCorectAnswer.append(tdCorrectAnswerLabel);
            trCorectAnswer.append(tdCorrectAnswer);
            $("#questionAreaTable").append(trCorectAnswer);
        }else if($("#questionType option:selected").val() === "2"){

            var trAnswer1 = $("<tr></tr>");
            var trAnswer2 = $("<tr></tr>");
            var trAnswer3 = $("<tr></tr>");
            var trAnswer4 = $("<tr></tr>");

            var tdAnswer1Label = $("<p><p>Unesite prvi ponuđeni odgovor</p></p>");
            var tdAnswer2Label = $("<td><p>Unesite drugi ponuđeni odgovor</p></td>");
            var tdAnswer3Label = $("<td><p>Unesite treći ponuđeni odgovor</p></td>");
            var tdAnswer4Label = $("<td><p>Unesite četvrti ponuđeni odgovor</p></td>");

            var tdAnswer1 = $("<td><input style='width: 190px; float: left' type='text' id='answer1' name='answer1' placeholder='Upišite prvi odgovor' /</td>");
            var tdAnswer2 = $("<td><input style='width: 190px; float: left' type='text' id='answer2' name='answer2' style='float: left' placeholder='Upišite drugi odgovor' /></td>");
            var tdAnswer3 = $("<td><input style='width: 190px; float: left' type='text' id='answer3' name='answer3' style='float: left' placeholder='Upišite treći odgovor' /></td>");
            var tdAnswer4 = $("<td><input style='width: 190px; float: left' type='text' id='answer4' name='answer4' style='float: left' placeholder='Upišite četvrti odgovor' /></td>");

            var tocan1 = $("<p style='margin-left:20px; float: left; width: 50px; line-height: 35px; vertical-align: middle'>Točan?</p>");
            var tocan2 = $("<p style='margin-left:20px; float: left; width: 50px; line-height: 35px; vertical-align: middle'>Točan?</p>");
            var tocan3 = $("<p style='margin-left:20px; float: left; width: 50px; line-height: 35px; vertical-align: middle'>Točan?</p>");
            var tocan4 = $("<p style='margin-left:20px; float: left; width: 50px; line-height: 35px; vertical-align: middle'>Točan?</p>");

            var radio1 = $("<input style='width: 20px; float: left' type='radio' id='answer1_radio' name='answer_radio' value='1' />");
            var radio2 = $("<input style='width: 20px; float: left' type='radio' id='answer2_radio' name='answer_radio' value='2' />");
            var radio3 = $("<input style='width: 20px; float: left' type='radio' id='answer3_radio' name='answer_radio' value='3' />");
            var radio4 = $("<input style='width: 20px; float: left' type='radio' id='answer4_radio' name='answer_radio' value='4' />");

            tdAnswer1.append(tocan1);
            tdAnswer1.append(radio1);
            tdAnswer2.append(tocan2);
            tdAnswer2.append(radio2);
            tdAnswer3.append(tocan3);
            tdAnswer3.append(radio3);
            tdAnswer4.append(tocan4);
            tdAnswer4.append(radio4);

            trAnswer1.append(tdAnswer1Label);
            trAnswer1.append(tdAnswer1);
            trAnswer2.append(tdAnswer2Label);
            trAnswer2.append(tdAnswer2);
            trAnswer3.append(tdAnswer3Label);
            trAnswer3.append(tdAnswer3);
            trAnswer4.append(tdAnswer4Label);
            trAnswer4.append(tdAnswer4);

            $("#questionAreaTable").append(trAnswer1);
            $("#questionAreaTable").append(trAnswer2);
            $("#questionAreaTable").append(trAnswer3);
            $("#questionAreaTable").append(trAnswer4);

        }else if($("#questionType option:selected").val() === "4"){

            var trTocno = $("<tr></tr>");

            var tdCorrectLabel = $("<td><p>Je li trvdnja točna</p></td>");
            var tdCorrectSelect = $("<td></td>");
            var correctSelect = $("<select id='correctSelect' name='correctSelect' />");
            var optionCorrect = $("<option></option>");
            optionCorrect.val("1");
            optionCorrect.html("Točno");
            correctSelect.append(optionCorrect);
            var optionIncorrect = $("<option></option>");
            optionIncorrect.val("0");
            optionIncorrect.html("Netočno");
            correctSelect.append(optionIncorrect);
            tdCorrectSelect.append(correctSelect);

            trTocno.append(tdCorrectLabel);
            trTocno.append(tdCorrectSelect);

            $("#questionAreaTable").append(trTocno);
        }else{
            var trCorectAnswer = $("<tr></tr>");
            var tdCorrectAnswerLabel = $("<td><p>Unesite točan odgovor</p></td>");
            var tdCorrectAnswer = $("<td><input type='text' id='correctAnswer' name='correctAnswer' placeholder='Upišite točan odgovor' /></td>");
            trCorectAnswer.append(tdCorrectAnswerLabel);
            trCorectAnswer.append(tdCorrectAnswer);

            var trImage = $("<tr></tr>");
            var tdImageLabel = $("<td><p>Odaberite sliku (ne veću od 1MB)</p></td>");
            var tdImage = $("<td><input type='file' id='imageId' name='imageId' accept='image/*' /></td>");
            trImage.append(tdImageLabel);
            trImage.append(tdImage);

            $("#questionAreaTable").append(trCorectAnswer);
            $("#questionAreaTable").append(trImage);

        }

        var trExplanation = $("<tr></tr>");
        var tdExplanationLabel = $("<td><p>Unesite objašnjenje</p></td>");
        var tdExplanation = $("<td><textarea rows='5' colls='20' name='questionExplanation' id='questionExplanation' placeholder='Unesite objašnjenje'></textarea></td>");
        trExplanation.append(tdExplanationLabel);
        trExplanation.append(tdExplanation);

        $("#questionAreaTable").append(trExplanation);

        var trSubmit = $("<tr></tr>");
        var tdSubmit = $("<td><input type='submit' id='submit' name='submit' value='Dodaj pitanje' /></td>");
        trSubmit.append($("<td></td>"));
        trSubmit.append(tdSubmit);
        $("#questionAreaTable").append(trSubmit);

    });
</script>
