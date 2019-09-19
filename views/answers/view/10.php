<?php

use onmotion\survey\models\SurveyUserAnswer;
use yii\bootstrap\Progress;
use yii\helpers\Html;


/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

$totalVotesCount = $question->getTotalUserAnswersCount();

?>
<div class="answers-stat">
<table class="table">
	<thead>
	<tr>
		<th></th>
		<th><?php echo \Yii::t('survey', 'Available'); ?></th>
		<th><?php echo \Yii::t('survey', 'Unavailable'); ?></th>
		<th><?php echo \Yii::t('survey', 'If needed'); ?></th>
		<th><?php echo \Yii::t('survey', 'Total'); ?></th>
	</tr>
	</thead>
	<tbody>
<?php
$betterChoice = [
	1 => [
		'value' => '',
		'count' => 0
	],
	3 => [
		'value' => '',
		'count' => 0
	]
];

foreach ($question->answers as $i => $answer) {
	$count = [
		1 => 0,
		2 => 0,
		3 => 0
	];

	foreach ($answer->userAnswers as $userAnswer) {
		$count[$userAnswer->survey_user_answer_value]++;
	}

	if ($betterChoice[1]['count'] < $count[1]) {
		$betterChoice[1]['value'] = $answer->survey_answer_name;
		$betterChoice[1]['count'] = $count[1];
	} else if ($count[1] && $betterChoice[1]['count'] == $count[1]) {
		$betterChoice[1]['value'] .= \Yii::t('survey', ' or ') . $answer->survey_answer_name;
	}

	if ($betterChoice[3]['count'] < $count[1] + $count[3]) {
		$betterChoice[3]['value'] = $answer->survey_answer_name;
		$betterChoice[3]['count'] = $count[1] + $count[3];
	} else if ($count[1] + $count[3] && $betterChoice[1]['count'] == $count[1] + $count[3]) {
		$betterChoice[3]['value'] .= \Yii::t('survey', ' or ') . $answer->survey_answer_name;
	}

	echo Html::beginTag('tr')
		. Html::tag('td', $answer->survey_answer_name)
		. Html::tag('td', $count[1])
		. Html::tag('td', $count[2])
		. Html::tag('td', $count[3])
		. Html::tag('td', ($count[1] + $count[3]) . ($count[1] && $count[3] == 0 ? ' *' : ''))
		. Html::endTag('tr');
}
?>
	</tbody>
	<tfoot>
		<tr>
			<th><?php echo \Yii::t('survey', 'Better option'); ?></th>
			<th><?php echo $betterChoice[1]['value']; ?></th>
			<th></th>
			<th><?php echo $betterChoice[3]['value']; ?></th>
			<th></th>
		</tr>
	</tfoot>
</table>
</div>