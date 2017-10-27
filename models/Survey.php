<?php

namespace common\modules\survey\models;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "survey".
 *
 * @property integer $survey_id
 * @property string $survey_name
 * @property string $survey_created_at
 * @property string $survey_updated_at
 * @property string $survey_expired_at
 * @property boolean $survey_is_pinned
 * @property boolean $survey_is_closed
 *
 * @property SurveyUserAnswer[] $surveyUserAnswers
 * @property SurveyQuestion[] $questions
 */
class Survey extends \yii\db\ActiveRecord
{

    public $question = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_created_at', 'survey_updated_at', 'survey_expired_at'], 'safe'],
            [['survey_is_pinned', 'survey_is_closed'], 'boolean'],
            [['survey_name'], 'string', 'max' => 45],
            [['survey_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_id' => Yii::t('survey', 'Survey ID'),
            'survey_name' => Yii::t('survey', 'Name'),
            'survey_created_at' => Yii::t('survey', 'Created at'),
            'survey_updated_at' => Yii::t('survey', 'Updated at'),
            'survey_expired_at' => Yii::t('survey', 'Expired at'),
            'survey_is_pinned' => Yii::t('survey', 'Is Pinned'),
            'survey_is_closed' => Yii::t('survey', 'Is Closed'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswers()
    {
        return $this->hasMany(SurveyUserAnswer::className(), ['survey_user_answer_survey_id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(SurveyQuestion::className(), ['survey_question_survey_id' => 'survey_id']);
    }

    public function setQuestions($val)
    {
        return $this->questions = $val;
    }

    static function getDropdownList()
    {
        return ArrayHelper::map(self::find()
            ->where(['>', 'survey_expired_at', new Expression('NOW()')])
            ->orderBy(['survey_created_at' => SORT_ASC])
            ->asArray()->all(), 'survey_id', 'survey_name');
    }
}
