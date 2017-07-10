<?php
    require_once ('classes/Question.php');
    require_once ('classes/Answer.php');

function addQuestion($question, $answers, $correctAnswer) {

    global $connection;

    try {
        $statement = $connection->prepare("INSERT INTO pitanja
                                                (question, questionType, questionScore, categoryId, imageForQuestion, correctAnswer, questionExplanation) VALUES
                                                (:question, :questionType, :questionScore, :categoryId, :imageForQuestion, :correctAnswer, :questionExplanation)");

        $statement->bindParam(':question', $question->question, PDO::PARAM_STR);
        $statement->bindParam(':questionType', $question->questionType, PDO::PARAM_INT);
        $statement->bindParam(':questionScore', $question->questionScore, PDO::PARAM_INT);
        $statement->bindParam(':categoryId', $question->categoryId, PDO::PARAM_INT);
        $statement->bindParam(':imageForQuestion', $question->imageForQuestion, PDO::PARAM_STR);
        $statement->bindParam(':correctAnswer', $question->correctAnswer, PDO::PARAM_STR);
        $statement->bindParam(':questionExplanation', $question->questionExplanation, PDO::PARAM_STR);

        $statement->execute();

        $questionId = $connection->lastInsertId();

        for($i=0;$i < sizeof($answers); $i++){
          $statement = $connection->prepare("INSERT INTO ponudjeniodgovori
                                                  (questionId, textAnswer, isCorrect) VALUES
                                                  (:questionId, :textAnswer, :isCorrect)");

          $isCorrect = 0;
          if($i === $correctAnswer){
            $isCorrect = 1;
          }
          $statement->bindParam(':questionId', $questionId, PDO::PARAM_INT);
          $statement->bindParam(':textAnswer', $answers[$i], PDO::PARAM_STR);
          $statement->bindParam(':isCorrect', $isCorrect, PDO::PARAM_INT);

          $statement->execute();

        }

        return "";
    }
    catch(PDOException $e) {
        echo $e;
    }
}

function getQuestionByCategoryId($categoryId) {

    global $connection;

    try {

        $statement = "";

        if($categoryId == -1){
            $statement = $connection->prepare("SELECT * FROM pitanja");
        }else{
            $statement = $connection->prepare("SELECT * FROM pitanja WHERE categoryId = :categoryId");
            $statement->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
        }

        $statement->execute();

        $resultArray = Array();
        while($item = $statement->fetchObject()){
            $question = new Question();
            $question->id = $item->id;
            $question->question = $item->question;
            $question->questionType = $item->questionType;
            $question->imageForQuestion = $item->imageForQuestion;
            $question->correctAnswer = $item->correctAnswer;
            $question->questionScore = $item->questionScore;
            $question->questionExplanation = $item->questionExplanation;
            $question->categoryId = $item->categoryId;

            if($question->questionType == 2) {
                $statement2 = $connection->prepare("SELECT * FROM ponudjeniodgovori WHERE questionId = :questionId");
                $statement2->bindParam(':questionId', $question->id, PDO::PARAM_INT);
                $statement2->execute();
                $question->answers = array();
                while ($item2 = $statement2->fetchObject()) {
                    $answer = new Answer();
                    $answer->id = $item2->id;
                    $answer->questionId = $item2->questionId;
                    $answer->textAnswer = $item2->textAnswer;
                    $answer->isCorrect = $item2->isCorrect;
                    array_push($question->answers, $answer);
                }
            }

            array_push($resultArray, $question);
        }
        return $resultArray;

    }
    catch(PDOException $e) {
        echo $e;
    }
}
