<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\controllers\Utils;

/**
 * OutgoSearch represents the model behind the search form about `app\models\Outgo`.
 */
class OutgoSearch extends Outgo
{
    /**
     * @inheritdoc
     */
    public $income_sum;
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'category', 'category2', 'date', 'income_sum'], 'safe'],
            [['sum'], 'number'],
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
        $query = Outgo::find()->select('id, MAX(date) as date, SUM(sum) as sum, category');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //we always filter by user_id and month
        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($this->date)]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($this->date)]);

        $query->groupBy(['category']);


        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied by category
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCategory($params)
    {
        $query = Outgo::find()->select('id, name, MAX(date) as date, SUM(sum) as sum, category, category2');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $one = Outgo::findOne($params['id']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //we always filter by user_id and month
        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($one->date)]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($one->date)]);
        $query->andFilterWhere(['category' => $one->category]);
        $query->groupBy('category2');



        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied by category2
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCategory2($params)
    {
        $query = Outgo::find()->select('id, name, date, sum, category, category2');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $one = Outgo::findOne($params['id']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //we always filter by user_id and month
        $query->andFilterWhere([
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($one->date)]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($one->date)]);
        $query->andFilterWhere(['category' => $one->category]);
        $query->andFilterWhere(['category2' => $one->category2]);



        return $dataProvider;
    }
}
