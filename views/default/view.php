<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use cenotia\components\modal\RemoteModal;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \common\modules\survey\models\Survey */
/* @var $respondentsCount integer */

$this->title = Yii::t('survey', 'Survey') . ' - ' . $survey->survey_name;

BootstrapPluginAsset::register($this);


?>
<div id="survey-title">
    <h4><?= $survey->survey_name; ?></h4>
    <div class="survey-labels">
        <a href="<?= Url::toRoute(['default/respondents', 'surveyId' => $survey->survey_id]) ?>" role="remote-modal" class="survey-label respondents" data-toggle="tooltip" title="<?= \Yii::t('survey', 'Respondents') ?>"><?= $respondentsCount ?></a>
        <span class="survey-label" data-toggle="tooltip" title="<?= \Yii::t('survey', 'Questions') ?>"><?= $survey->getQuestions()->count() ?></span>
    </div>
</div>
<div id="survey-view">
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

<?php RemoteModal::begin([
    "id"=>"remote-modal",
    "options"=> [ "class"=>"fade slide-right "],
    "footer"=>"", // always need it for jquery plugin
])?>
<?php RemoteModal::end(); ?>