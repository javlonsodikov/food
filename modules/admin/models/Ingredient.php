<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ingredient".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 *
 * @property FoodIngredients[] $foodIngredients
 */
class Ingredient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ingredient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFoodIngredients()
    {
        return $this->hasMany(FoodIngredients::className(), ['ingredient_id' => 'id']);
    }

    public static function getIngredients($all = true)
    {
        $options = [0 => ""];
        $query = Ingredient::find();
        if (!$all) {
            $query->where('active=1');
        }
        $ingredients = $query->all(); //->where('active=1')
        foreach ($ingredients as $ingredient) {
            $options[$ingredient->id] = $ingredient->name;

        }
        return $options;
    }
}
