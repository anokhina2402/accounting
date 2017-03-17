<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use app\controllers\Utils;

/**
 * This is the model class for table "income".
 *
 * @property integer $id
 * @property string $category
 * @property double $sum
 * @property string $date
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Income extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'income';
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
     * Get sum Income in the month
     * @param int $date - date in the month
     * @return mixed - sum
     */
    public static function getSumIncome($date = 0)
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
     * @param int $id - id of income
     * @return bool
     */
    public static function isOneCategoryIncome($id)
    {
        $income = self::findOne($id);
        $query = self::find();

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->id,
        ]);

        $query->andFilterWhere(['>=', 'date', Utils::getStartMonth($income['date'])]);
        $query->andFilterWhere(['<=', 'date', Utils::getFinishMonth($income['date'])]);
        $query->andFilterWhere(['category' => $income['category']]);

        return ($query->count() > 1 ? false : true );
    }


}
