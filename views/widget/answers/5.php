<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use common\modules\survey\models\SurveyUserAnswer;
use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \common\modules\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */

$userAnswers = $question->userAnswers;

