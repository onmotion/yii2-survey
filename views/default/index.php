<?php

/* @var $this yii\web\View */

use cenotia\components\modal\RemoteModal;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $searchModel onmotion\survey\models\search\SurveySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


BootstrapPluginAsset::register($this);

?>
    <div id="survey-index">
        <?php
        echo Html::a(\Yii::t('survey', 'Create new survey'), Url::toRoute(['default/create']), ['class' => 'btn btn-success pull-right']);

        Pjax::begin([
            'id' => 'survey-index-pjax',
            'enablePushState' => true,
            'timeout' => 0]);

        echo ListView::widget(['id' => 'survey-list',
            'layout' => "{summary}\n{items}\n<div class='clearfix'></div><div class='col-md-12'>{pager}</div>",
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                /** @var $model \onmotion\survey\models\Survey */
                $survey = $model;
                $image = $survey->getImage();
                ob_start();
                ?>
                <div class="item-column">
                    <div class="survey-card">
                        <div class="status <?= $survey->getStatus() ?>"></div>
                        <div class="first-line">
                            <div class="image" <?php
                            if (!empty($image)) {
                                echo "style='background-image: url($image)'";
                            }
                            ?>></div>
                            <div class="description">
                                <div class="name-wrap">
                                    <a href="<?= Url::toRoute(['default/view/', 'id' => $survey->survey_id]) ?>"
                                       class="name" data-pjax="0"
                                       title="<?= Html::encode($survey->survey_name) ?>"><?= Html::encode($survey->survey_name) ?></a>
                                </div>
                                <div>
                                    <div class="survey-labels">
                            <span class="survey-label respondents" data-toggle="tooltip"
                                  title="<?= \Yii::t('survey', 'Respondents count') ?>"><?= $survey->getRespondentsCount() ?></span>
                                        <span class="survey-label completed-respondents" data-toggle="tooltip"
                                              title="<?= \Yii::t('survey', 'Were interviewed') ?>"><?= $survey->getCompletedRespondentsCount() ?></span>
                                        <span class="survey-label" data-toggle="tooltip"
                                              title="<?= \Yii::t('survey', 'Questions count') ?>"><?= $survey->getQuestions()->count() ?></span>
                                    </div>
                                    <div class="survey-actions">
                                        <a href="<?= Url::toRoute(['default/update/', 'id' => $survey->survey_id]) ?>"
                                           class="btn btn-info btn-xs" data-pjax="0"
                                           title="edit"><span class="glyphicon glyphicon-pencil"></span></a>
                                        <?php
                                        echo Html::a(\Yii::t('survey', '<span class="glyphicon glyphicon-trash"></span>'), Url::toRoute(['default/delete', 'id' => $survey->survey_id]), [
                                            'class' => 'btn btn-danger btn-xs pull-right btn-delete',
                                            'data-pjax' => 0,
                                            'role' => 'remote-modal',
                                            'data-confirm-message' => 'Are you sure?',
                                            'data-method' => false,// for overide yii data api
                                            'data-request-method' => 'post',
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="second-line">
                            <span>Author: <?= $survey->getAuthorName() ?></span>
                            <span class="date">Created At: <?= \Yii::$app->formatter->asDate($survey->survey_created_at) ?></span>
                        </div>
                    </div>
                </div>
                <?php
                return ob_get_clean();
            },]);

        Pjax::end();

        ?>

    </div>

<?php RemoteModal::begin([
    "id" => "remote-modal",
    "options" => ["class" => "fade "],
    "footer" => "", // always need it for jquery plugin
]) ?>
<?php RemoteModal::end(); ?>

<?php
$this->registerJs(<<<JS
$(document).ready(function (e) {
    $.fn.survey();
});
JS
);
