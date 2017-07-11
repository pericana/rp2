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
        require_once "user_info.php";
    ?>

    <div id="main">

        <input type="hidden" id="userId" value="<?php echo getSessionUser()->id; ?>"><!-- da u javiscript se moze uzeti id od ulogiranog korisnika-->

        <h3> Kviz </h3>

        <div style="margin-bottom: 50px">
            <p style="float: left">Bodovi: <span id="scoreSpan">0</span></p>
            <p style="float: right">trenutno pitanje <span id="currectQuestionSpan">0</span> od <span id="allQuestionSpan">0</span></p>
        </div>
        <br />


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
        start.css("margin-left", "20px");
        start.css("width", "80px");
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

        var selectedButtonForType2;

        var questionParagraph = $("<p id='questionParagraph'></p>");
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
            //var button1 = $("<button></button>");
            //button1.html(answers[i]['textAnswer'])
            for(var i = 0; i < answers.length; i++){

                var button = $("<button></button>");
                button.html(answers[i]['textAnswer']);
                button.val(answers[i]['isCorrect']);
                if(i % 2 == 0){
                    button.css("margin-left", "0px");
                }else{
                    button.css("margin-right", "0px");
                }
                button.attr("class", "buttonSelect");
                $("#dinamicContent").append(button);

                button.on("click", function () {
                    $(".buttonSelect").css("background-color", "");
                    $(this).css("background-color", "gray");
                    selectedButtonForType2 = $(this);
                });
            }
        }else if(currentQuestion['questionType'] == 4){

            var buttonTocno = $("<button></button>");
            buttonTocno.html("Točno");
            buttonTocno.val("true");
            buttonTocno.css("margin-left", "0px");
            buttonTocno.attr("class", "buttonSelect");
            $("#dinamicContent").append(buttonTocno);

            buttonTocno.on("click", function () {
                $(".buttonSelect").css("background-color", "");
                $(this).css("background-color", "gray");
                selectedButtonForType2 = $(this);
            });

            var buttonNetocno = $("<button></button>");
            buttonNetocno.html("Netočno");
            buttonNetocno.val("false");
            buttonNetocno.css("margin-right", "0px");
            buttonNetocno.attr("class", "buttonSelect");
            $("#dinamicContent").append(buttonNetocno);

            buttonNetocno.on("click", function () {
                $(".buttonSelect").css("background-color", "");
                $(this).css("background-color", "gray");
                selectedButtonForType2 = $(this);
            });

        }

        //answer question button
        var answerQuestion = $("<button></button>");
        answerQuestion.html("Odgovori");
        answerQuestion.attr("id", "answerButton");
        $("#dinamicContent").append("<br>");
        $("#dinamicContent").append(answerQuestion);

        answerQuestion.on("click", function () {
            checkAnswer(currentQuestion, selectedButtonForType2);
        });

    }

    function checkAnswer(currentQuestion, selectedButtonForType2) {
        //next question
        $("#answerButton").remove();
        $("#answer").attr("disabled", true);
        $(".answer").attr("disabled", true);
        $(".buttonSelect").attr("disabled", true);

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
            var isCorrect;
            if(selectedButtonForType2 == null){
                isCorrect = 0;
            }else{
                isCorrect = selectedButtonForType2.val();
            }
            if(isCorrect == 1){
                currentScore += parseInt(currentQuestion['questionScore']);
                selectedButtonForType2.css("background-color", "green");
            }else{
                if(selectedButtonForType2 != null) selectedButtonForType2.css("background-color", "red");
                var buttons = $("button.buttonSelect");
                for(var i = 0; i < buttons.length; i++){
                    if(buttons.eq(i).val() == 1){
                        buttons.eq(i).css("background-color", "green");
                    }
                }
            }
        }else if(currentQuestion['questionType'] == 4) {
            var answer;
            if(selectedButtonForType2 == null){
                answer = "";
            }else{
                answer = selectedButtonForType2.val();
            }
            if(answer == currentQuestion['correctAnswer']){
                currentScore += parseInt(currentQuestion['questionScore']);
                selectedButtonForType2.css("background-color", "green");
            }else{
                if(selectedButtonForType2 != null) selectedButtonForType2.css("background-color", "red");
                var buttons = $("button.buttonSelect");
                for(var i = 0; i < buttons.length; i++){
                    if(buttons.eq(i).val() == currentQuestion['correctAnswer']){
                        buttons.eq(i).css("background-color", "green");
                    }
                }
            }
        }

        $("#scoreSpan").html(currentScore);

        var explanationH3 = $("<h3 style='margin-top: 20px'>Objašnjenje:</h3>");
        var explanationParagraph = $("<p id='explanation'></p>");
        explanationParagraph.html(currentQuestion['questionExplanation']);
        $("#dinamicContent").append(explanationH3);
        $("#dinamicContent").append(explanationParagraph);

        var nextQuestion = $("<button></button>");
        nextQuestion.attr("id", "nextQuestion");
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
