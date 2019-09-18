<?php

namespace onmotion\survey\widgetControllers;

use onmotion\survey\models\search\SurveySearch;
use onmotion\survey\models\search\SurveyStatSearch;
use onmotion\survey\models\Survey;
use onmotion\survey\models\SurveyAnswer;
use onmotion\survey\models\SurveyQuestion;
use onmotion\survey\models\SurveyStat;
use onmotion\survey\models\SurveyType;
use onmotion\survey\SurveyInterface;
use onmotion\survey\User;
use yii\base\Model;
use yii\base\UserException;
use yii\db\Expression;
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
     * @param $question SurveyQuestion
     * @return bool
     */
    protected function validateQuestion($question)
    {
        $userAnswers = $question->userAnswers;
        if ($question->survey_question_type === SurveyType::TYPE_MULTIPLE
            || $question->survey_question_type === SurveyType::TYPE_RANKING
            || $question->survey_question_type === SurveyType::TYPE_MULTIPLE_TEXTBOX
            || $question->survey_question_type === SurveyType::TYPE_CALENDAR
        ) {
            if (count($userAnswers) < 2) {
                return false;
            }
            foreach ($question->answers as $i => $answer) {
                $userAnswer = $userAnswers[$answer->survey_answer_id];
                $userAnswer->validate();
                foreach ($userAnswer->getErrors() as $attribute => $errors) {
                    return false;
                }
                $question->validateMultipleAnswer();
                foreach ($question->userAnswers as $userAnswer) {
                    foreach ($userAnswer->getErrors() as $attribute => $errors) {
                        return false;
                    }
                }
            }
        } elseif ($question->survey_question_type === SurveyType::TYPE_ONE_OF_LIST
            || $question->survey_question_type === SurveyType::TYPE_DROPDOWN
            || $question->survey_question_type === SurveyType::TYPE_SLIDER
            || $question->survey_question_type === SurveyType::TYPE_SINGLE_TEXTBOX
            || $question->survey_question_type === SurveyType::TYPE_COMMENT_BOX
            || $question->survey_question_type === SurveyType::TYPE_DATE_TIME
        ) {
            if (empty(current($userAnswers))) {
                return false;
            }
            $userAnswer = current($userAnswers);
            $userAnswer->validate();
            foreach ($userAnswer->getErrors() as $attribute => $errors) {
                return false;
            }
        }

        return true;
    }

    public function actionDone()
    {
        $id = \Yii::$app->request->post('id');
        if ($id < 0) {
            throw new UserException('Wrong survey id defined');
        }
        $survey = $this->findModel($id);
        $stat = SurveyStat::findOne(['survey_stat_survey_id' => $id, 'survey_stat_user_id' => \Yii::$app->user->getId()]);
        if ($stat === null) {
            throw new UserException('The requested survey stat does not exist.');
        } elseif ($stat->survey_stat_is_done) {
            throw new UserException('The survey has already been completed.');
        }
        foreach ($survey->questions as $question) {
            if (!$this->validateQuestion($question)) {
                var_dump($question->getFirstErrors());
                throw new UserException('An error has been occurred during validating.');
            }
        }
        //all validation is passed.
        $stat->survey_stat_is_done = true;
        $stat->survey_stat_ended_at = new Expression("NOW()");
        $stat->save(false);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'title' => '<div class="text-center"><h2>' . \Yii::t('survey', 'Thank you!'),
            'content' => $this->renderPartial('@surveyRoot/views/widget/default/success', ['survey' => $survey]),
            'footer' =>
              Html::button('Ok', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) 
        ];
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
