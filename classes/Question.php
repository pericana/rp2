<?php

class Question
{
    public $id, $question, $questionType, $categoryId, $imageForQuestion, $correctAnswer, $questionScore, $questionExplanation;

    public $answers; // za tip pitanja 2 - sa ponudjenim odgovorima

}
