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
use kartik\widgets\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
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
                'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_name'])
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
                'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_expired_at'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'class' => Editable::INPUT_DATETIME,
                'pluginOptions' => [
                    'autoclose' => true,
                   // 'format' => 'd.m.Y H:i'
                ],
                'options' => ['placeholder' => 'Expired at']
            ]
        ]);

        Pjax::begin([
            'id' => 'survey-pjax',
            'enablePushState' => false,
            'timeout' => 0,
            'scrollTo' => false,
            'clientOptions' => [
                'type' => 'post',
                'skipOuterContainers' => true,
            ]
        ]);

        $form = ActiveForm::begin([
            'id' => 'survey-form',
            'action' => Url::toRoute(['default/update', 'id' => $survey->survey_id]),
            'options' => ['class' => 'form-inline', 'data-pjax' => true],
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'fieldConfig' => [
                'template' => "<div class='survey-form-field submit-on-click'>{label}{input}\n{error}</div>",
                'labelOptions' => ['class' => ''],
            ],
        ]);

        echo $form->field($survey, "survey_is_closed")->checkbox(['class' => 'checkbox']);

        echo Html::submitButton('ss', ['class' => 'hidden']);


        ActiveForm::end();

        Pjax::end();

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
    echo Html::tag('div', Html::a('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('survey', 'Add question'), Url::toRoute(['question/create', 'id' => $survey->survey_id]), ['class' => 'btn btn-success']),
        ['class' => 'text-center survey-btn', 'id' => '']);
    echo Html::tag('div', Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('survey', 'Save'),
        ['class' => 'btn btn-primary']), ['class' => 'text-center survey-btn', 'id' => 'done', 'data-action' => Url::toRoute(['default/view', 'id' => $survey->survey_id])]);

    Pjax::end(); ?>


</div>