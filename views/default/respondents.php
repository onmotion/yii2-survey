<?php

/* @var $this yii\web\View */

use kartik\widgets\Typeahead;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $searchModel \onmotion\survey\models\search\SurveyStatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $surveyId integer */


?>
<div id="survey-respondents" class="survey-container">
    <?php


   echo Html::beginTag('div', ['id' => 'addition-form']);
   echo Html::label(\Yii::t('survey', 'Add respondents'), [], ['class' => 'control-label']);

   $assignUrl = Url::toRoute(['default/assign-user', 'surveyId' => $surveyId]);
   $pjaxUrl = Url::toRoute(['default/respondents', 'surveyId' => $surveyId]);
   echo Typeahead::widget([
       'name' => 'respondents',
       'id' => 'respondents-typeahead',
       'scrollable' => true,
       'dataset' => [
           [
               'display' => 'text',
               'remote' => ['url' => Url::toRoute(['default/get-respondents-by-token', 'surveyId' => $surveyId]) . '&token=%QUERY',
                   'wildcard' => '%QUERY',
                   'rateLimitWait' => 300],
               'limit' => 20,
               'templates' => [
                   'notFound' => '<div class="text-danger" style="padding:0 8px">Ничего не найдено.</div>',
                   'suggestion' => new \yii\web\JsExpression(<<<JS
                   function _(data) {
                  if(data.isAssigned === true){
                      return '<div class="disabled">'+ data.text +'</div>';
                  }else{
                      return '<div>'+ data.text +'</div>';
                  }
                   }
JS
                   ),
               ],
           ]
       ],
       'pluginOptions' => [
           'highlight' => true,
           'minLength' => 2,
           'val' => '',
       ],
       'pluginEvents' => [
           "typeahead:selected" => <<<JS
                       function _(ev, data) {
   if(data.isAssigned === true){
       $('#respondents-typeahead').typeahead('val', '');
       return false;
   }
                         $.post("$assignUrl", { userId: data.id })
                               .done(function( data ) {
                                   $('#respondents-typeahead').typeahead('val', '');
                                   $.pjax({container: '#survey-respondents-pjax', timeout: 0, url: '$pjaxUrl', push: false});
                               }).fail(function(error) {
                                    alert(error.responseJSON.message);
                                });
                       }
JS
       ],
       'options' => ['placeholder' => 'Type some words...'],]);
   echo Html::endTag('div');

   Pjax::begin([
       'id' => 'survey-respondents-pjax',
       'enableReplaceState' => false,
       'enablePushState' => false,
       'timeout' => 0,
       'clientOptions' => [
           //   'async' => false
       ],
   ]);

   ActiveForm::begin([
       'id' => 'survey-respondents-form',
       'action' => Url::toRoute(['default/unassign-user', 'surveyId' => $surveyId]),
       'enableClientValidation' => false,
       'enableAjaxValidation' => false,
       'options' => ['data-pjax' => true],
   ]);


   echo ListView::widget([
       'id' => 'survey-respondents-list',
       'layout' => "<div class='pull-right'>{summary}</div>\n<div class='clearfix'></div>{items}\n<div class='clearfix'></div><div class='col-md-12'>{pager}</div>",
       'dataProvider' => $dataProvider,
       'itemOptions' => ['class' => 'item'],
       'itemView' => function ($model, $key, $index, $widget) use ($surveyId) {
           /** @var $model \onmotion\survey\models\SurveyStat */
           $surveyStat = $model;
           ob_start();
           ?>
           <div class="assigned-user">
              <?php
               echo $surveyStat->user->fullname
               ?>

           </div>
           <div class="buttons">
              <?php
               echo Html::submitButton(\Yii::t('survey', '<i class="fa fa-trash" aria-hidden="true"></i>'),
                   ['class' => 'btn btn-danger btn-delete-assigned-user user-assign-submit',
                       'data-action' => Url::toRoute(['default/unassign-user', 'surveyId' => $surveyId]),
                       'name' => 'userId', 'value' => $surveyStat->survey_stat_user_id
                   ]);
               ?>
           </div>
          <?php
           return ob_get_clean();
       },]);
    ActiveForm::end();
   Pjax::end();

    ?>

</div>
    
