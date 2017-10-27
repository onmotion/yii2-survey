<?php

namespace common\modules\survey\controllers;

use common\modules\survey\models\search\SurveySearch;
use common\modules\survey\models\search\SurveyStatSearch;
use common\modules\survey\models\Survey;
use common\modules\survey\models\SurveyAnswer;
use common\modules\survey\models\SurveyQuestion;
use common\modules\survey\models\SurveyStat;
use common\modules\survey\SurveyInterface;
use common\modules\survey\User;
use yii\base\Model;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
        $searchModel = new SurveySearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $survey = $this->findModel($id);
        $respondentsCount = SurveyStat::find()->where(['survey_stat_survey_id' => $id])->count();
        return $this->render('view', ['survey' => $survey, 'respondentsCount' => $respondentsCount]);
    }

    public function actionCreate()
    {
        $survey = new Survey();
        $survey->survey_name = \Yii::t('survey', 'New Survey');
        $survey->save(false);

        return $this->render('create', ['survey' => $survey]);
    }

    public function actionRespondents($surveyId)
    {
        $searchModel = new SurveyStatSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['survey_stat_survey_id' => $surveyId])
        ->orderBy(['survey_stat_assigned_at' => SORT_DESC]);

        $dataProvider->pagination->pageSize = 10;

        if (\Yii::$app->request->isPjax){
            $dataProvider->pagination->route = Url::toRoute(['default/respondents']); ;
            return $this->renderAjax('respondents',
                compact('searchModel', 'dataProvider', 'surveyId'));
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'title' => "Assigned respondents",
            'content' => $this->renderAjax('respondents',
                compact('searchModel', 'dataProvider', 'surveyId')
            ),
            'footer' => Html::button('Close', ['class' => 'btn btn-default', 'data-dismiss' => "modal"])
        ];
    }

    /**
     * Returns user models founded by token
     *
     * @param $token string
     * @param $surveyId
     * @return User
     */
    public function actionGetRespondentsByToken($token, $surveyId)
    {
        $userClass = $this->module->userClass;
        $userList = $userClass::actionGetRespondentsByToken($token);
        $userList = ArrayHelper::index($userList, 'id');
        $ids = ArrayHelper::getColumn($userList, 'id');
        $assignedRespondents = SurveyStat::find()->where(['survey_stat_survey_id' => $surveyId])
            ->andWhere(['survey_stat_user_id' => $ids])->asArray()->all();

        foreach ($assignedRespondents as $item){
            $userList[$item['survey_stat_user_id']]['isAssigned'] = true;
        }

        return json_encode($userList);

    }

    /**
     * @param $surveyId
     * @return bool
     */
    public function actionAssignUser($surveyId)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = \Yii::$app->request->post('userId');

        return SurveyStat::assignUser($userId, $surveyId);
    }

    /**
     * @param $surveyId
     * @return array|string
     */
    public function actionUnassignUser($surveyId)
    {
        $userId = \Yii::$app->request->post('userId');
        SurveyStat::unassignUser($userId, $surveyId);

        return $this->actionRespondents($surveyId);
    }

    public function actionUpdate($id)
    {

        $survey = $this->findModel($id);

        if (\Yii::$app->request->isPjax) {
            $post = \Yii::$app->request->post();
            if ($survey->load($post) && $survey->validate()) {
                $survey->save();
                return $this->renderAjax('update', ['survey' => $survey]);
            }
        }

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
