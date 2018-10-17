<?php

namespace onmotion\survey\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "survey_answer".
 *
 * @property string $survey_answer_id
 * @property string $survey_answer_name
 * @property string $survey_answer_descr
 * @property string $survey_answer_class
 * @property string $survey_answer_comment
 * @property integer $survey_answer_question_id
 * @property integer $survey_answer_sort
 * @property integer $survey_answer_points
 * @property boolean $survey_answer_show_descr
 *
 * @property SurveyQuestion $question
 * @property SurveyUserAnswer[] $userAnswers
 */
class SurveyAnswer extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_answer_descr'], 'string'],
            [['survey_answer_question_id', 'survey_answer_sort', 'survey_answer_points'], 'integer'],
            [['survey_answer_question_id', 'survey_answer_sort', 'survey_answer_points'], 'filter', 'filter' => 'intval'],
            [['survey_answer_show_descr'], 'boolean'],
            [['survey_answer_show_descr'], 'filter', 'filter' => 'boolval'],
            [['survey_answer_name'], 'string', 'max' => 100],
            [['survey_answer_name'], 'required'],
            [['survey_answer_class', 'survey_answer_comment'], 'string', 'max' => 255],
            [['survey_answer_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyQuestion::class, 'targetAttribute' => ['survey_answer_question_id' => 'survey_question_id']],
        ];
    }



    public function afterDelete()
    {
        $question = $this->question;
        if (!empty($question)) {
            $answersCount = $question->getAnswers()->count();
            if ($answersCount == 0) {
                //prevent deleting last answer
                $question->link('answers', (new SurveyAnswer(['survey_answer_sort' => 0])));
            }
        }

        return parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_answer_id' => Yii::t('survey', 'Answer ID'),
            'survey_answer_name' => Yii::t('survey', 'Answer'),
            'survey_answer_descr' => Yii::t('survey', 'Detailed description'),
            'survey_answer_show_descr' => Yii::t('survey', 'Show detailed description'),
            'survey_answer_class' => Yii::t('survey', 'Class'),
            'survey_answer_comment' => Yii::t('survey', 'Comment'),
            'survey_answer_question_id' => Yii::t('survey', 'Question ID'),
            'survey_answer_points' => Yii::t('survey', 'Points'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(SurveyQuestion::class, ['survey_question_id' => 'survey_answer_question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAnswers()
    {
        return $this->hasMany(SurveyUserAnswer::class, ['survey_user_answer_answer_id' => 'survey_answer_id']);
    }

    /**
     * Returns the total number of users voted for this answer
     *
     * @return int
     */
    public function getTotalUserAnswersCount()
    {
        switch ($this->question->survey_question_type){
            case SurveyType::TYPE_MULTIPLE:
                $result = SurveyUserAnswer::find()->where(['survey_user_answer_answer_id' => $this->survey_answer_id])
                    ->andWhere(['survey_user_answer_value' => 1])
                    ->count();
                break;
            case SurveyType::TYPE_ONE_OF_LIST:
            case SurveyType::TYPE_DROPDOWN:
                $result = SurveyUserAnswer::find()->andWhere(['survey_user_answer_value' => $this->survey_answer_id])
                    ->count();
                break;
            case SurveyType::TYPE_RANKING:

                $result = (new Query())
                    ->from(SurveyUserAnswer::tableName())
                    ->addSelect(['AVG(survey_user_answer_value) average'])
                    ->where(['survey_user_answer_answer_id' => $this->survey_answer_id])
                    ->andWhere(['>', 'survey_user_answer_value', 0])
                    ->groupBy(['survey_user_answer_answer_id'])->scalar();
                break;
            case SurveyType::TYPE_SLIDER:
                $result = (new Query())
                    ->from(SurveyUserAnswer::tableName())
                    ->addSelect(['AVG(survey_user_answer_value) average'])
                    ->andWhere(['survey_user_answer_question_id' => $this->question->survey_question_id])
                    ->andWhere(['is not', 'survey_user_answer_value', null])
                    ->groupBy(['survey_user_answer_question_id'])->scalar();
                break;
            default:
                $result = 0;
                break;
        }

        return $result;

    }
}
