<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use cenotia\components\modal\RemoteModal;
use onmotion\survey\models\SurveyStat;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */
/* @var $stat SurveyStat */


BootstrapPluginAsset::register($this);
echo Html::csrfMetaTags();

?>

    <div id="survey-widget">
        <div class="row">
            <div class="col-sm-8">

                <div class="survey-container">
                    <div id="survey-widget-title">
                        <h3><?= $survey->survey_name; ?></h3>
                        <?php
                        if (!empty($survey->survey_descr)) {
                            echo "<div class='survey-description'>$survey->survey_descr</div>";
                        }
                        ?>
                    </div>

                    <div id="survey-questions">
                        <?php
                        foreach ($survey->questions as $i => $question) {
                            echo $this->render('@surveyRoot/views/widget/question/_form', ['question' => $question, 'number' => $i]);
                        }
                        ?>
                    </div>
                </div>
                <div class="loader"></div>

            </div>
            <div class="col-sm-4 hidden-xs">
                <div class="survey-infopane" data-spy="affix" data-offset-top="260">
                    <?php
                    if ($stat && $stat->survey_stat_is_done) {
                        $status = 'Completed';
                        $statusClass = 'is-done';
                    } else {
                        $statusClass = $survey->getStatus();
                        switch ($statusClass) {
                            case 'active':
                                $status = 'Active';
                                break;
                            case 'expired':
                                $status = 'Expired';
                                break;
                            case 'closed':
                                $status = 'Closed';
                                break;
                            default:
                                $status = 'undefined';
                                break;
                        }
                    }
                    echo Html::beginTag('div', ['class' => 'infopane-block']);
                    echo Html::tag('div', $status, ['class' => "status $statusClass"]);
                    echo Html::endTag('div');


                    if ($survey->survey_wallet > 0 && \Yii::$app->user->identity->hasWallet()) {
                        ?>
                        <div class="infopane-block">
                            <p>Баллов в кошелек:</p>
                            <div class="wallet-price">
                                <i class="icon-wallet" aria-hidden="true"></i>
                                <span class="price">+<?= $survey->survey_wallet ?></span>
                            </div>
                        </div>
                        <?php
                    }
                    if ($survey->survey_status > 0) {
                        ?>
                        <div class="infopane-block">
                            <p>Баллов в статус:</p>
                            <div class="status-price">
                                <i class="icon-star" aria-hidden="true"></i>
                                <span class="price">+<?= $survey->survey_status ?></span>
                            </div>
                        </div>
                        <?php
                    }

                    $tags = explode(',', $survey->survey_tags);
                    if (!empty($tags[0])) {
                        echo Html::beginTag('div', ['class' => 'infopane-block']);
                        foreach ($tags as $tag) {
                            echo Html::tag('div', trim($tag), ['class' => 'survey-label']);
                        }
                        echo Html::endTag('div');
                    }

                    ?>
                </div>
            </div>

        </div>
        <?php
        $stat = SurveyStat::findOne(['survey_stat_survey_id' => $survey->survey_id, 'survey_stat_user_id' => \Yii::$app->user->getId()]);

        if ($statusClass === 'active') {
            echo Html::button(\Yii::t('survey', 'Done'),
                ['class' => 'btn btn-success center-block', 'data-default-text' => \Yii::t('survey', 'Done'),
                    'id' => 's-done', 'data-action' => Url::toRoute(['/survey/default/done']), 'data-hash' => $stat->survey_stat_hash]);
        }
        ?>
    </div>
<?php
$this->registerJs(<<<JS
$(document).ready(function (e) {
   // $.fn.survey();
    $.fn.surveyWidget({id: $survey->survey_id});
});
JS
);