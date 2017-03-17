<?php

namespace app\controllers;

use Yii;
use app\models\Income;
use app\models\IncomeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * IncomeController implements the CRUD actions for Income model.
 */
class IncomeController extends Controller
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
     * Lists all Income models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IncomeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Income models on category
     * @return mixed
     */
    public function actionIndexcategory()
    {
        Url::remember();
        $searchModel = new IncomeSearch();
        $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);
        $one = Income::findOne(Yii::$app->request->queryParams['id']);

        return $this->render('indexcategory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $one->category
        ]);
    }

    /**
     * Displays a single Income model.
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
     * Creates a new Income model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Income();

        $categories = Utils::getIncomeCategories();

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
     * Updates an existing one category Income Model.
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
            if ( Income::isOneCategoryIncome( $id ) ) {
                //If this month there is only one entry in this category - just to update
                $categories = Utils::getIncomeCategories();
                return $this->render('update', [
                    'model' => $model,
                    'categories' => $categories,
                ]);

            }
            else {
                //If more than one record - show the table of these Incomes
                return $this->redirect(Yii::$app->getUrlManager()->createUrl(['income/indexcategory', 'id' => $id]));
            }
        }
    }


    /**
     * Updates an existing Income model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $categories = Utils::getIncomeCategories();

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
     * Deletes an existing Income model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * Deletes an existing one category Income models.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecategory($id)
    {
        $income = Income::findOne($id);
        Income::deleteAll(['AND',
            'category=:category',
            'date>=:date_start',
            'date<=:date_finish',
            'user_id=:user_id'],
            [
                ':category' => $income->category,
                ':date_start' => Utils::getStartMonth($income->date),
                ':date_finish' => Utils::getFinishMonth($income->date),
                ':user_id' => $income->user_id,
            ]);

        return $this->goBack();
    }

    /**
     * Finds the Income model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Income the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Income::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
