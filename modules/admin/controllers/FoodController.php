<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\FoodIngredients;
use app\modules\admin\models\Ingredient;
use Yii;
use app\modules\admin\models\Food;
use app\modules\admin\models\FoodSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FoodController implements the CRUD actions for Food model.
 */
class FoodController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'view', 'edit', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'view', 'edit', 'delete'],
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Food models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FoodSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Food model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }


    /**
     * Creates a new Food model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Food();
        $ingredients = $model->getFoodIngredients()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //обеждаемся что получим всегда array
            $inputIngredients = (array)Yii::$app->request->post('ingredients');
            //преобразовываем все на числа для безопасности
            $inputIngredients = array_map('intval', $inputIngredients);
            //линуем ингредиенты к блюде
            foreach ($inputIngredients as $inputIngredient) {
                $foodIngredients = new FoodIngredients();
                $foodIngredients->food_id = $model->id;
                $foodIngredients->ingredient_id = $inputIngredient;
                $foodIngredients->save();
            }
            //сохраняем готовый список ингредиентов на поле ingredients в таблице food
            if (count($inputIngredients)) {
                $ingredientTags = Ingredient::find()->where('id in (' . implode(',', $inputIngredients) . ')')->all();
                $_tags = [];
                foreach ($ingredientTags as $ingredientTag) {
                    $_tags[] = $ingredientTag->name;
                }
                $model->ingredients = implode(', ', $_tags);
            } else {
                $model->ingredients = "";
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model, 'ingredients' => $ingredients,
            ]);
        }
    }

    /**
     * Updates an existing Food model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $ingredients = $model->getFoodIngredients()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //обеждаемся что получим всегда array
            $inputIngredients = (array)Yii::$app->request->post('ingredients');
            $keepIngredients = [];
            //удаляем не нужные ингредиенты
            foreach ($ingredients as $ingredient) {
                if (!in_array($ingredient->id, $inputIngredients)) {
                    $ingredient->delete();
                } else {
                    $keepIngredients[] = $ingredient->id;
                }
            }
            $_insertIngredients = array_diff($inputIngredients, $keepIngredients);
            //линуем ингредиенты к блюде
            foreach ($_insertIngredients as $_insertIngredient) {
                $foodIngredients = new FoodIngredients();
                $foodIngredients->food_id = $id;
                $foodIngredients->ingredient_id = $_insertIngredient;
                $foodIngredients->save();
            }
            //сохраняем готовый список ингредиентов на поле ingredients в таблице food
            if (count($inputIngredients)) {
                $ingredientTags = Ingredient::find()->where('id in (' . implode(',', $inputIngredients) . ')')->all();
                $_tags = [];
                foreach ($ingredientTags as $ingredientTag) {
                    $_tags[] = $ingredientTag->name;
                }
                $model->ingredients = implode(', ', $_tags);
            } else {
                $model->ingredients = "";
            }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model, 'ingredients' => $ingredients,
            ]);
        }
    }

    /**
     * Deletes an existing Food model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Food model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Food the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Food::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
