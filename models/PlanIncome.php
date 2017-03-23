<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use app\controllers\Utils;

/**
 * This is the model class for table "plan_income".
 *
 * @property integer $id
 * @property string $category
 * @property double $sum
 * @property string $date
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class PlanIncome extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'plan_income';
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
     * is one row on this category
     * @param int $id - id of outgo
     * @return bool
     */
    public static function isOneCategoryPlanIncome($id)
    {
        $planincome = self::findOne($id);
        $count = (new Query())
            ->from( self::tableName() )
            ->where( 'date>=:date_start AND date<=:date_end AND category=:category AND user_id=:user_id',
                array(
                    ':date_start' => Utils::getStartMonth($planincome['date']),
                    ':date_end' => Utils::getFinishMonth($planincome['date']),
                    ':category' => $planincome['category'],
                    ':user_id' => $planincome['user_id'],
                    ) )
            ->count();

        return ( $count > 1 ? false : true );
    }

    /**
     * Get sum PlanIncome in the month
     * @param int $date - date in the month
     * @return mixed - sum
     */
    public static function getSumPlanIncome($date = 0)
    {

        $query = self::find()->select('SUM(sum) as sum');

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($date)]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($date)]);

        return $query->scalar();
    }




}
