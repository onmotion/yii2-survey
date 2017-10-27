<?php

/* @var $this yii\web\View */

use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $searchModel common\modules\survey\models\search\SurveySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


BootstrapPluginAsset::register($this);

?>
<div id="survey-index">
    <?php
    echo Html::a(\Yii::t('survey', 'Create new survey'), Url::toRoute(['default/create']), ['class' => 'btn btn-success pull-right']);
    Pjax::begin([
        'id' => 'survey-pjax',
        'enablePushState' => true,
        'timeout' => 0]);

    echo ListView::widget(['id' => 'survey-list',
        'layout' => "{summary}\n{items}\n<div class='clearfix'></div><div class='col-md-12'>{pager}</div>",
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            /** @var $model \common\modules\survey\models\Survey */
            $survey = $model;
            ob_start();
            ?>
            <div class="item-column">
                <div class="survey-card">
                    <div class="status <?= $survey->survey_is_closed ? 'closed' : 'active' ?>"></div>
                    <div class="image">IM</div>
                    <div class="description">
                        <div class="name-wrap">
                            <a href="<?= Url::toRoute(['default/view/' . $survey->survey_id]) ?>"
                               class="name" data-pjax="0"
                               title="<?= Html::encode($survey->survey_name) ?>"><?= Html::encode($survey->survey_name) ?></a>

                            <a href="<?= Url::toRoute('default/update/' . $survey->survey_id) ?>"
                               class="btn btn-info btn-xs" data-pjax="0"
                               title="edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        </div>
                        <div class="survey-labels">
                            <span class="survey-label respondents" data-toggle="tooltip"
                                  title="<?= \Yii::t('survey', 'Respondents') ?>"><?= '0' ?></span>
                            <span class="survey-label" data-toggle="tooltip"
                                  title="<?= \Yii::t('survey', 'Questions') ?>"><?= $survey->getQuestions()->count() ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        },]);

    Pjax::end();

    ?>

</div>
    