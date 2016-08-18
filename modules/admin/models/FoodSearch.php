<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Food;

/**
 * FoodSearch represents the model behind the search form about `app\modules\admin\models\Food`.
 */
class FoodSearch extends Food
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'ingredients'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Food::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'food.id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'food.name', $this->name]);
        $query->andFilterWhere(['like', 'food.ingredients', $this->ingredients]);

        /*
         //скрытия блюд если скрыты хотябы один из ингредиентов - для админа не нужна
         $query->
        select(['food.*'])->
        leftJoin('food_ingredients', '`food_ingredients`.`food_id`=`food`.`id`')->
        leftJoin('ingredient', '`food_ingredients`.`ingredient_id`=`ingredient`.`id`')->
        groupBy('food.id')->
        having('count(ingredient.id)=COALESCE(sum(ingredient.active),0)');*/

        return $dataProvider;
    }
}
