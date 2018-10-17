<?php

namespace onmotion\survey\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use onmotion\survey\models\Survey;

/**
 * SurveySearch represents the model behind the search form about `onmotion\survey\models\Survey`.
 */
class SurveySearch extends Survey
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['survey_id', 'survey_badge_id'], 'integer'],
            [['survey_name', 'survey_created_at', 'survey_updated_at', 'survey_expired_at'], 'safe'],
            [['survey_is_pinned', 'survey_is_closed'], 'boolean'],
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
        $query = Survey::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['defaultPageSize' => 10],
            'sort' => ['defaultOrder' => ['survey_created_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'survey_id' => $this->survey_id,
            'survey_created_at' => $this->survey_created_at,
            'survey_updated_at' => $this->survey_updated_at,
            'survey_expired_at' => $this->survey_expired_at,
            'survey_is_pinned' => $this->survey_is_pinned,
            'survey_is_closed' => $this->survey_is_closed,
        ]);

        $query->andFilterWhere(['like', 'survey_name', $this->survey_name]);

        return $dataProvider;
    }
}
