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


BootstrapPluginAsset::register($this);

?>

<div id="survey-widget">
    <div id="survey-title">
        <div class="subcontainer">
        <h3><?= $survey->survey_name; ?></h3>
        <div class="survey-labels">
            <div class="survey-label">
                ОПРОС
            </div>
        </div>
        </div>
    </div>
    <div class="survey-container">
        <div id="survey-questions">
            <?php
            foreach ($survey->questions as $i => $question) {
                echo $this->render('@surveyRoot/views/widget/question/_form', ['question' => $question, 'number' => $i]);
            }
            ?>
        </div>
    </div>
</div>
