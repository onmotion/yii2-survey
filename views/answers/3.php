<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \common\modules\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

//$form->field()
foreach ($question->answers as $i => $answer) {

    echo $form->field($answer, "[$question->survey_question_id][$i]survey_answer_name")->input('text',
        ['placeholder' => \Yii::t('survey', 'Enter an answer choice')])->label(false);

    if ($question->survey_question_is_scorable) {
        echo Html::beginTag('div', ['class' => 'points-wrap']);
        if ($i === 0) {
            echo Html::tag('span', 'Баллы', ['class' => 'points-title']);
        }
        echo $form->field($answer, "[$question->survey_question_id][$i]survey_answer_points")->input('number')->label(false);
        echo Html::endTag('div');
    }

    echo Html::submitButton(\Yii::t('survey', '<i class="fa fa-plus" aria-hidden="true"></i>'), ['class' => 'btn btn-success btn-add-answer survey-question-submit',
        'data-action' => Url::toRoute(['/survey/question/add-answer', 'id' => $question->survey_question_id, 'after' => $i])]);
    echo Html::submitButton(\Yii::t('survey', '<i class="fa fa-minus" aria-hidden="true"></i>'), ['class' => 'btn btn-danger btn-delete-answer survey-question-submit',
        'data-action' => Url::toRoute(['/survey/question/delete-answer', 'id' => $question->survey_question_id, 'answer' => $i]),
        'name' => 'action', 'value' => 'delete-answer'
    ]);

    echo Html::tag('br', '');
}