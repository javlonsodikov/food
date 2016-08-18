<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "food".
 *
 * @property integer $id
 * @property string $name
 * @property string $ingredients
 *
 * @property FoodIngredients[] $foodIngredients
 */
class Food extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'food';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'ingredients'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ingredients' => 'Ingredients'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFoodIngredients()
    {
        return $this->hasMany(Ingredient::className(), ['id' => 'ingredient_id'])
            ->viaTable('food_ingredients', ['food_id' => 'id']);
    }
}
