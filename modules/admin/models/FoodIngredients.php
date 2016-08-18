<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "food_ingredients".
 *
 * @property integer $id
 * @property integer $food_id
 * @property integer $ingredient_id
 *
 * @property Food $food
 * @property Ingredient $ingredient
 */
class FoodIngredients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'food_ingredients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['food_id', 'ingredient_id'], 'required'],
            [['food_id', 'ingredient_id'], 'integer'],
            [['food_id'], 'exist', 'skipOnError' => true, 'targetClass' => Food::className(), 'targetAttribute' => ['food_id' => 'id']],
            [['ingredient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ingredient::className(), 'targetAttribute' => ['ingredient_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'food_id' => 'Food ID',
            'ingredient_id' => 'Ingredient ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFood()
    {
        return $this->hasOne(Food::className(), ['id' => 'food_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngredient()
    {
        return $this->hasOne(Ingredient::className(), ['id' => 'ingredient_id']);
    }
}
