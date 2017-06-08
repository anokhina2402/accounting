<?php

namespace app\models;

use yii;
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
            'pagination' => false,
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
            'pagination' => [
                'pageSize' => false,
            ],
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
            'pagination' => [
                'pageSize' => false,
            ],
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
        $query->orderBy('date DESC, name ASC');



        return $dataProvider;
    }

    /**
     * Creates data provider instance with statistic query applied by type: category, category2, name
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchStatistic($params)
    {
        $fields = 'id, name, date, sum, category, category2';
        if ($params['type'] =='category') {
            $fields = 'id, name, MAX(date) as date, SUM(sum) as sum, category, category2';
        }
        $query = Outgo::find()->select($fields);


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        //we always filter by user_id
        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);
        $query->andFilterWhere(['>=', 'date', $params['date_start']]);
        $query->andFilterWhere(['<=', 'date', $params['date_end']]);
        $query->orderBy('date DESC');
        $query->groupBy('');

        if ($params['type'] == 'category') {
            $query->andFilterWhere(['category' => $params['category']]);
            $query->groupBy('category2');
            $query->orderBy('category2');
        }
        else if ($params['type'] == 'category2') {
            $query->andFilterWhere(['category2' => $params['category']]);
        }
        else if ($params['type'] == 'name') {
            $query->andFilterWhere(['name' => $params['category']]);
        }

        if (count($dataProvider->getModels()) == 1 && $dataProvider->getModels()[0]->category2 == '' && $params['type'] == 'category') {
            $query->select('id, name, date, sum, category, category2');
            $query->groupBy('');
            $query->orderBy('date DESC');
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);


        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with  all statistic query of income and outgo on date
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAllStatistic($params)
    {
        if ($params['by_category']){
            $select_outgo = 'id, MAX(date) AS date, -SUM(sum) AS sum, category, category2, name';
            $group = 'GROUP BY category';
        }
        else {
            $select_outgo = 'id, date, -(sum) as sum, category, category2, name';
            $group = '';
        }

        $query = Outgo::findBySql("
            SELECT id, date, sum, category, category2, name
            FROM 
             (SELECT $select_outgo 
              FROM outgo 
              WHERE user_id=" .  Yii::$app->user->id . " AND date>='" . Utils::getStartMonth($params['date']) ."' AND date<='" . $params['date'] ."' " . $group ."
              UNION
              SELECT id, date, sum, category, '' AS category2, '' AS name
              FROM income 
              WHERE user_id=" .  Yii::$app->user->id . " AND date>='" . Utils::getStartMonth($params['date']) ."' AND date<='" . $params['date'] ."'
             ) AS income_outgo ORDER BY " . ($params['by_category'] ? 'category' : 'date DESC'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $dataProvider;
    }
}
