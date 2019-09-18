<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use onmotion\survey\models\SurveyUserAnswer;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

$userAnswers = $question->userAnswers;

$ddList = [
		1 => \Yii::t('survey', 'Available'),
		2 => \Yii::t('survey', 'Unavailable'),
		3 => \Yii::t('survey', 'If needed'),
];

foreach ($question->answers as $i => $answer) {
	$userAnswer = $userAnswers[$answer->survey_answer_id] ?? (new SurveyUserAnswer());

	$label = '<div class="answer-text"><div class="name">' . $answer->survey_answer_name . '</div>';

	if ($answer->survey_answer_show_descr) {
		$label .= '<br>' . $answer->survey_answer_descr;
	}
	$label .= '</div>';

	echo Html::beginTag('div', ['class' => 'answer-item']);

	echo Html::tag('div', $form->field($userAnswer, "[$question->survey_question_id][$answer->survey_answer_id]survey_user_answer_value",[
		'template' => "{input}{label}\n{hint}\n{error}",
	])
		->dropDownList($ddList, ['encode' => false, 'prompt' => \Yii::t('survey', 'Select...')])->label(false));
	echo $label;
	echo Html::endTag('div');

	//   echo Html::tag('br', '');
}