<?php

namespace app\controllers;

use app\models\PlanIncome;
use app\models\PlanOutgo;
use Yii;
use yii\web\Controller;
use app\models\Income;
use app\models\Outgo;
use app\models\PlanIncomeSearch;
use app\models\PlanOutgoSearch;
use app\models\IncomeSearch;
use app\models\OutgoSearch;
use yii\helpers\Url;


class MainController extends Controller
{
    /**
     * show all plans and real incomes and outfoes on current or selected month for current user
     * @return string
     */
    public function actionIndex()
    {
        Url::remember();
        $current_date = isset(Yii::$app->request->queryParams['date']) ? date('Y-m-d', strtotime(Yii::$app->request->queryParams['date'])) : date('Y-m-d');

        $sum_replace = Income::getSumIncome() - Outgo::getSumOutgo();
        // days replace in current month
        $day_replace =  date('t') - date('d');

        // if money is enough - number will be green
        $class_sum = 'green';
        if ( $sum_replace <= 0 ) {
            //have not money in current month - number will be red
            $class_sum = 'red';
        }
        else if ( $day_replace > 0 && $sum_replace / $day_replace < 100) {
            //have money less than 100 to day, it may not be enough until the end of the month - number will be orange
            $class_sum = 'orange';
        }


        $searchModelPlanIncome = new PlanIncomeSearch();
        $dataProviderPlanIncome = $searchModelPlanIncome->search(array(
                'PlanIncomeSearch' => array(
                    'user_id' => Yii::$app->user->getId(),
                    'date' => $current_date
                )
            )
        );

        $searchModelPlanOutgo = new PlanOutgoSearch();
        $dataProviderPlanOutgo = $searchModelPlanOutgo->search(array(
                'PlanOutgoSearch' => array(
                    'user_id' => Yii::$app->user->getId(),
                    'date' => $current_date
                )
            )
        );

        $searchModelIncome = new IncomeSearch();
        $dataProviderIncome = $searchModelIncome->search(array(
            'IncomeSearch' => array(
                'user_id' => Yii::$app->user->getId(),
                'date' => $current_date
                )
            )
        );

        $searchModelOutgo = new OutgoSearch();
        $dataProviderOutgo = $searchModelOutgo->search(array(
                'OutgoSearch' => array(
                    'user_id' => Yii::$app->user->getId(),
                    'date' => $current_date
                )
            )
        );

        // information message
        $info = '';
        $class_info = '';
        if ( date('t') - date('d') <= 10 ) {
            $info = 'Before the start of next month have little time left, fill in the plan for the next month';
            if ( date('t') - date('d') <= 5 )
                $class_info = 'orange';
            if ( date('t') - date('d') <= 2 )
                $class_info = 'red';
        }

        $sum_planincome = 0;
        if (!empty($dataProviderPlanIncome->getModels())) {
            foreach ($dataProviderPlanIncome->getModels() as $key => $val) {
                $sum_planincome += $val->sum;
            }
        }

        $sum_planoutgo = 0;
        if (!empty($dataProviderPlanOutgo->getModels())) {
            foreach ($dataProviderPlanOutgo->getModels() as $key => $val) {
                $sum_planoutgo += $val->sum;
            }
        }

        $sum_income = 0;
        if (!empty($dataProviderIncome->getModels())) {
            foreach ($dataProviderIncome->getModels() as $key => $val) {
                $sum_income += $val->sum;
            }
        }

        $sum_outgo = 0;
        if (!empty($dataProviderOutgo->getModels())) {
            foreach ($dataProviderOutgo->getModels() as $key => $val) {
                $sum_outgo += $val->sum;
            }
        }


        return $this->render('index', array(
                'sum_replace' =>$sum_replace,
                'day_replace' =>$day_replace,
                'class_sum' => $class_sum,
                'info' => $info,
                'class_info' => $class_info,
                'dataProviderPlanIncome' => $dataProviderPlanIncome,
                'dataProviderPlanOutgo' => $dataProviderPlanOutgo,
                'dataProviderIncome' => $dataProviderIncome,
                'dataProviderOutgo' => $dataProviderOutgo,
                'sum_planincome' => $sum_planincome,
                'sum_planoutgo' => $sum_planoutgo,
                'sum_income' => $sum_income,
                'sum_outgo' => $sum_outgo,
                'current_date' => $current_date,
            )
        );
    }

    /**
     * Export plan from current month to the next
     * @return array(result, message)
     */
    public function actionExportMonthPlan()
    {
        if (Yii::$app->request->isAjax) {
            $is_plan_outgo = (PlanOutgo::find()
                ->where(['user_id' => Yii::$app->user->getId()])
                ->andWhere(['>=', 'date', Utils::getStartMonth(Utils::getStartNextMonth())])
                ->andWhere(['<=', 'date', Utils::getFinishMonth(Utils::getStartNextMonth())])
                ->count() ? true : false);

            $is_plan_income = (PlanIncome::find()
                ->where(['user_id' => Yii::$app->user->getId()])
                ->andWhere(['>=', 'date', Utils::getStartMonth(Utils::getStartNextMonth())])
                ->andWhere(['<=', 'date', Utils::getFinishMonth(Utils::getStartNextMonth())])
                ->count() ? true : false);

            if ($is_plan_outgo || $is_plan_income) {

                // if plan is exists - do not export
                Yii::$app->response->format = 'json';
                return ['message' => 'Plan for the next month exists!', 'class'=>'alert-danger'];

            }

            $plan_income = PlanIncome::find()
                ->select('category, sum, date, user_id')
                ->where(['user_id' => Yii::$app->user->getId()])
                ->andWhere(['>=', 'date', Utils::getStartMonth(date('Y-m-d'))])
                ->andWhere(['<=', 'date', Utils::getFinishMonth(date('Y-m-d'))])
                ->asArray()
                ->all();
            foreach ($plan_income as $key => $value) {
                $new_plan_income = new PlanIncome($value);
                $new_plan_income->date = Utils::getStartNextMonth();
                $new_plan_income->save();
            }
            $plan_outgo = PlanOutgo::find()
                ->select('category, sum, date, user_id')
                ->where(['user_id' => Yii::$app->user->getId()])
                ->andWhere(['>=', 'date', Utils::getStartMonth(date('Y-m-d'))])
                ->andWhere(['<=', 'date', Utils::getFinishMonth(date('Y-m-d'))])
                ->asArray()
                ->all();
            foreach ($plan_outgo as $key => $value) {
                $new_plan_outgo = new PlanOutgo($value);
                $new_plan_outgo->date = Utils::getStartNextMonth();
                $new_plan_outgo->save();
            }
            Yii::$app->response->format = 'json';
            return ['message' => 'Plan was exported successfully', 'class'=>'alert-success'];
        }
    }

}
