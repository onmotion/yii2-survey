<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 26/10/2017
 * Time: 10:09
 */

namespace common\modules\survey;


use yii\web\NotFoundHttpException;

class Survey extends \yii\base\Widget
{
    public $surveyId = null;

    public function init()
    {
        // set up i8n
        if (empty(\Yii::$app->i18n->translations['survey'])) {
            \Yii::$app->i18n->translations['survey'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@surveyRoot/messages',
            ];
        }

        \Yii::setAlias('@surveyRoot', __DIR__);

        parent::init();
    }

    public function getViewPath()
    {
        return \Yii::getAlias('@surveyRoot/views');
    }

    public function run()
    {
        $view = $this->getView();
        SurveyAsset::register($view);
        $this->renderSurvey($this->surveyId);
    }

    private function renderSurvey($id)
    {
        $survey = $this->findModel($id);
        echo $this->render('widget/default/index', ['survey' => $survey]);
    }

    protected function findModel($id)
    {
        if (($model = \common\modules\survey\models\Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}