<?php

namespace common\modules\survey\widgetControllers;

use common\modules\survey\models\Survey;
use common\modules\survey\models\SurveyAnswer;
use common\modules\survey\models\SurveyQuestion;
use common\modules\survey\models\SurveyType;
use common\modules\survey\models\SurveyUserAnswer;
use kartik\widgets\ActiveForm;
use vova07\imperavi\actions\GetAction;
use yii\base\Event;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `survey` module
 */
class QuestionController extends Controller
{


    public function actionValidate($id)
    {
        $question = $this->findModel($id);
        $post = \Yii::$app->request->post();
        $action = ArrayHelper::getValue($post, "action");
        $result = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $answersData = ArrayHelper::getValue($post, "SurveyUserAnswer.{$question->survey_question_id}");

        $userAnswers = $question->userAnswers;

        if (!empty($answersData)) {
            if ($question->survey_question_type === SurveyType::TYPE_MULTIPLE
                || $question->survey_question_type === SurveyType::TYPE_RANKING
            ) {
                foreach ($question->answers as $i => $answer) {
                    $userAnswer = isset($userAnswers[$answer->survey_answer_id]) ? $userAnswers[$answer->survey_answer_id] : (new SurveyUserAnswer([
                        'survey_user_answer_user_id' => \Yii::$app->user->getId(),
                        'survey_user_answer_survey_id' => $question->survey_question_survey_id,
                        'survey_user_answer_question_id' => $question->survey_question_id,
                        'survey_user_answer_answer_id' => $answer->survey_answer_id
                    ]));
                    if ($userAnswer->load($answersData[$answer->survey_answer_id], '')) {
                        $userAnswer->validate();
                        foreach ($userAnswer->getErrors() as $attribute => $errors) {
                            $result["surveyuseranswer-{$question->survey_question_id}-{$answer->survey_answer_id}-{$attribute}"] = $errors;
                        }
                        $userAnswer->save(false);
                    }
                }
            } elseif ($question->survey_question_type === SurveyType::TYPE_ONE_OF_LIST
                || $question->survey_question_type === SurveyType::TYPE_DROPDOWN
            ) {
                $userAnswer = !empty(current($userAnswers)) ? current($userAnswers) : (new SurveyUserAnswer([
                    'survey_user_answer_user_id' => \Yii::$app->user->getId(),
                    'survey_user_answer_survey_id' => $question->survey_question_survey_id,
                    'survey_user_answer_question_id' => $question->survey_question_id,
                ]));
                if ($userAnswer->load($answersData, '')) {
                    $userAnswer->validate();
                    foreach ($userAnswer->getErrors() as $attribute => $errors) {
                        $result["surveyuseranswer-{$question->survey_question_id}-{$attribute}"] = $errors;
                    }
                    $userAnswer->save(false);
                }
            }
        }

        return $result;
    }


    protected function findModel($id)
    {
        if (($model = SurveyQuestion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findSurveyModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findTypeModel($id)
    {
        if (($model = SurveyType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
