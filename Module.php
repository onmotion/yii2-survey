<?php

namespace common\modules\survey;

use yii\base\UserException;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/**
 * survey module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = null;
    public $userClass;

    public $params = [
        'uploadsUrl' => null,
        'uploadsPath' => null,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->controllerNamespace = \Yii::$app->controllerNamespace === 'backend\controllers'
            ? 'common\modules\survey\controllers'
            : 'common\modules\survey\widgetControllers';


        parent::init();

        if (empty($this->params['uploadsUrl'])) {
            throw new UserException("You must set uploadsUrl param in the config. Please see the documentation for more information.");
        }else{
            $this->params['uploadsUrl'] = rtrim($this->params['uploadsUrl'], '/');
        }
        if (empty($this->params['uploadsPath'])) {
            throw new UserException("You must set uploadsPath param in the config. Please see the documentation for more information.");
        }else{
            $this->params['uploadsPath'] = FileHelper::normalizePath($this->params['uploadsPath']);
        }

        $this->userClass = \Yii::$app->user->identityClass;

        \Yii::setAlias('@surveyRoot', __DIR__);

        // set up i8n
        if (empty(\Yii::$app->i18n->translations['survey'])) {
            \Yii::$app->i18n->translations['survey'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@surveyRoot/messages',
            ];
        }

        $view = \Yii::$app->getView();
        SurveyAsset::register($view);
    }
}
