<?php
    require_once 'user_session.php';
    if(isUserLogined() === -1){
        header("Location: login.php");
    }

    require_once 'classes/Question.php';

?>

<?php
    $title = "Igraj kviz";
    require_once "header.php";
?>

<div id="center">

    <?php
        require_once "sidebar.php";
    ?>

    <div id="main">

        <input type="hidden" id="userId" value="<?php echo getSessionUser()->id; ?>"><!-- da u javiscript se moze uzeti id od ulogiranog korisnika-->

        <h1> Kviz </h1>

        <p>Bodovi: <span id="scoreSpan">0</span></p>
        <p>trenutno pitanje <span id="currectQuestionSpan">0</span> od <span id="allQuestionSpan">0</span></p>

        <div id="dinamicContent">

        </div>

    </div>

</div>

<script>

    var categories;
    getCategories();
    var selectedCategoryName;
    var questions = new Array();
    var currentQuestionStep = 1;
    var currentScore = 0;

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
                showCategories();
            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }

    function showCategories() {
        var categoryLabel = $("<label for='category'>Odaberite kategoriju</label>");
        var categorySelect = $("<select id='category' name='category' />");
        var all = $("<option></option>");
        all.val("-1");
        all.html("Sve kategorije");
        categorySelect.append(all);
        for(var i = 0; i < categories.length; i++){
            var option = $("<option></option>");
            option.val(categories[i].id);
            option.html(categories[i].category);
            categorySelect.append(option);
        }
        $("#dinamicContent").append(categoryLabel);
        $("#dinamicContent").append(categorySelect);

        var start = $("<button id='start' class='dinamic'>Kreni</button>");
        $("#dinamicContent").append(start);

        start.on("click", function () {
            startQuiz();
        });
    }

    function startQuiz() {
        var categoryId = $("#category option:selected").val();
        selectedCategoryName = $("#category option:selected").html();

        $.ajax({
            url : "script_question.php",

            data :
                {
                    action : "get",
                    cat_id: categoryId
                },

            type: "GET",

            dataType : "json",

            success : function(data)
            {
                for(var i = 0; i < data.length; i++){
                    console.log("QUESTION: " + data[i]['question']);
                }
                var max = 10;
                if(data.length < 10){
                    max = data.length;
                }

                questions = new Array();
                for(var i = 0; i < max; i++){
                    var random = Math.floor(Math.random() * data.length);
                    console.log("RANDOM (0 i " + data.length + "): " + random);
                    questions.push(data[random]);
                    data.splice(random, 1);
                }

                for(var i = 0; i < questions.length; i++){
                    console.log("QUESTION: " + questions[i]['question']);
                }

                showQuestion();

            },
            error : function (xhr, status, errorThrown){
                alert("greska");
            }

        });

    }

    function showQuestion(){
        $("#dinamicContent").empty();
        var currentQuestion = questions[currentQuestionStep - 1];
        $("#currectQuestionSpan").html(currentQuestionStep);
        $("#allQuestionSpan").html(questions.length);

        var questionParagraph = $("<p></p>");
        questionParagraph.html(currentQuestion['question']);

        $("#dinamicContent").append(questionParagraph);

        if(currentQuestion['questionType'] == 3){
            var img = $("<img />");
            img.attr("src", currentQuestion['imageForQuestion']);
            img.css("width", "50%");
            $("#dinamicContent").append(img);
        }

        //show answer form
        if(currentQuestion['questionType'] == 1 || currentQuestion['questionType'] == 3){
            var answer = $("<input />");
            answer.attr("type", "text");
            answer.attr("id", "answer");
            answer.attr("placeholder", "Unesite odgovor");
            $("#dinamicContent").append("<br>");
            $("#dinamicContent").append(answer);
        }else if(currentQuestion['questionType'] == 2){
            var answers = currentQuestion['answers'];
            for(var i = 0; i < answers.length; i++){
                var answer = $("<input />");
                answer.attr("type", "radio");
                answer.attr("name", "answer");
                answer.attr("class", "answer");
                answer.attr("value", answers[i]['isCorrect']);
                $("#dinamicContent").append(answer);
                $("#dinamicContent").append(answers[i]['textAnswer'] + "<br/>");
            }
        }else if(currentQuestion['questionType'] == 4){
            var answer = $("<input />");
            answer.attr("type", "radio");
            answer.attr("name", "answer");
            answer.attr("class", "answer");
            answer.attr("value", "true");
            $("#dinamicContent").append(answer);
            $("#dinamicContent").append("Točno <br/>");

            var answer2 = $("<input />");
            answer2.attr("type", "radio");
            answer2.attr("name", "answer");
            answer2.attr("class", "answer");
            answer2.attr("value", "false");
            $("#dinamicContent").append(answer2);
            $("#dinamicContent").append("Netočno <br/>");
        }

        //answer question button
        var answerQuestion = $("<button></button>");
        answerQuestion.html("Odgovori");
        answerQuestion.attr("id", "answerButton");
        $("#dinamicContent").append("<br>");
        $("#dinamicContent").append(answerQuestion);

        answerQuestion.on("click", function () {
            checkAnswer(currentQuestion);
        });

    }

    function checkAnswer(currentQuestion) {
        //next question
        $("#answerButton").remove();
        $("#answer").attr("disabled", true);
        $(".answer").attr("disabled", true);

        if(currentQuestion['questionType'] == 1 || currentQuestion['questionType'] == 3) {
            var answerText = $("#answer").val();

            if(answerText.toLowerCase() === currentQuestion['correctAnswer'].toLowerCase()){
                $("#answer").css("background-color", "green");
                currentScore += parseInt(currentQuestion['questionScore']);
            }else{
                $("#answer").css("background-color", "red");
                var correctAnswerIs = $("<p></p>");
                correctAnswerIs.html("Točan odgovor je: " + currentQuestion['correctAnswer']);
                $("#dinamicContent").append(correctAnswerIs);
            }
        }else if(currentQuestion['questionType'] == 2) {
            var isCorrect = $('input[name=answer]:checked').val();
            console.log("IS CORRECT: " + isCorrect);
            if(isCorrect == 1){
                currentScore += parseInt(currentQuestion['questionScore']);
            }else{

                var answers = currentQuestion['answers'];
                var correctAnswerIs = $("<p></p>");
                var correctItem = "";
                for(var i = 0; i < answers.length; i++){
                    if(answers[i]['isCorrect'] == 1){
                        correctItem = answers[i]['textAnswer'];
                        break;
                    }
                }
                correctAnswerIs.html("Točan odgovor je: " + correctItem);
                $("#dinamicContent").append(correctAnswerIs);
            }
        }else if(currentQuestion['questionType'] == 4) {
            var answer = $('input[name=answer]:checked').val();
            if(answer == currentQuestion['correctAnswer']){
                currentScore += parseInt(currentQuestion['questionScore']);
            }else{

                var correctAnswerIs = $("<p></p>");
                if(currentQuestion['correctAnswer'] == "true"){
                    correctAnswerIs.html("Tvrdnja je točna.");
                }else{
                    correctAnswerIs.html("Tvrdnja nije točna.");
                }
                $("#dinamicContent").append(correctAnswerIs);
            }
        }

        $("#scoreSpan").html(currentScore);

        var explanationParagraph = $("<p></p>");
        explanationParagraph.html(currentQuestion['questionExplanation']);
        $("#dinamicContent").append(explanationParagraph);

        var nextQuestion = $("<button></button>");
        if((currentQuestionStep) === questions.length){
            nextQuestion.html("Rezultat");
        }else{
            nextQuestion.html("Sljedeće pitanje");
        }
        $("#dinamicContent").append(nextQuestion);

        nextQuestion.on("click", function () {
            if((currentQuestionStep) === questions.length){
                setResults();
            }else{
                currentQuestionStep++;
                showQuestion();
            }
        });
    }

    function setResults() {
        $("#dinamicContent").empty();

        var yourScore = $("<p></p>");
        yourScore.html("Vaš rezultat je " + currentScore + " bodova");
        $("#dinamicContent").append(yourScore);

        var aHrefBack = $("<a>Gotovo</a>");
        aHrefBack.attr("href", "index.php");
        $("#dinamicContent").append(aHrefBack);

        console.log("userID: " +$("#userId").val());
        console.log("cat_name: " +selectedCategoryName);
        console.log("score: " +currentScore);



        $.ajax({
            url : "script_user.php",

            data :
                {
                    action : "updateScore",
                    user_id: $("#userId").val(),
                    cat_name: selectedCategoryName,
                    score: currentScore
                },

            type: "POST",

            dataType : "json"

        });
    }

</script>
