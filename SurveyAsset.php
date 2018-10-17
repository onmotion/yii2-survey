<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:18
 */

namespace onmotion\survey;


use yii\web\AssetBundle;

class SurveyAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV //dev
    ];

    public $css = [
        'css/survey.css',
        'css/preloader.css',
    ];
    public $js = [
        'js/survey.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}