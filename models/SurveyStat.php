<?php

namespace common\modules\survey\models;

use Yii;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "survey_stat".
 *
 * @property integer $survey_stat_id
 * @property integer $survey_stat_survey_id
 * @property integer $survey_stat_user_id
 * @property string $survey_stat_assigned_at
 * @property string $survey_stat_started_at
 * @property string $survey_stat_updated_at
 * @property string $survey_stat_ended_at
 * @property string $survey_stat_ip
 * @property boolean $survey_stat_is_done
 *
 * @property Survey $survey
 * @property User $user
 */
class SurveyStat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_stat';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['survey_stat_assigned_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['survey_stat_updated_at'],
                ],
                'createdAtAttribute' => 'survey_stat_assigned_at',
                'updatedAtAttribute' => 'survey_stat_updated_at',
                'skipUpdateOnClean' => true,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_stat_survey_id', 'survey_stat_user_id'], 'integer'],
            [['survey_stat_assigned_at', 'survey_stat_started_at', 'survey_stat_updated_at', 'survey_stat_ended_at'], 'safe'],
            [['survey_stat_is_done'], 'boolean'],
            [['survey_stat_ip'], 'string', 'max' => 45],
            [['survey_stat_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_stat_survey_id' => 'survey_id']],
            [['survey_stat_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \Yii::$app->user->identityClass, 'targetAttribute' => ['survey_stat_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_stat_id' => Yii::t('survey', 'Survey Stat ID'),
            'survey_stat_survey_id' => Yii::t('survey', 'Survey Stat Survey ID'),
            'survey_stat_user_id' => Yii::t('survey', 'Survey Stat User ID'),
            'survey_stat_assigned_at' => Yii::t('survey', 'Survey Stat Assigned At'),
            'survey_stat_started_at' => Yii::t('survey', 'Survey Stat Started At'),
            'survey_stat_updated_at' => Yii::t('survey', 'Survey Stat Updated At'),
            'survey_stat_ended_at' => Yii::t('survey', 'Survey Stat Ended At'),
            'survey_stat_ip' => Yii::t('survey', 'Survey Stat Ip'),
            'survey_stat_is_done' => Yii::t('survey', 'Survey Stat Is Done'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Survey::className(), ['survey_id' => 'survey_stat_survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        $userClass = \Yii::$app->user->identityClass;
        return $this->hasOne($userClass, ['id' => 'survey_stat_user_id']);
    }

    /**
     * @param $userId
     * @param $surveyId
     * @return bool
     * @throws UserException
     */
    static function assignUser($userId, $surveyId){

        $user = Survey::findOne($surveyId);
        if (!$user) {
            throw new UserException('survey does not exist');
        }

        /** @var \common\modules\user\models\User $User */
        $User = \Yii::$app->user->identityClass;
        $user = $User::findOne($userId);
        if (!$user) {
            throw new UserException('user does not exist');
        }

        $isAssigned = SurveyStat::find()->where(['survey_stat_survey_id' => $surveyId])
            ->andWhere(['survey_stat_user_id' => $userId])->count();
        if ($isAssigned){
            throw new UserException('user already assigned', 1001);
        }

        $surveyStat = new SurveyStat();
        $surveyStat->survey_stat_user_id = $userId;
        $surveyStat->survey_stat_survey_id = $surveyId;
        return $surveyStat->save(false);
    }

    static function unassignUser($userId, $surveyId){

        $user = Survey::findOne($surveyId);
        if (!$user) {
            throw new UserException('survey does not exist');
        }

        /** @var \common\modules\user\models\User $User */
        $User = \Yii::$app->user->identityClass;
        $user = $User::findOne($userId);
        if (!$user) {
            throw new UserException('user does not exist');
        }

        return SurveyStat::deleteAll(['survey_stat_survey_id' => $surveyId, 'survey_stat_user_id' => $userId]);
    }
}
