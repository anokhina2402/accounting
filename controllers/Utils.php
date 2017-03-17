<?php

namespace app\controllers;

use app\models\Income;
use app\models\PlanIncome;
use app\models\Outgo;
use app\models\PlanOutgo;

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
            return date( "Y-m-01", strtotime( date( 'Y' ) . '-' . date( 'm', strtotime( '+1 month' ) ) . '-01 00:00:00' ) );
        }
        return date( "Y-m-01", strtotime( date( 'Y', strtotime( $date ) ) . '-' . date( 'm', strtotime( '+1 month', strtotime( $date ) ) ) . '-01 00:00:00' ) );
    }

    /**
     * Get Income and Plan Income categories
     * @return array(label)
     */
    public static function getIncomeCategories() {

        $category_income = Income::find()
            ->select(['category as label'])
            ->distinct()
            ->asArray()
            ->all();

        $category_plan_income = PlanIncome::find()
            ->select(['category as label'])
            ->distinct()
            ->asArray()
            ->all();

        $category = array_unique( array_merge($category_income, $category_plan_income), SORT_REGULAR );
        return $category;


    }

    /**
     * Get Outgo and Plan Outgo categories
     * @return array(label)
     */
    public static function getOutgoCategories() {

        $category_outgo = Outgo::find()
            ->select(['category as label'])
            ->distinct()
            ->asArray()
            ->all();

        $category_plan_outgo = PlanOutgo::find()
            ->select(['category as label'])
            ->distinct()
            ->asArray()
            ->all();

        $category = array_unique( array_merge( $category_outgo, $category_plan_outgo ), SORT_REGULAR );
        return $category;


    }


}
