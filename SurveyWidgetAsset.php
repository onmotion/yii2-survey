<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:18
 */

namespace common\modules\survey;


use yii\web\AssetBundle;

class SurveyWidgetAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

    public $publishOptions = [
        'forceCopy' => true //dev
    ];

    public $css = [
        'css/survey.css',
        'css/preloader.css',
    ];
    public $js = [
        'js/survey.js',
        'js/survey.widget.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}