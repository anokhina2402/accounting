<?php

namespace app\controllers;

use app\models\Income;
use app\models\PlanOutgo;
use Yii;
use app\models\Outgo;
use app\models\OutgoSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * OutgoController implements the CRUD actions for Outgo model.
 */
class OutgoController extends Controller
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
     * Lists all Outgo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OutgoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Outgo models on one category.
     * @return mixed
     */
    public function actionIndexcategory()
    {
        Url::remember();
        $searchModel = new OutgoSearch();
        $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);
        $one = Outgo::findOne(Yii::$app->request->queryParams['id']);

        return $this->render('indexcategory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $one->category
        ]);
    }

    /**
     * Lists all Outgo models on one category2.
     * @return mixed
     */
    public function actionIndexcategory2()
    {
        Url::remember();
        $searchModel = new OutgoSearch();
        $dataProvider = $searchModel->searchCategory2(Yii::$app->request->queryParams);
        $one = Outgo::findOne(Yii::$app->request->queryParams['id']);

        return $this->render('indexcategory2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $one->category,
            'category2' => $one->category2
        ]);
    }

    /**
     * Displays a single Outgo model.
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
     * get all outgo category2
     * @return array(label)
     */
    public static function getOutgoCategories2() {

        $category2 = Outgo::find()
            ->select(['category2 as label'])
            ->distinct()
            ->asArray()
            ->all();
        return $category2;
    }

    /**
     * get names outgo
     * @return array(label)
     */
    public static function getNames() {

        $category2 = Outgo::find()
            ->select(['name as label'])
            ->distinct()
            ->asArray()
            ->all();
        return $category2;
    }



    /**
     * Creates a new Outgo model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Outgo();

        $categories = Utils::getOutgoCategories();
        $categories2 = self::getOutgoCategories2();
        $names = self::getNames();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            $model->category = ( isset(Yii::$app->request->queryParams['category']) ? Yii::$app->request->queryParams['category'] : '' );
            $model->category2 = ( isset(Yii::$app->request->queryParams['category2']) ? Yii::$app->request->queryParams['category2'] : '' );
            $model->date = date('Y-m-d');
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
                'categories2' => $categories2,
                'names' => $names,
            ]);
        }
    }

    /**
     * Copy a new Outgo model.
     * @return mixed
     */
    public function actionCopy()
    {
        $model = new Outgo();

        $categories = Utils::getOutgoCategories();
        $categories2 = self::getOutgoCategories2();
        $names = self::getNames();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            if (isset(Yii::$app->request->queryParams['id']) && Yii::$app->request->queryParams['id']) {
                $one = Outgo::findOne(Yii::$app->request->queryParams['id']);
                $model->category = ( $one->category );
                $model->category2 = ( $one->category2 );
                $model->name = ( $one->name );
                $model->date = ( $one->date );
                $model->sum = ( $one->sum );
            }
            else {
                $model->category = (isset(Yii::$app->request->queryParams['category']) ? Yii::$app->request->queryParams['category'] : '');
                $model->category2 = (isset(Yii::$app->request->queryParams['category2']) ? Yii::$app->request->queryParams['category2'] : '');
                $model->date = date('Y-m-d');
            }
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
                'categories2' => $categories2,
                'names' => $names,
            ]);
        }
    }

    /**
     * check saved outgo from request params: category, sum, date, id
     * @return array|string - json array of error or success
     */
    public function actionValidate(){

        if (Yii::$app->request->isAjax) {

            //$category, $sum, $date, $id;
            $category = Yii::$app->getRequest()->getQueryParam('category');
            $sum = Yii::$app->getRequest()->getQueryParam('sum');
            $date = Yii::$app->getRequest()->getQueryParam('date');
            $id = Yii::$app->getRequest()->getQueryParam('id');

            //check - if outgo more than planned by category
            $plan_sum_category = PlanOutgo::getSum([
                'category' => $category,
                'date' => $date,
                'user_id' => Yii::$app->user->id
            ]);
            $sum_category_without_id = Outgo::getSumWithoutId([
                'category' => $category,
                'date' => $date,
                'id' => $id,
                'user_id' => Yii::$app->user->id
            ]);
            if ($plan_sum_category < $sum_category_without_id + $sum) {
                return Json::encode([
                    'result' => 'error',
                    'message' => 'Expenditures by category ' . $category . ' exceeded planned by ' . ( $sum_category_without_id + $sum - $plan_sum_category ) . '!'
                ]);
            }

            // check if outgo more than income
            $sum_income = Income::getSumIncome($date);
            $sum_without_id = Outgo::getSumWithoutId([
                'date' => $date,
                'id' => $id,
                'user_id' => Yii::$app->user->id
            ]);
            if ($sum_income < $sum_without_id + $sum) {
                return Json::encode([
                    'result' => 'error',
                    'message' => 'Expenses for the month more income by ' . ( $sum_without_id + $sum - $sum_income ) . '!'
                ]);
            }
            //check if day of not current month
            if ($date < Utils::getStartMonth() || $date > Utils::getFinishMonth()) {
                return Json::encode([
                    'result' => 'error',
                    'message' => 'Selected day of not current month!'
                ]);
            }

            return Json::encode([
                'result' => 'success',
                'message' => '',
            ]);
        }
    }

    /**
     * Updates an existing one category Outgo model.
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
                if (Outgo::isOneCategoryOutgo($id)) {
                    //If this month there is only one entry in this category - just to update
                    $categories = Utils::getOutgoCategories();
                    $categories2 = self::getOutgoCategories2();
                    $names = self::getNames();

                    return $this->render('update', [
                        'model' => $model,
                        'categories' => $categories,
                        'categories2' => $categories2,
                        'names' => $names,
                    ]);

                } else {

                    $searchModel = new OutgoSearch();
                    $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);
                    if ( count( $dataProvider->getModels() ) == 1 && $dataProvider->getModels()[0]->category2 == '' ) {
                        //If one empty category2 of these Incomes
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl(['outgo/indexcategory2', 'id' => $id]));
                    }
                    else {
                        //If more than one record - show the table of these Incomes
                        return $this->redirect(Yii::$app->getUrlManager()->createUrl(['outgo/indexcategory', 'id' => $id]));
                    }
                }
            }
    }

    /**
     * Updates an existing one category2 Outgo model.
     * If exists more than one row on this category2 - go to the table of rows of this category2
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatecategory2($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            if ( Outgo::isOneCategory2Outgo( $id ) ) {
                //If this month there is only one entry in this category - just to update
                $categories = Utils::getOutgoCategories();
                $categories2 = self::getOutgoCategories2();
                $names = self::getNames();

                return $this->render('update', [
                    'model' => $model,
                    'categories' => $categories,
                    'categories2' => $categories2,
                    'names' => $names,
                ]);

            }
            else {
                //If more than one record - show the table of these Outgoes
                return $this->redirect(Yii::$app->getUrlManager()->createUrl(['outgo/indexcategory2', 'id' => $id]));
            }
        }
    }

    /**
     * Updates an existing Outgo model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $categories = Utils::getOutgoCategories();
        $categories2 = self::getOutgoCategories2();
        $names = self::getNames();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
                'categories2' => $categories2,
                'names' => $names,
            ]);
        }
    }



    /**
     * Deletes an existing Outgo model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }


    /**
     * Deletes an existing one category Outgo models.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecategory($id)
    {
        $outgo = Outgo::findOne($id);
        Outgo::deleteAll(['AND',
            'category=:category',
            'date>=:date_start',
            'date<=:date_finish',
            'user_id=:user_id'
        ],
            [
                ':category' => $outgo->category,
                ':date_start' => Utils::getStartMonth($outgo->date),
                ':date_finish' => Utils::getFinishMonth($outgo->date),
                ':user_id' => $outgo->user_id,
            ]);

        return $this->goBack();
    }

    /**
     * Deletes an existing one category2 Outgo models.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecategory2($id)
    {
        $outgo = Outgo::findOne($id);
        Outgo::deleteAll(['AND',
            'category=:category',
            'category2=:category2',
            'date>=:date_start',
            'date<=:date_finish',
            'user_id=:user_id'],
            [
                ':category' => $outgo->category,
                ':category2' => $outgo->category2,
                ':date_start' => Utils::getStartMonth($outgo->date),
                ':date_finish' => Utils::getFinishMonth($outgo->date),
                ':user_id' => $outgo->user_id,
            ]);

        return $this->goBack();
    }

    /**
     * Finds the Outgo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Outgo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Outgo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Show all statistic
     * @param $category - category
     * @param $date_start - date start period
     * @param $date_end - date end period
     * @return string
     */
    public function actionStatistic() {
        if  (Yii::$app->request->isAjax) {
            //$category, $date_start, $date_end
            $searchModel = new OutgoSearch();
            $dataProvider = $searchModel->searchStatistic(Yii::$app->request->queryParams);

            return $this->renderAjax('/modal/modal-category', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'date_start' => Yii::$app->request->queryParams['date_start'],
                'date_end' => Yii::$app->request->queryParams['date_end'],
                'category' => Yii::$app->request->queryParams['category'],
                'type' => Yii::$app->request->queryParams['type'],
                'sum' => Outgo::getSumStatistic(Yii::$app->request->queryParams),
            ]);
        }
    }


}
