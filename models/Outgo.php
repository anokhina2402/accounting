<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use app\controllers\Utils;

/**
 * This is the model class for table "outgo".
 *
 * @property integer $id
 * @property string $name
 * @property string $category
 * @property string $category2
 * @property double $sum
 * @property string $date
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Outgo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outgo';
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
            [['category2'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category' => 'Category',
            'category2' => 'Category2',
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
     * Get sum outgo in the month
     * @param int $date - date in the month
     * @return mixed - sum
     */
    public static function getSumOutgo($date = 0)
    {
        $query = self::find()->select('SUM(sum) as sum');

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($date)]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($date)]);

        return $query->scalar();
    }

    /**
     * Is one row on this category
     * @param int $id - id of outgo
     * @return bool
     */
    public static function isOneCategoryOutgo($id)
    {
        $outgo = self::findOne($id);
        $count = (new Query())
            ->select( 'id' )
            ->from( self::tableName() )
            ->where( 'date>=:date_start AND date<=:date_end AND category=:category AND user_id=:user_id',
                array(
                    ':date_start' => Utils::getStartMonth($outgo['date']),
                    ':date_end' => Utils::getFinishMonth($outgo['date']),
                    ':category' => $outgo['category'],
                    ':user_id' => $outgo['user_id'],
                    ) )
            ->count();

        return ( $count > 1 ? false : true );
    }

    /**
     * is one row on this category2
     * @param int $id - id of outgo
     * @return bool
     */
    public static function isOneCategory2Outgo($id)
    {
        $outgo = self::findOne($id);
        $count = (new Query())
            ->select( 'id' )
            ->from( self::tableName() )
            ->where( 'date>=:date_start AND date<=:date_end AND category2=:category2 AND user_id=:user_id',
                array(
                    ':date_start' => Utils::getStartMonth($outgo['date']),
                    ':date_end' => Utils::getFinishMonth($outgo['date']),
                    ':category2' => $outgo['category2'],
                    ':user_id' => $outgo['user_id'],
                    ) )
            ->count();

        return ( $count > 1 ? false : true );
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
