<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 09:51
 */


use onmotion\survey\models\SurveyType;
use kartik\editable\Editable;
use kartik\helpers\Html;
use kartik\select2\Select2;

use vova07\imperavi\Widget;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $question \onmotion\survey\models\SurveyQuestion */

Pjax::begin([
    'id' => 'survey-questions-pjax-' . $question->survey_question_id,
    'enablePushState' => false,
    'timeout' => 0,
    'scrollTo' => false,
    'options' => ['class' => 'survey-question-pjax-container'],
   // 'reloadCss' => false,
    'clientOptions' => [
        'type' => 'post',
        'skipOuterContainers' => true,
    ]
]);

$form = ActiveForm::begin([
    'id' => 'survey-questions-form-' . $question->survey_question_id,
    'action' => Url::toRoute(['question/update-and-close', 'id' => $question->survey_question_id]),
    'validationUrl' => Url::toRoute(['question/validate', 'id' => $question->survey_question_id]),
    'options' => ['class' => 'form-inline', 'data-pjax' => true],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "<div class='survey-questions-form-field'>{label}{input}\n{error}</div>",
        'labelOptions' => ['class' => ''],
    ],
]);

echo Html::beginTag('div', ['class' => 'survey-block', 'id' => 'survey-question-' . $question->survey_question_id]);

echo Html::beginTag('div', ['class' => 'survey-question-name-wrap']);

echo $form->field($question, "[{$question->survey_question_id}]survey_question_name")->input('text', ['placeholder' => \Yii::t('survey', 'Enter question name')])->label(false);

echo Html::a(\Yii::t('survey', '<span class="glyphicon glyphicon-trash"></span>'), Url::toRoute(['question/delete', 'id' => $question->survey_question_id]), [
    'class' => 'btn btn-danger pull-right btn-delete',
]);
echo Html::submitButton('<span class="glyphicon glyphicon-ok"></span>', ['class' => 'btn btn-success pull-right btn-save-question']);
echo Html::submitButton('', ['class' => 'hidden update-question-btn survey-question-submit', 'data-action' => Url::toRoute(['question/update', 'id' => $question->survey_question_id])]);
echo Html::endTag('div');

$confirmMessage = \Yii::t('survey', 'Current types are not compatible, all entered data will be deleted. Are you sure?');
echo $form->field($question, "[{$question->survey_question_id}]survey_question_type")->widget(Select2::classname(), [
    'data' => \onmotion\survey\models\SurveyType::getDropdownList(),
    'pluginOptions' => [
        'allowClear' => false
    ],
    'pluginEvents' => [

        "select2:selecting" => new \yii\web\JsExpression(<<<JS
                function _(e) {
                     var that = $(this);
                     var previous = that.val();
                     var current = e.params.args.data.id;
                     var updateBtn = $(this).closest('[data-pjax-container]').find('.update-question-btn');
                  
                     if (current === '5'){
                          krajeeDialog.confirm('$confirmMessage', function (result) {
                          if (result) {       
                             updateBtn.click();
                          } else {
                              e.preventDefault();
                              if (previous !== undefined){
                                that.select2('val', previous);
                              }
                              return true;
                          }
                         });
                     }
                }
JS
        ),
        "change" => new \yii\web\JsExpression(<<<JS
                function _(e) {
                     let current = e.target.value;
                     if (current === '5'){
                        return false;
                     }
                     let updateBtn = $(this).closest('[data-pjax-container]').find('.update-question-btn');
                     updateBtn.click();
                }
JS
        ),]
]);

echo Html::tag('br', '');
echo Html::tag('br', '');

echo $form->field($question, "[{$question->survey_question_id}]survey_question_show_descr")->checkbox(['class' => 'checkbox-updatable']);
echo Html::tag('br', '');

if ($question->survey_question_show_descr) {
    echo $form->field($question, "[{$question->survey_question_id}]survey_question_descr")->widget(Widget::class, [
        'settings' => [
            'lang' => 'ru',
            'minHeight' => 200,
            'toolbarFixed' => false,
            'imageManagerJson' => Url::toRoute(['question/images-get']),
            'imageUpload' => Url::toRoute(['question/image-upload']),
            'fileManagerJson' => Url::toRoute(['question/files-get']),
            'fileUpload' => Url::toRoute(['question/file-upload']),
            'plugins' => [
                'imagemanager',
                'video',
                'fullscreen',
                'filemanager',
                'fontsize',
                'fontcolor',
                'table',
            ]
        ]
    ])->label(false);
}

echo Html::beginTag('div', ['class' => 'answers-container', 'id' => 'survey-answers-' . $question->survey_question_id]);
if (isset($question->survey_question_type)) {
    echo $this->render('/answers/_form', ['question' => $question, 'form' => $form]);
}

echo Html::endTag('div');

echo Html::tag('hr', '');
echo $form->field($question, "[{$question->survey_question_id}]survey_question_can_skip")->checkbox();
if (in_array($question->survey_question_type, [
    SurveyType::TYPE_MULTIPLE,
    SurveyType::TYPE_ONE_OF_LIST,
    SurveyType::TYPE_DROPDOWN
])) {
    echo Html::tag('br', '');
    echo $form->field($question, "[{$question->survey_question_id}]survey_question_is_scorable")->checkbox(['class' => 'checkbox-updatable']);
}
?>
    <div class="preloader">
        <div class="cssload-spin-box"></div>
    </div>
<?php

echo Html::endTag('div');


ActiveForm::end();

Pjax::end();