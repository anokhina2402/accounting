<?php

namespace app\controllers;

use app\models\Income;
use app\models\PlanIncome;
use app\models\Outgo;
use app\models\PlanOutgo;
use yii\db\Query;

/**
 * This is the class for utils.
 *
 */
class Utils
{

    /**
     * Get first day of the month
     * @param int $date - date in the month
     * @return false|string - date
     */
    public static function getStartMonth( $date = 0 ) {
        if ( !$date ) {
            return date('Y-m-01');
        }
        else return date('Y-m-01', strtotime($date));
    }

    /**
     * Get last day of the month
     * @param int $date - date in the month
     * @return false|string - date
     */
    public static function getFinishMonth( $date = 0 ) {
        if ( !$date ) {
            return date('Y-m-' . date('t'));
        }
        else return date('Y-m-' . date('t', strtotime( $date ) ), strtotime($date));
    }

    /**
     * Get first day of next month
     * @param int $date - date in the month
     * @return false|string - date
     */
    public static function getStartNextMonth( $date = 0 ) {
        if ( !$date ) {
            return date( "Y-m-01", strtotime( "+1 month" ) );
        }
        return date( "Y-m-01", strtotime( "+1 month", strtotime($date) ) );
    }

    /**
     * Get Income and Plan Income categories
     * @return array(label)
     */
    public static function getIncomeCategories() {

        $income = (new Query())
            ->select('category as label')
            ->from(Income::tableName());

        $plan_income = (new Query())
            ->select('category as label')
            ->from(PlanIncome::tableName());

        $category = (new Query())
            ->select('*')
            ->distinct()
            ->from([$income->union($plan_income)])
            ->orderBy('label')
            ->all();

        return $category;

    }

    /**
     * Get Outgo and Plan Outgo categories
     * @return array(label)
     */
    public static function getOutgoCategories() {

        $outgo = (new Query())
            ->select('category as label')
            ->from(Outgo::tableName());

        $plan_outgo = (new Query())
            ->select('category as label')
            ->from(PlanOutgo::tableName());

        $category = (new Query())
            ->select('*')
            ->distinct()
            ->from([$outgo->union($plan_outgo)])
            ->orderBy('label')
            ->all();

        return $category;


    }


}
