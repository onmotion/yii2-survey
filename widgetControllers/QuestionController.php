<?php

namespace onmotion\survey\widgetControllers;

use onmotion\survey\models\Survey;
use onmotion\survey\models\SurveyAnswer;
use onmotion\survey\models\SurveyQuestion;
use onmotion\survey\models\SurveyStat;
use onmotion\survey\models\SurveyType;
use onmotion\survey\models\SurveyUserAnswer;
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

    /**
     * @param $question SurveyQuestion
     * @return array|bool
     */
    protected function validate(&$question)
    {

        $stat = SurveyStat::getAssignedUserStat(\Yii::$app->user->getId(), $question->survey->survey_id);
        //не работаем с завершенными опросами
        if ($stat->survey_stat_is_done) {
            return false;
        }
        $post = \Yii::$app->request->post();

        $result = [];

        \Yii::$app->response->format = Response::FORMAT_JSON;

        $answersData = ArrayHelper::getValue($post, "SurveyUserAnswer.{$question->survey_question_id}");
        $userAnswers = $question->userAnswers;

        if (!empty($answersData)) {
            if ($question->survey_question_type === SurveyType::TYPE_MULTIPLE
                || $question->survey_question_type === SurveyType::TYPE_RANKING
                || $question->survey_question_type === SurveyType::TYPE_MULTIPLE_TEXTBOX
                || $question->survey_question_type === SurveyType::TYPE_DATE_TIME
                || $question->survey_question_type === SurveyType::TYPE_CALENDAR
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
                        $userAnswer->save();
                    }
                }
                $question->refresh();
                $question->validateMultipleAnswer();
                foreach ($question->userAnswers as $userAnswer) {
                    foreach ($userAnswer->getErrors() as $attribute => $errors) {
                        $result["surveyuseranswer-{$question->survey_question_id}-{$userAnswer->survey_user_answer_answer_id}-{$attribute}"] = $errors;
                    }
                }
            } elseif ($question->survey_question_type === SurveyType::TYPE_ONE_OF_LIST
                || $question->survey_question_type === SurveyType::TYPE_DROPDOWN
                || $question->survey_question_type === SurveyType::TYPE_SLIDER
                || $question->survey_question_type === SurveyType::TYPE_SINGLE_TEXTBOX
                || $question->survey_question_type === SurveyType::TYPE_COMMENT_BOX
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
                        $userAnswer->save();
                }
            }
        }

        return $result;
    }

    public function actionValidate($id)
    {
        $question = $this->findModel($id);
        return $this->validate($question);
    }

    public function actionSubmitAnswer($id, $n)
    {
        $question = $this->findModel($id);
        $this->validate($question);

        return $this->renderAjax('@surveyRoot/views/widget/question/_form', ['question' => $question, 'number' => $n]);
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
