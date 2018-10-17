<?php

namespace onmotion\survey\models;

use Yii;

/**
 * This is the model class for table "survey_user_answer".
 *
 * @property integer $survey_user_answer_id
 * @property integer $survey_user_answer_user_id
 * @property integer $survey_user_answer_survey_id
 * @property integer $survey_user_answer_question_id
 * @property string $survey_user_answer_answer_id
 * @property string $survey_user_answer_value
 * @property string $survey_user_answer_text
 *
 * @property SurveyAnswer $surveyUserAnswerAnswer
 * @property SurveyQuestion $question
 * @property Survey $surveyUserAnswerSurvey
 * @property User $surveyUserAnswerUser
 */
class SurveyUserAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_user_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_user_answer_user_id'], 'required'],
            [['survey_user_answer_id', 'survey_user_answer_user_id', 'survey_user_answer_survey_id', 'survey_user_answer_question_id', 'survey_user_answer_answer_id'], 'integer'],
            [['survey_user_answer_value'], 'string', 'max' => 255],
            [['survey_user_answer_text'], 'string'],

            [['survey_user_answer_value'], 'required', 'when' => function($model){
                /** @var $model SurveyUserAnswer */
                return ($model->question->survey_question_can_skip == false && $model->question->survey_question_type !== SurveyType::TYPE_COMMENT_BOX);
            }, 'message' => \Yii::t('survey', 'You must enter an answer')],

            [['survey_user_answer_text'], 'required', 'when' => function($model){
                /** @var $model SurveyUserAnswer */
                return ($model->question->survey_question_can_skip == false && $model->question->survey_question_type === SurveyType::TYPE_COMMENT_BOX);
            }, 'message' => \Yii::t('survey', 'You must enter an answer')],

            [['survey_user_answer_value', 'survey_user_answer_text'], 'default', 'value' => null],
            [['survey_user_answer_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyAnswer::class, 'targetAttribute' => ['survey_user_answer_answer_id' => 'survey_answer_id']],
            [['survey_user_answer_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyQuestion::class, 'targetAttribute' => ['survey_user_answer_question_id' => 'survey_question_id']],
            [['survey_user_answer_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::class, 'targetAttribute' => ['survey_user_answer_survey_id' => 'survey_id']],
            [['survey_user_answer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass::className(), 'targetAttribute' => ['survey_user_answer_user_id' => 'id']],
        ];
    }

    public function beforeValidate()
    {
        $stat = SurveyStat::getAssignedUserStat(\Yii::$app->user->getId(), $this->question->survey_question_survey_id);
        if ($stat && $stat->survey_stat_is_done){
            return false;
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_user_answer_id' => Yii::t('survey', 'Survey User Answer ID'),
            'survey_user_answer_user_id' => Yii::t('survey', 'Survey User Answer User ID'),
            'survey_user_answer_survey_id' => Yii::t('survey', 'Survey User Answer Survey ID'),
            'survey_user_answer_question_id' => Yii::t('survey', 'Survey User Answer Question ID'),
            'survey_user_answer_answer_id' => Yii::t('survey', 'Survey User Answer Answer ID'),
            'survey_user_answer_value' => Yii::t('survey', 'Answer'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerAnswer()
    {
        return $this->hasOne(SurveyAnswer::class, ['survey_answer_id' => 'survey_user_answer_answer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(SurveyQuestion::class, ['survey_question_id' => 'survey_user_answer_question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerSurvey()
    {
        return $this->hasOne(Survey::class, ['survey_id' => 'survey_user_answer_survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\Yii::$app->user->identityClass::className(), ['id' => 'survey_user_answer_user_id']);
    }


}
