<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use onmotion\survey\models\SurveyUserAnswer;
use yii\helpers\Html;

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

$userAnswers = $question->userAnswers;
$userAnswer = !empty(current($userAnswers)) ? current($userAnswers) : (new SurveyUserAnswer()) ;
$ddList = [];

foreach ($question->answers as $i => $answer) {
    $ddList[$answer->survey_answer_id] = $answer->survey_answer_name;
}

echo $form->field($userAnswer, "[$question->survey_question_id]survey_user_answer_value")->dropDownList($ddList,
    ['encode' => false, 'prompt' => \Yii::t('survey', 'Select...')])->label(false);
echo Html::tag('div', '', ['class' => 'clearfix']);