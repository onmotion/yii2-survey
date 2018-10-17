<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 12/10/2017
 * Time: 14:09
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $question \onmotion\survey\models\SurveyQuestion */


Pjax::begin([
    'id' => 'survey-questions-pjax-' . $question->survey_question_id,
    'enablePushState' => false,
    'timeout' => 0,
    'scrollTo' => false,
    'clientOptions' => [
        'type' => 'post',
        'skipOuterContainers' => true,
    ]
]);

echo Html::beginTag('div', ['class' => 'survey-block', 'id' => 'survey-question-' . $question->survey_question_id]);
echo Html::beginTag('div', ['class' => 'survey-question-view-wrap']);

echo Html::tag('h4', $question->survey_question_name);

echo Html::a(\Yii::t('survey', '<span class="glyphicon glyphicon-pencil"></span>'), Url::toRoute(['question/edit', 'id' => $question->survey_question_id]), [
    'class' => 'btn btn-info pull-right btn-edit',
]);
echo Html::endTag('div');
echo Html::endTag('div');

Pjax::end();