<?php

namespace onmotion\survey\models;

use Yii;
use yii\db\conditions\AndCondition;
use yii\db\conditions\OrCondition;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "survey".
 *
 * @property integer $survey_id
 * @property integer $survey_created_by
 * @property string $survey_name
 * @property string $survey_created_at
 * @property string $survey_updated_at
 * @property string $survey_expired_at
 * @property boolean $survey_is_pinned
 * @property boolean $survey_is_closed
 * @property boolean $survey_is_private
 * @property boolean $survey_is_visible
 * @property integer $survey_wallet
 * @property integer $survey_status
 * @property integer $survey_time_to_pass
 * @property integer $survey_badge_id
 * @property string $survey_tags
 * @property string $survey_image
 * @property string $survey_descr
 *
 * @property SurveyUserAnswer[] $surveyUserAnswers
 * @property SurveyQuestion[] $questions
 * @property SurveyStat[] $stats
 * @property-read User[] $restrictedUsers
 * @property Badge $badge
 *
 * @property-read int[] $restrictedUserIds
 * @property-read string[] $restrictedUserNames
 * @property-read boolean $isAccessibleByCurrentUser
 */
class Survey extends \yii\db\ActiveRecord
{

    public $question = null;
    public $imageFile = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey';
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->survey_created_by = \Yii::$app->user->getId();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_created_at', 'survey_updated_at', 'survey_expired_at'], 'safe'],
            [['survey_is_pinned', 'survey_is_closed', 'survey_is_private', 'survey_is_visible'], 'boolean'],
            [['survey_name'], 'string', 'max' => 45],
            [['survey_descr'], 'string'],
            [['survey_tags', 'survey_image'], 'string', 'max' => 255],
            [['survey_name'], 'required'],
            [['survey_wallet', 'survey_status', 'survey_created_by', 'survey_time_to_pass', 'survey_badge_id'], 'integer'],
            [['imageFile'], 'file', 'mimeTypes' => 'image/jpeg, image/png', 'maxSize' => 5000000]
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
            'survey_is_pinned' => Yii::t('survey', 'Pinned'),
            'survey_is_closed' => Yii::t('survey', 'Closed'),
            'survey_is_private' => Yii::t('survey', 'Private'),
            'survey_is_visible' => Yii::t('survey', 'Visible'),
            'survey_wallet' => Yii::t('survey', 'Price'),
            'survey_tags' => Yii::t('survey', 'Tags'),
            'survey_descr' => Yii::t('survey', 'Description'),
            'survey_time_to_pass' => Yii::t('survey', 'Time to pass'),
            'restrictedUserIds' => Yii::t('survey', 'Restricted users'),
            'imageFile' => '',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyUserAnswers()
    {
        return $this->hasOne(SurveyUserAnswer::class, ['survey_user_answer_survey_id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(SurveyQuestion::class, ['survey_question_survey_id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStats()
    {
        return $this->hasMany(SurveyStat::class, ['survey_stat_survey_id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRestrictedUsers()
    {
        return $this->hasMany(Yii::$app->user->identityClass, ['id' => 'survey_restricted_user_user_id'])
	        ->viaTable('survey_restricted_user', ['survey_restricted_user_survey_id' => 'survey_id']);
    }

    public function getStatus()
    {
        if (isset($this->survey_expired_at) && strtotime($this->survey_expired_at) < time()) {
            $status = 'expired';
        } else {
            $status = $this->survey_is_closed ? 'closed' : 'active';
        }
        return $status;
    }

    public function setQuestions($val)
    {
        return $this->questions = $val;
    }

    public function getRespondentsCount()
    {
        return SurveyStat::find()->where(['survey_stat_survey_id' => $this->survey_id])->count();
    }

    public function getCompletedRespondentsCount()
    {
        return SurveyStat::find()->where(['survey_stat_survey_id' => $this->survey_id])
            ->andWhere(['survey_stat_is_done' => true])
            ->count();
    }

    static function getDropdownList()
    {
        return ArrayHelper::map(self::find()
	        ->leftJoin('survey_restricted_user', 'survey_id = survey_restricted_user_survey_id')
            ->where(new AndCondition([
	            ['survey_is_visible' => 1],
            	new OrCondition([
		            ['>', 'survey_expired_at', new Expression('NOW()')],
		            ['survey_expired_at' => null]
	            ]),
	            new OrCondition([
	            	['survey_is_private' => 0],
		            ['survey_restricted_user_user_id' => \Yii::$app->user->id]
	            ])
            ]))
            ->orderBy(['survey_created_at' => SORT_ASC])
            ->asArray()->all(), 'survey_id', 'survey_name');
    }

    /**
     * @return string
     */
    public function getImage()
    {
        $file = !empty($this->survey_image) ? $this->survey_image : null;

        if (empty($file)) {
            return null;
        }
        $module = \Yii::$app->getModule('survey');
        $basepath = $module->params['uploadsUrl'];
        $path = $basepath . '/' . $this->survey_image;

        return $path;
    }

    public function getAuthorName()
    {
        try {
            $userClass = \Yii::$app->user->identityClass;
            $author = $userClass::findOne($this->survey_created_by);
            if ($author) {
                return $author->username;
            } else {
                return null;
            }
        } catch (\Throwable $e) {
            return 'undefined';
        }
    }

    public function getRestrictedUserIds() {
    	return ArrayHelper::map($this->restrictedUsers, 'id', 'id');
    }

    public function getRestrictedUserNames() {
    	return ArrayHelper::map($this->restrictedUsers, 'id', 'fullname');
    }

    public function isAccessibleBy($userId) {
	    return ($this->survey_is_visible && (!$this->survey_is_private || in_array($userId, $this->restrictedUserIds)));
    }

    public function getIsAccessibleByCurrentUser() {
    	return $this->isAccessibleBy(\Yii::$app->user->id);
    }
}
