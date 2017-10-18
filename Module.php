<?php

namespace common\modules\survey;

use yii\base\UserException;

/**
 * survey module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\survey\controllers';


    public $params = [
        'uploadsUrl' => null,
        'uploadsPath' => null,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->params['uploadsUrl'])){
            throw new UserException("You must set uploadsUrl param in the config. Please see the documentation for more information.");
        }
        if (empty($this->params['uploadsPath'])){
            throw new UserException("You must set uploadsPath param in the config. Please see the documentation for more information.");
        }

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
