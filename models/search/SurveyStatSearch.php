<?php

namespace onmotion\survey\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use onmotion\survey\models\SurveyStat;

/**
 * SurveyStatSearch represents the model behind the search form about `onmotion\survey\models\SurveyStat`.
 */
class SurveyStatSearch extends SurveyStat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_stat_id', 'survey_stat_survey_id', 'survey_stat_user_id'], 'integer'],
            [['survey_stat_assigned_at', 'survey_stat_started_at', 'survey_stat_updated_at', 'survey_stat_ended_at', 'survey_stat_ip'], 'safe'],
            [['survey_stat_is_done'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SurveyStat::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query->joinWith('survey'),
            'sort' => ['defaultOrder' => ['survey_stat_assigned_at' => SORT_DESC]]

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'survey_stat_id' => $this->survey_stat_id,
            'survey_stat_survey_id' => $this->survey_stat_survey_id,
            'survey_stat_user_id' => $this->survey_stat_user_id,
            'survey_stat_assigned_at' => $this->survey_stat_assigned_at,
            'survey_stat_started_at' => $this->survey_stat_started_at,
            'survey_stat_updated_at' => $this->survey_stat_updated_at,
            'survey_stat_ended_at' => $this->survey_stat_ended_at,
            'survey_stat_is_done' => $this->survey_stat_is_done,
        ]);

        $query->andFilterWhere(['like', 'survey_stat_ip', $this->survey_stat_ip]);

        return $dataProvider;
    }
}
