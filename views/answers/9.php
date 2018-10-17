<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

//$form->field()
foreach ($question->answers as $i => $answer) {

    echo $form->field($answer, "[$question->survey_question_id][$i]survey_answer_name", [
    'template' => "<div class='survey-questions-form-field'><div class='inline-input'>{label}{input}</div>\n{error}</div>",
    ])->input('text')->label(\Yii::t('survey', 'Label') . ' ' . ($i + 1));

    echo Html::submitButton(\Yii::t('survey', '<span class="glyphicon glyphicon-plus"></span>'), ['class' => 'btn btn-success btn-add-answer survey-question-submit',
        'data-action' => Url::toRoute(['question/add-answer', 'id' => $question->survey_question_id, 'after' => $i])]);
    echo Html::submitButton(\Yii::t('survey', '<span class="glyphicon glyphicon-minus"></span>'), ['class' => 'btn btn-danger btn-delete-answer survey-question-submit',
        'data-action' => Url::toRoute(['question/delete-answer', 'id' => $question->survey_question_id, 'answer' => $i]),
        'name' => 'action', 'value' => 'delete-answer'
    ]);

    echo Html::tag('br', '');
}