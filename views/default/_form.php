<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use kartik\dialog\Dialog;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $survey \common\modules\survey\models\Survey */

// widget with default options
echo Dialog::widget();

?>
<div class="survey-container">

    <div class="survey-block">
        <?php

        echo Html::beginTag('div', ['class' => 'survey-name-wrap']);

        echo Editable::widget([
            'model' => $survey,
            'attribute' => 'survey_name',
            'asPopover' => true,
            'header' => 'Name',
            'size' => 'md',
            'formOptions' => [
                'action' => Url::to(['/survey/default/update-editable', 'property' => 'survey_name'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'class' => 'form-control',
                'placeholder' => 'Enter survey name...',
            ]
        ]);
        echo Html::endTag('div');


        echo Html::tag('br', '');

        echo Html::label(Yii::t('survey', 'Expired at') . ': ', 'survey-survey_expired_at');
        echo Editable::widget([
            'model' => $survey,
            'attribute' => 'survey_expired_at',
            'header' => 'Expired at',
            'asPopover' => true,
            'size' => 'md',
            'inputType' => Editable::INPUT_DATETIME,
            'formOptions' => [
                'action' => Url::to(['/survey/default/update-editable', 'property' => 'survey_expired_at'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'options' => ['placeholder' => 'Expired at']
            ]
        ]);


        ?>
    </div>

    <div id="survey-questions">
        <?php
        foreach ($survey->questions as $i => $question) {
            echo $this->render('/question/_form', ['question' => $question]);
        }
        ?>
    </div>
    <?php
    echo Html::tag('div', '', ['id' => 'survey-questions-append']);

    Pjax::begin([
        'id' => 'survey-questions-pjax',
        'enablePushState' => false,
        'timeout' => 0,
        'scrollTo' => false,
        'clientOptions' => [
            'type' => 'post',
            'container' => '#survey-questions-append',
            'skipOuterContainers' => true,
        ]
    ]);
    echo Html::tag('div', Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('survey', 'Add question'), Url::toRoute(['/survey/question/create', 'id' => $question->survey->survey_id]), ['class' => 'btn btn-success']),
        ['class' => 'text-center survey-btn', 'id' => '']);
    echo Html::tag('div', Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('survey', 'Save'),
        ['class' => 'btn btn-primary']), ['class' => 'text-center survey-btn']);

    Pjax::end(); ?>


</div>