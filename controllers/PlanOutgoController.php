<?php

namespace app\controllers;

use app\models\PlanIncome;
use Yii;
use app\models\PlanOutgo;
use app\models\PlanOutgoSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * PlanOutgoController implements the CRUD actions for PlanOutgo model.
 */
class PlanOutgoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PlanOutgo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlanOutgoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all PlanIncome models on category.
     * @return mixed
     */
    public function actionIndexcategory()
    {
        Url::remember();
        $searchModel = new PlanOutgoSearch();
        $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);
        $one = PlanOutgo::findOne(Yii::$app->request->queryParams['id']);

        return $this->render('indexcategory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $one->category
        ]);
    }



    /**
     * Displays a single PlanOutgo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PlanOutgo model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlanOutgo();

        $categories = Utils::getOutgoCategories();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            $model->category = ( isset(Yii::$app->request->queryParams['category']) ? Yii::$app->request->queryParams['category'] : '' );
            $model->date = date('Y-m-d');
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Updates an existing PlanOutgo model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $categories = Utils::getOutgoCategories();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Updates an existing one category Plan Outgo model.
     * If exists more than one row on this category - go to the table of rows of this category
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatecategory($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            if ( PlanOutgo::isOneCategoryPlanOutgo( $id ) ) {
                //If this month there is only one entry in this category - just to update
                $categories = Utils::getOutgoCategories();
                return $this->render('update', [
                    'model' => $model,
                    'categories' => $categories,
                ]);

            }
            else {
                //If more than one record - show the table of these Incomes
                return $this->redirect(Yii::$app->getUrlManager()->createUrl(['plan-outgo/indexcategory', 'id' => $id]));
            }
        }
    }

    /**
     * Deletes an existing PlanOutgo model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * Deletes an existing one category Plan Outgo models.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecategory($id)
    {
        $planoutgo= PlanOutgo::findOne($id);
        PlanOutgo::deleteAll(['AND',
            'category=:category',
            'date>=:date_start',
            'date<=:date_finish',
            'user_id=:user_id'
        ], [
            ':category' => $planoutgo->category,
            ':date_start' => Utils::getStartMonth($planoutgo->date),
            ':date_finish' => Utils::getFinishMonth($planoutgo->date),
            ':user_id' => $planoutgo->user_id
        ]);

        return $this->goBack();
    }

    /**
     * check saved PlanOutgo from request params: sum, date, id
     * @return array|string - json array of error or success
     */
    public function actionValidate(){

        if (Yii::$app->request->isAjax) {

            //$category, $sum, $date, $id;
            $sum = Yii::$app->getRequest()->getQueryParam('sum');
            $date = Yii::$app->getRequest()->getQueryParam('date');
            $id = Yii::$app->getRequest()->getQueryParam('id');


            // check if plan outgo more than plan income
            $sum_income = PlanIncome::getSumPlanIncome($date);
            $sum_without_id = PlanOutgo::getSumWithoutId([
                'date' => $date,
                'id' => $id,
                'user_id' => Yii::$app->user->id
            ]);
            if ($sum_income < $sum_without_id + $sum) {
                return Json::encode([
                    'result' => 'error',
                    'message' => 'Expenses for the month more plan income by ' . ( $sum_without_id + $sum - $sum_income ) . '!'
                ]);
            }
            //check if day of started month
            if ($date < Utils::getStartMonth()) {
                return Json::encode([
                    'result' => 'error',
                    'message' => 'Selected day of started month!'
                ]);
            }

            return Json::encode([
                'result' => 'success',
                'message' => '',
            ]);
        }
    }



    /**
     * Finds the PlanOutgo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlanOutgo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlanOutgo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
