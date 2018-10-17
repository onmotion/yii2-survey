<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 09:51
 */


use onmotion\survey\models\SurveyType;
use kartik\editable\Editable;
use kartik\helpers\Html;
use kartik\select2\Select2;

use vova07\imperavi\Widget;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $question \onmotion\survey\models\SurveyQuestion */
/* @var $number integer */


echo Html::beginTag('div', ['class' => 'survey-block', 'id' => 'survey-question-' . $question->survey_question_id]);

echo Html::beginTag('div', ['class' => 'survey-question-name-wrap']);
echo ($number + 1) . '. ' . $question->survey_question_name;
echo Html::endTag('div');


echo Html::beginTag('div', ['class' => 'survey-question-descr-wrap']);
if ($question->survey_question_show_descr) {
    echo $question->survey_question_descr;
}
echo Html::endTag('div');


echo Html::beginTag('div', ['class' => 'answers-container', 'id' => 'survey-answers-' . $question->survey_question_id]);
if (isset($question->survey_question_type)) {
    echo $this->render('/answers/view/_form', ['question' => $question]);
}

echo Html::endTag('div');

echo Html::tag('hr', '');

?>
    <div class="preloader">
        <div class="cssload-spin-box"></div>
    </div>
<?php

echo Html::endTag('div');


