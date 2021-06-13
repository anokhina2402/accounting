<?php

namespace app\models;

use yii\db\Query;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use app\controllers\Utils;

/**
 * This is the model class for table "plan_outgo".
 *
 * @property integer $id
 * @property string $category
 * @property double $sum
 * @property string $date
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class PlanOutgo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plan_outgo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'date', 'sum'], 'required'],
            [['sum'], 'number'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'sum' => 'Sum',
            'date' => 'Date',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'user_id',
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * Is one row on this category
     * @param int $id - id of outgo
     * @return bool
     */
    public static function isOneCategoryPlanOutgo($id)
    {
        $planoutgo = self::findOne($id);
        $count = (new Query())
            ->from( self::tableName() )
            ->where( 'date>=:date_start AND date<=:date_end AND category=:category AND user_id=:user_id',
                array(
                    ':date_start' => Utils::getStartMonth($planoutgo['date']),
                    ':date_end' => Utils::getFinishMonth($planoutgo['date']),
                    ':category' => $planoutgo['category'],
                    ':user_id' => $planoutgo['user_id'],
                ) )
            ->count();

        return ( $count > 1 ? false : true );
    }

    /**
     * Get planned sum by parameters
     *
     * @param array $params(user_id, date, category)
     *
     * @return float
     */
    public static function getSum($params)
    {
        $query = self::find()->select('SUM(sum) as sum');

        //we always filter by user_id and month
        if ( isset($params['user_id']) && $params['user_id']) {
            $query->andFilterWhere([
                'user_id' => $params['user_id'],
            ]);
        }

        if ( isset($params['date']) && $params['date']) {
            $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($params['date'])]);
            $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($params['date'])]);
        }
        if ( isset($params['category']) && $params['category']) {
            $query->andFilterWhere(['category' => $params['category']]);
        }


        return $query->scalar();
    }

    /**
     * Get sum by parameters without one ID
     *
     * @param array $params(user_id, date, category, id)
     *
     * @return float
     */
    public static function getSumWithoutId($params)
    {
        $query = self::find()->select('SUM(sum) as sum');

        //we always filter by user_id and month
        if ( isset($params['user_id']) && $params['user_id']) {
            $query->andFilterWhere([
                'user_id' => $params['user_id'],
            ]);
        }

        if ( isset($params['date']) && $params['date']) {
            $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($params['date'])]);
            $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($params['date'])]);
        }
        if ( isset($params['category']) && $params['category']) {
            $query->andFilterWhere(['category' => $params['category']]);
        }

        if ( isset($params['id']) && $params['id']) {
            $query->andFilterWhere(['<>', 'id', $params['id']]);
        }

        return $query->scalar();
    }




}
