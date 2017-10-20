<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \common\modules\survey\models\Survey */

$this->title = Yii::t('survey', 'Survey') . ' - ' . $survey->survey_name;


echo $survey->survey_name;