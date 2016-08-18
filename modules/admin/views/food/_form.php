<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\modules\admin\models\Ingredient;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Food */
/* @var $ingredients \yii\db\ActiveQuery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="food-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-food-name required">
        <label for="food-ingredients" class="control-label">Ingredients</label>
        <select name="ingredients[]" class="ingredients-select form-control select2-allow-clear" id="id_label_multiple" multiple="multiple">
            <?php foreach (Ingredient::getIngredients() as $key => $val) {
                echo '<option value="' . $key . '">' . $val . '</option>' . PHP_EOL;
            } ?>
        </select>

        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerCssFile('/css/select2.min.css');
$this->registerCssFile('/css/select2-bootstrap.css');
$this->registerJsFile('/js/select2.full.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);

$_tags = [];
foreach ($ingredients as $ingredient) {
    $_tags[] = $ingredient->id;
}
$tags = implode(',', $_tags);

$this->registerJs("

    $(function(){
        $('.ingredients-select').val([" . $tags . "]);
        $('.ingredients-select').select2({
            maximumSelectionLength: 5,
            maximumSelectionSize:5,
            allowClear: true,
        });
    });

 ", \yii\web\View::POS_END);
?>
