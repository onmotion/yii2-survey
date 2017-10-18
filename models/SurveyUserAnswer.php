<?php

namespace common\modules\survey\models;

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
 *
 * @property SurveyAnswer $surveyUserAnswerAnswer
 * @property SurveyQuestion $surveyUserAnswerQuestion
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
            [['survey_user_answer_id'], 'required'],
            [['survey_user_answer_id', 'survey_user_answer_user_id', 'survey_user_answer_survey_id', 'survey_user_answer_question_id', 'survey_user_answer_answer_id'], 'integer'],
            [['survey_user_answer_value'], 'string', 'max' => 255],
            [['survey_user_answer_answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyAnswer::className(), 'targetAttribute' => ['survey_user_answer_answer_id' => 'survey_answer_id']],
            [['survey_user_answer_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyQuestion::className(), 'targetAttribute' => ['survey_user_answer_question_id' => 'survey_question_id']],
            [['survey_user_answer_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_user_answer_survey_id' => 'survey_id']],
            [['survey_user_answer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['survey_user_answer_user_id' => 'id']],
        ];
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
            'survey_user_answer_value' => Yii::t('survey', 'Survey User Answer Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerAnswer()
    {
        return $this->hasOne(SurveyAnswer::className(), ['survey_answer_id' => 'survey_user_answer_answer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerQuestion()
    {
        return $this->hasOne(SurveyQuestion::className(), ['survey_question_id' => 'survey_user_answer_question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerSurvey()
    {
        return $this->hasOne(Survey::className(), ['survey_id' => 'survey_user_answer_survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswerUser()
    {
        return $this->hasOne(User::className(), ['id' => 'survey_user_answer_user_id']);
    }
}
