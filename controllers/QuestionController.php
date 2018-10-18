<?php

namespace onmotion\survey\controllers;

use onmotion\survey\models\Survey;
use onmotion\survey\models\SurveyAnswer;
use onmotion\survey\models\SurveyQuestion;
use onmotion\survey\models\SurveyType;
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

    public function actions()
    {
        return [
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetImagesAction',
                'url' => $this->module->params['uploadsUrl'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // URL адрес папки где хранятся изображения.
                'path' => $this->module->params['uploadsPath'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // Или абсолютный путь к папке с изображениями.
                'options' => ['only' => ['*.jpg', '*.jpeg', '*.png', '*.gif', '*.ico']], // These options are by default.
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => $this->module->params['uploadsUrl'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // URL адрес папки где хранятся изображения.
                'path' => $this->module->params['uploadsPath'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // Или абсолютный путь к папке с изображениями.
            ],
            'files-get' => [
                'class' => 'vova07\imperavi\actions\GetFilesAction',
                'url' => $this->module->params['uploadsUrl'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // URL адрес папки где хранятся изображения.
                'path' => $this->module->params['uploadsPath'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // Или абсолютный путь к папке с изображениями.
            ],
            'file-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => $this->module->params['uploadsUrl'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // URL адрес папки где хранятся изображения.
                'path' => $this->module->params['uploadsPath'] . \Yii::$app->session->get('surveyUploadsSubpath', ''), // Или абсолютный путь к папке с изображениями.
                'uploadOnlyImage' => false, // Для загрузки не только изображений.
            ],

        ];
    }

    public function actionCreate($id)
    {
        $survey = $this->findSurveyModel($id);
        $question = new SurveyQuestion();
        $question->loadDefaultValues();
        // $question->survey_question_name = \Yii::t('survey', 'New Question');
        $survey->link('questions', $question);

        for ($i = 1; $i <= 2; ++$i) {
            $question->link('answers', (new SurveyAnswer(['survey_answer_sort' => $i])));
        }

        return $this->renderAjax('_form', ['question' => $question]);
    }

    public function actionDelete($id)
    {
        $question = $this->findModel($id);

        if ($question->delete()) {
            return '<span></span>';
        } else {
            throw new HttpException('500', 'unable to delete record');
        }
    }

    public function actionValidate($id)
    {
        $question = $this->findModel($id);
        $post = \Yii::$app->request->post();
        $action = ArrayHelper::getValue($post, "action");
        $result = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $questionIsChanged = false;

        $questionData = ArrayHelper::getValue($post, "SurveyQuestion.{$question->survey_question_id}");
        if (!empty($questionData) && $question->load($questionData, '')) {
            $isValid = $question->validate();
            $questionIsChanged = !empty($question->getDirtyAttributes());
            foreach ($question->getErrors() as $attribute => $errors) {
                $result["surveyquestion-{$question->survey_question_id}-{$attribute}"] = $errors;
            }
            if ($isValid) {
                $attrsForSave = $question->getAttributes(null, ['survey_question_type']);
                $attrs = array_keys($question->getDirtyAttributes(array_keys($attrsForSave)));
                $question->save(false, $attrs);
            }
        }

        $answersData = ArrayHelper::getValue($post, "SurveyAnswer.{$question->survey_question_id}");

        if (!empty($answersData)
            && (count($answersData) === count($question->answers))
            && Model::loadMultiple($question->answers, $answersData, '')) {
            foreach ($question->answers as $i => $model) {
                if (!$questionIsChanged || $action !== 'delete-answer') {
                    $model->validate();
                    foreach ($model->getErrors() as $attribute => $errors) {
                        $result["surveyanswer-{$question->survey_question_id}-{$i}-{$attribute}"] = $errors;
                    }
                }
                //    $model->validate();
                $model->save();
            }
        }

        return $result;
    }

    public function actionUpdate($id)
    {
        $question = $this->findModel($id);

        $post = \Yii::$app->request->post();

        $questionData = ArrayHelper::getValue($post, "SurveyQuestion.{$question->survey_question_id}");

        $isTypeChanged = false;
        if (!empty($questionData) && $question->load($questionData, '') && $question->validate()) {
            $isTypeChanged = $question->isAttributeChanged('survey_question_type');
            if ($isTypeChanged) {
                $question->changeDefaultValuesOnTypeChange();
            }
            $question->save(false);
        }

        $answersData = ArrayHelper::getValue($post, "SurveyAnswer.{$question->survey_question_id}");
        if (!empty($answersData) && Model::loadMultiple($question->answers, $answersData, '') && !$isTypeChanged) {
            foreach ($question->answers as $answer) {
                $answer->save();
            }
        }

        $question->refresh();

        return $this->renderAjax('_form', ['question' => $question]);
    }

    public function actionEdit($id)
    {
        $question = $this->findModel($id);
        return $this->renderAjax('_form', ['question' => $question]);
    }


    public function actionAddAnswer($id, $after = null)
    {
        $question = $this->findModel($id);
        $answers = $question->answers;
        $lastAnswer = array_values(array_slice($answers, -1))[0];

        if (!isset($after)) {
            $sort = ArrayHelper::getValue($lastAnswer, 'survey_answer_sort', 0) + 1;
        } else {
            $pevAnswer = $answers[$after] ? $answers[$after] : null;
            $sort = ArrayHelper::getValue($pevAnswer, 'survey_answer_sort', 0) + 1;

            //moving all new answers forward
            SurveyAnswer::updateAll(['survey_answer_sort' => new Expression('survey_answer_sort+1')],
                ['AND', ['>=', 'survey_answer_sort', $sort], ['survey_answer_question_id' => $question->survey_question_id]]);
        }

        $question->link('answers', (new SurveyAnswer(['survey_answer_sort' => $sort])));
        //updated model
        $question = $this->findModel($id);

        return $this->renderAjax('_form', ['question' => $question]);
    }

    public function actionDeleteAnswer($id, $answer)
    {
        $question = $this->findModel($id);
        $answer = $question->answers[$answer];
        $answer->delete();

        //updated model
        $question = $this->findModel($id);

        return $this->renderAjax('_form', ['question' => $question]);
    }

    public function actionUpdateAndClose($id)
    {
        $question = $this->findModel($id);

        $post = \Yii::$app->request->post();

        $questionData = ArrayHelper::getValue($post, "SurveyQuestion.{$question->survey_question_id}");
        if (!empty($questionData) && $question->load($questionData, '')) {
            $question->save();
        }

        $answersData = ArrayHelper::getValue($post, "SurveyAnswer.{$question->survey_question_id}");
        if (!empty($answersData) && Model::loadMultiple($question->answers, $answersData, '')) {
            foreach ($question->answers as $answer) {
                $answer->save();
            }
        }

        return $this->renderAjax('cardView', ['question' => $question]);
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
