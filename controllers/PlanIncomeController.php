<?php

namespace app\controllers;

use Yii;
use app\models\PlanIncome;
use app\models\PlanIncomeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * PlanIncomeController implements the CRUD actions for PlanIncome model.
 */
class PlanIncomeController extends Controller
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
     * Lists all PlanIncome models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PlanIncomeSearch();
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
        $searchModel = new PlanIncomeSearch();
        $dataProvider = $searchModel->searchCategory(Yii::$app->request->queryParams);
        $one = PlanIncome::findOne(Yii::$app->request->queryParams['id']);

        return $this->render('indexcategory', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $one->category
        ]);
    }

    /**
     * Displays a single PlanIncome model.
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
     * Creates a new PlanIncome model.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PlanIncome();

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
     * Updates an existing PlanIncome model.
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
                'category' => $categories,
            ]);
        }
    }

    /**
     * Updates an existing one category PlanIncome model.
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
            if ( PlanIncome::isOneCategoryPlanIncome( $id ) ) {
                //If this month there is only one entry in this category - just to update
                $categories = Utils::getIncomeCategories();
                return $this->render('update', [
                    'model' => $model,
                    'categories' => $categories,
                ]);

            }
            else {
                //If more than one record - show the table of these Incomes
                return $this->redirect(Yii::$app->getUrlManager()->createUrl(['plan-income/indexcategory', 'id' => $id]));
            }
        }
    }


    /**
     * Deletes an existing PlanIncome model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * Deletes an existing one category PlanIncome models.
     * @param integer $id
     * @return mixed
     */
    public function actionDeletecategory($id)
    {
        $planincome = PlanIncome::findOne($id);
        PlanIncome::deleteAll(['AND',
            'category=:category',
            'date>=:date_start',
            'date<=:date_finish',
            'user_id=:user_id'
        ], [
            ':category' => $planincome->category,
            ':date_start' => Utils::getStartMonth($planincome->date),
            ':date_finish' => Utils::getFinishMonth($planincome->date),
            ':user_id' => $planincome->user_id
        ]);

        return $this->goBack();
    }



    /**
     * Finds the PlanIncome model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PlanIncome the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlanIncome::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
