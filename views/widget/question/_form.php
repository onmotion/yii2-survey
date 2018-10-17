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

Pjax::begin([
    'id' => 'survey-questions-pjax-' . $question->survey_question_id,
    'enablePushState' => false,
    'timeout' => 0,
    'scrollTo' => false,
    'options' => ['class' => 'survey-question-pjax-container'],
    'clientOptions' => [
        'type' => 'post',
        'skipOuterContainers' => true,
       // 'async' => false,
    ]
]);

$form = ActiveForm::begin([
    'id' => 'survey-questions-form-' . $question->survey_question_id,
    'action' => Url::toRoute(['/survey/question/submit-answer', 'id' => $question->survey_question_id, 'n' => $number]),
    'validationUrl' => Url::toRoute(['/survey/question/validate', 'id' => $question->survey_question_id]),
    'options' => ['class' => 'form-inline question-form', 'data-pjax' => true],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validateOnBlur' => false,
    'validateOnSubmit' => true,
    'fieldConfig' => [
        'template' => "<div class='survey-questions-form-field'>{label}{input}\n{error}</div>",
        'labelOptions' => ['class' => ''],
    ],
]);

echo Html::beginTag('div', ['class' => 'survey-block', 'id' => 'survey-question-' . $question->survey_question_id]);

echo Html::beginTag('div', ['class' => 'survey-question-name-wrap']);
echo Html::tag('h4', ($number + 1) . '. ' . $question->survey_question_name);
echo Html::endTag('div');

if ($question->survey_question_show_descr) {
    echo Html::beginTag('div', ['class' => 'survey-question-descr-wrap']);
    echo $question->survey_question_descr;
    echo Html::endTag('div');
}


echo Html::beginTag('div', ['class' => 'answers-container', 'id' => 'survey-answers-' . $question->survey_question_id]);
if (isset($question->survey_question_type)) {
    echo $this->render('@surveyRoot/views/widget/answers/_form', ['question' => $question, 'form' => $form]);
}

echo Html::endTag('div');

echo Html::tag('div', Html::submitButton('DONE', ['class' => 'btn btn-primary btn-submit hidden animated']), ['class' => 'card-footer']);

//echo $form->errorSummary([$question]);

?>
    <div class="preloader">
        <div class="cssload-spin-box"></div>
    </div>
<?php

echo Html::endTag('div');


ActiveForm::end();

Pjax::end();