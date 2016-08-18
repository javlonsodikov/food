<?php

namespace app\controllers;

use app\modules\admin\models\Food;
use app\modules\admin\models\FoodSearch;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\widgets\LinkPager;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $inputIngredients = (array)Yii::$app->request->post('ingredients');
        $inputIngredients = array_map('intval', $inputIngredients);
        if (count($inputIngredients)) {

        }

        $foods = null;

        //Найти блюда со скрытыми ингредиентами
        $hidden_foods = new Query();
        $hidden_foods->select('food.id')->from('food');
        $hidden_foods->innerJoin('food_ingredients', '`food_ingredients`.`food_id`=`food`.`id`');
        $hidden_foods->innerJoin('ingredient', '`food_ingredients`.`ingredient_id`=`ingredient`.`id`');
        $hidden_foods->groupBy('food.id')->where('ingredient.active=0');
        $hidden_ids = $hidden_foods->all();
        $_hidden_ids = [];
        foreach ($hidden_ids as $hidden_id) {
            $_hidden_ids[] = $hidden_id['id'];
        }


        $query = Food::find();
        //исключит блюда у которых скрытьы хотяьы один из ингредиентов
        if (count($_hidden_ids)) {
            $food_hidden_ids = implode(',', $_hidden_ids);
            $query->where('food.id not in (' . $food_hidden_ids . ')');
        }
        if (count($inputIngredients)) {
            $inputIngredient_ids = implode(',', $inputIngredients);

            //1.Если найдены блюда с полным совпадением ингредиентов вывести только их
            $query->select(['food.*']);
            $query->innerJoin('food_ingredients', '`food_ingredients`.`food_id`=`food`.`id`');
            $query->innerJoin('ingredient', '`food_ingredients`.`ingredient_id`=`ingredient`.`id`');
            $query->groupBy('food.id');
            $query->andWhere('food_ingredients.ingredient_id in (' . $inputIngredient_ids . ')');

            $query2 = clone $query;

            //проверка на точное совподение ингредиентов
            $query->andWhere('exists (
                select  1
                from    food_ingredients b
                where   food_ingredients.food_id = b.food_id
                group   by food_id
                having  count(distinct ingredient_id) = ' . count($inputIngredients) . ')');
            $query->andHaving('count(ingredient.id)=' . count($inputIngredients));

            $foods = $query->all();

            //2. Если найдены блюда с частичными совпадениями ингредиентов - вывести в порядке уменьшения совпадения ингредиентов вполд до 2-х
            if (!$foods) {
                $query = $query2;
                $query->andHaving('count(ingredient.id)>1');
                $query->orderBy(['count(ingredient.id)' => SORT_DESC]);
                $foods = $query->all();
            }
        } else {
            $foods = $query->all();
        }
        return $this->render('index', ['foods' => $foods, 'tags' => implode(',', $inputIngredients)]);
    }

    /**
     * Displays food.
     *
     * @return string
     */
    public function actionFood($id)
    {
        $food = Food::findOne($id);

        return $this->render('food', ['food' => $food]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
