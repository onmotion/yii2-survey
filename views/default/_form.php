<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use common\modules\crop_image_widget\UploadCrop;
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

        \yii\widgets\Pjax::begin([
            'id' => 'form-photo-pjax',
            'timeout' => 0,
            'enablePushState' => false
        ]);
        $form = ActiveForm::begin([
            'id' => 'survey-photo-form',
            'action' => \yii\helpers\Url::toRoute(['default/update-image', 'id' => $survey->survey_id]),
            'options' => ['class' => 'form-horizontal', 'data-pjax' => true],
            //  'enableAjaxValidation' => true,
        ]);

        echo UploadCrop::widget([
            'form' => $form,
            'model' => $survey,
            'attribute' => 'imageFile',
            'enableClientValidation' => true,
            'defaultPreviewImage' => $survey->getImage(),
            'jcropOptions' => [
                //  'dragMode' => 'none',
                'viewMode' => 2,
                'aspectRatio' => 1,
                'autoCropArea' => 1,
                'rotatable' => true,
                'scalable' => true,
                'zoomable' => true,
                'toggleDragModeOnDblclick' => false
            ]
        ]);

        ActiveForm::end();
        \yii\widgets\Pjax::end();

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

        echo Html::beginTag('div', ['class' => 'col-md-6']);
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

        echo Html::tag('div', '', ['class' => 'clearfix']);
        echo Html::label(Yii::t('survey', 'Time to pass') . ': ', 'survey-survey_time_to_pass');
        echo Editable::widget([
            'model' => $survey,
            'attribute' => 'survey_time_to_pass',
            'asPopover' => true,
            'header' => 'Time to pass',
            'size' => 'md',
            'formOptions' => [
                'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_time_to_pass'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'class' => 'form-control',
                'placeholder' => 'Enter time in minutes...',
                'type' => 'number',
            ]
        ]);
        echo Html::label(Yii::t('survey', 'minutes'));

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-6']);
        echo Html::label(Yii::t('survey', 'Wallet Price') . ': ', 'survey-survey_wallet');
        echo Editable::widget([
            'model' => $survey,
            'attribute' => 'survey_wallet',
            'asPopover' => true,
            'header' => 'Price',
            'size' => 'md',
            'formOptions' => [
                'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_wallet'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'class' => 'form-control',
                'placeholder' => 'Enter survey price...',
                'type' => 'number',
            ]
        ]);
        echo Html::label(Yii::t('survey', 'Status Price') . ': ', 'survey-survey_status');
        echo Editable::widget([
            'model' => $survey,
            'attribute' => 'survey_status',
            'asPopover' => true,
            'header' => 'Price',
            'size' => 'md',
            'formOptions' => [
                'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_status'])
            ],
            'additionalData' => ['id' => $survey->survey_id],
            'options' => [
                'class' => 'form-control',
                'placeholder' => 'Enter survey price...',
                'type' => 'number',
            ]
        ]);

        echo Html::endTag('div');
        echo Html::tag('div', '', ['class' => 'clearfix']);

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
                'template' => "<div class='survey-form-field'>{label}{input}\n{error}</div>",
                'labelOptions' => ['class' => ''],
            ],
        ]);

        echo Html::beginTag('div', ['class' => 'col-md-12']);
        echo $form->field($survey, "survey_descr", ['template' => "<div class='survey-form-field'>{label}{input}</div>",]
        )->textarea(['rows' => 3]);
        echo Html::tag('div', '', ['class' => 'clearfix']);
        echo Html::endTag('div');


        echo Html::beginTag('div', ['class' => 'col-md-6']);
        echo $form->field($survey, "survey_is_closed", ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
        )->checkbox(['class' => 'checkbox danger'], false);
        echo Html::tag('div', '', ['class' => 'clearfix']);
        echo $form->field($survey, "survey_is_pinned", ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
        )->checkbox(['class' => 'checkbox'], false);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-6']);
        echo $form->field($survey, "survey_tags")->input('text', ['placeholder' => 'Comma separated']);

        echo Html::endTag('div');


        echo Html::submitButton('', ['class' => 'hidden']);
        echo Html::tag('div', '', ['class' => 'clearfix']);

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
        ['class' => 'btn btn-primary', 'data-default-text' => '<i class="fa fa-floppy-o" aria-hidden="true"></i> ' . Yii::t('survey', 'Save')]), ['class' => 'text-center survey-btn', 'id' => 'save', 'data-action' => Url::toRoute(['default/view', 'id' => $survey->survey_id])]);

    Pjax::end(); ?>


</div>

<?php
$this->registerJs(<<<JS
$(document).ready(function(e) {
  $(document).on('cropready', function() {
    $('#survey-photo-form').submit();
  });
});
JS
);

$this->registerCss(<<<CSS
.modal-backdrop.in{
display: none;
}
CSS
);

$this->registerJs(<<<JS
$(document).ready(function (e) {
    $.fn.survey();
});
JS
);