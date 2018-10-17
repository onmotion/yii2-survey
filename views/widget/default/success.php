<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */


use kartik\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */


?>


<div class="reward-info">
    <?php
    if ($survey->survey_wallet > 0 && \Yii::$app->user->identity->hasWallet()) {
        ?>
        <div class="reward-item">
            <div class="wallet-price">
                <i class="icon-wallet" aria-hidden="true"></i>
                <span class="price">+<?= $survey->survey_wallet ?></span>
            </div>
            <p>Баллов в кошелек</p>
        </div>
        <?php
    }
    if ($survey->survey_status > 0) {
        ?>
        <div class="reward-item">
            <div class="status-price">
                <i class="icon-star" aria-hidden="true"></i>
                <span class="price">+<?= $survey->survey_status ?></span>
            </div>
            <p>Баллов в статус</p>
        </div>
        <?php
    }
    ?>
</div>
<p class="text">
    Ты прошел опрос <b><?= $survey->survey_name ?></b> и помог нашей команде собрать важную информацию.
</p>

