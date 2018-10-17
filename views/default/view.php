<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use cenotia\components\modal\RemoteModal;
use onmotion\survey\models\search\SurveyStatSearch;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */
/* @var $respondentsCount integer */

$this->title = Yii::t('survey', 'Survey') . ' - ' . $survey->survey_name;

BootstrapPluginAsset::register($this);


?>
    <div  id="survey-view">
        <div id="survey-title">
            <div class="subcontainer flex">
                <h4><?= $survey->survey_name; ?></h4>
                <div>
                    <div class="survey-labels">
                        <span class="survey-label respondents-toggle" data-toggle="tooltip"
                              title="<?= \Yii::t('survey', 'Respondents') ?>"><?= \Yii::t('survey', 'Number of respondents') ?>: <?= $survey->getRespondentsCount() ?></span>
                        <span class="survey-label" data-toggle="tooltip"
                              title="<?= \Yii::t('survey', 'Questions') ?>"><?= $survey->getQuestions()->count() ?></span>
                    </div>

                </div>

            </div>
            <div class="subcontainer">

                <?php
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
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-6']);

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

        </div>
        <div>
            <div class="survey-container">

                <div id="survey-questions">
                    <?php
                    foreach ($survey->questions as $i => $question) {
                        echo $this->render('/question/_viewForm', ['question' => $question, 'number' => $i]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<div class="hidden-modal-right " id="respondents-modal">
    <div class="close-btn">&times;</div>
    <?php

    $surveyId = $survey->survey_id;

    echo $this->render('respondents',
        compact('searchModel', 'dataProvider', 'surveyId'));
    ?>
</div>

<?php
$this->registerJs(<<<JS
$(document).ready(function(e) {
    setTimeout(function() {
       $('.progress-bar').each(function(i, el) {
    if ($(el).hasClass('init')){
        $(el).removeClass('init');
    }
  })
    }, 1000);
});
JS
);

$this->registerJs(<<<JS
$(document).ready(function (e) {
    $.fn.survey();
});
JS
);