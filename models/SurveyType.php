<?php

namespace onmotion\survey\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "survey_type".
 *
 * @property integer $survey_type_id
 * @property string $survey_type_name
 * @property string $survey_type_descr
 *
 * @property SurveyQuestion[] $surveyQuestions
 */
class SurveyType extends \yii\db\ActiveRecord
{
    const TYPE_MULTIPLE = 1;
    const TYPE_ONE_OF_LIST = 2;
    const TYPE_DROPDOWN = 3;
    const TYPE_RANKING = 4;
    const TYPE_SLIDER = 5;
    const TYPE_SINGLE_TEXTBOX = 6;
    const TYPE_MULTIPLE_TEXTBOX = 7;
    const TYPE_COMMENT_BOX = 8;
    const TYPE_DATE_TIME = 9;
    const TYPE_CALENDAR = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_type_name'], 'string', 'max' => 45],
            [['survey_type_descr'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_type_id' => Yii::t('survey', 'Survey Type ID'),
            'survey_type_name' => Yii::t('survey', 'Type'),
            'survey_type_descr' => Yii::t('survey', 'Description'),
        ];
    }

    public static function getDropdownList()
    {
        return ArrayHelper::map(SurveyType::find()
            ->asArray()->all(), 'survey_type_id', function($model){
            return \Yii::t('survey', $model['survey_type_name']);
        });
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyQuestions()
    {
        return $this->hasMany(SurveyQuestion::class, ['survey_question_type' => 'survey_type_id']);
    }
}
