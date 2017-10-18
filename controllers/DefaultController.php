<?php

namespace common\modules\survey\controllers;

use common\modules\survey\models\Survey;
use common\modules\survey\models\SurveyAnswer;
use common\modules\survey\models\SurveyQuestion;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `survey` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $survey = new Survey();
        $survey->survey_name = \Yii::t('survey', 'New Survey');
        $survey->save(false);

        return $this->render('create', ['survey' => $survey]);
    }

    public function actionUpdate($id)
    {

        $survey = $this->findModel($id);

        return $this->render('update', ['survey' => $survey]);
    }


    public function actionUpdateEditable($property)
    {
        $model = $this->findModel(\Yii::$app->request->post('id'));

        // Check if there is an Editable ajax request
        if (isset($_POST['hasEditable'])) {
            // use Yii's response format to encode output as JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            // read your posted model attributes
            if ($model->load($_POST)) {
                // read or convert your posted information
                if ($model->validate() && $model->save()) {
                    // return JSON encoded output in the below format
                    return ['output' => $model->$property, 'message' => ''];
                }

                return ['output' => '', 'message' => $model->getFirstError($property)];
            } // else if nothing to do always return an empty JSON encoded output
            else {
                return ['output' => '', 'message' => ''];
            }
        }

        throw new BadRequestHttpException();
    }

    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
