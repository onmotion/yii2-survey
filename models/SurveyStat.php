<?php

namespace onmotion\survey\models;

use app\modules\user\models\User;
use Yii;
use yii\base\Event;
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
 * @property string $survey_stat_hash
 *
 * @property Survey $survey
 * @property User $user
 */
class SurveyStat extends \yii\db\ActiveRecord
{
    const EVENT_SURVEY_AFTER_COMPLETE = 'surveyAfterComplete';
    const EVENT_SURVEY_HAS_BEEN_ASSIGN = 'surveyHasBeenAssign';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_stat';
    }

    public function beforeSave($insert)
    {
        if ($insert){
            $this->survey_stat_hash = self::generateHash($this->survey_stat_survey_id, $this->survey_stat_user_id);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert){
            \Yii::$app->trigger(self::EVENT_SURVEY_HAS_BEEN_ASSIGN, new Event(['sender' => $this]));
        }

        if (isset($changedAttributes['survey_stat_is_done']) && $changedAttributes['survey_stat_is_done'] === false){
            Event::trigger(self::class, self::EVENT_SURVEY_AFTER_COMPLETE, new Event(['sender' => $this]));
            \Yii::$app->trigger(self::EVENT_SURVEY_AFTER_COMPLETE, new Event(['sender' => $this]));
        }
    }

    /**
     * @param $survey_stat_survey_id
     * @param $survey_stat_user_id
     * @return string
     */
    static function generateHash($survey_stat_survey_id, $survey_stat_user_id){
        return md5($survey_stat_survey_id . $survey_stat_user_id);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
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
            [['survey_stat_survey_id', 'survey_stat_user_id', 'survey_stat_hash'], 'required'],
            [['survey_stat_survey_id', 'survey_stat_user_id'], 'integer'],
            [['survey_stat_assigned_at', 'survey_stat_started_at', 'survey_stat_updated_at', 'survey_stat_ended_at'], 'safe'],
            [['survey_stat_is_done'], 'boolean'],
            [['survey_stat_ip'], 'string', 'max' => 45],
            [['survey_stat_hash'], 'string', 'max' => 32],
            [['survey_stat_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::class, 'targetAttribute' => ['survey_stat_survey_id' => 'survey_id']],
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
        return $this->hasOne(Survey::class, ['survey_id' => 'survey_stat_survey_id']);
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

        /** @var \app\modules\user\models\User $User */
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
        if ($surveyStat->save(false)){

            return true;
        }else {
            return false;
        }
    }

    static function unassignUser($userId, $surveyId){

        $user = Survey::findOne($surveyId);
        if (!$user) {
            throw new UserException('survey does not exist');
        }

        /** @var \app\modules\user\models\User $User */
        $User = \Yii::$app->user->identityClass;
        $user = $User::findOne($userId);
        if (!$user) {
            throw new UserException('user does not exist');
        }

        return SurveyStat::deleteAll(['survey_stat_survey_id' => $surveyId, 'survey_stat_user_id' => $userId]);
    }

    /**
     * @param $userId
     * @param $surveyId
     * @return array|null|SurveyStat
     * @throws UserException
     */
    static function getAssignedUserStat($userId, $surveyId){

        $user = Survey::findOne($surveyId);
        if (!$user) {
            throw new UserException('survey does not exist');
        }

        /** @var \app\modules\user\models\User $User */
        $User = \Yii::$app->user->identityClass;
        $user = $User::findOne($userId);
        if (!$user) {
            throw new UserException('user does not exist');
        }

        $result = SurveyStat::find()->where(['survey_stat_survey_id' => $surveyId])
            ->andWhere(['survey_stat_user_id' => $userId])->one();

        return $result;
    }
}
