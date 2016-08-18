<?php

use \yii\helpers\Url;
use \yii\helpers\Html;
use yii\widgets\ActiveForm;
use \app\modules\admin\models\Ingredient;


/* @var $this yii\web\View */
/* @var $foods app\modules\admin\models\Food */

$this->title = 'Блюда';
?>
<div class="site-index">


    <div class="body-content">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-lg-6">
                <div class="input-group">
                    <select placeholder="ингредиенты..." name="ingredients[]" class="ingredients-select form-control select2-allow-clear" id="id_label_multiple" multiple="multiple">
                        <?php foreach (Ingredient::getIngredients(false) as $key => $val) {
                            echo '<option value="' . $key . '">' . $val . '</option>' . PHP_EOL;
                        } ?>
                    </select>
                    <span class="input-group-btn">
                        <input class="btn btn-default" type="submit" onclick="return checkTags();" value="Поиск!">
                    </span>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <div class="row">

            <?php
            if ($foods) {
                foreach ($foods as $food): ?>
                    <div class="col-lg-4">
                        <h4><?= $food->name ?></h4>

                        <p>Ингридиенты: <?= $food->ingredients ?></p>
                        <p>
                            <?= Html::a('рецепт &raquo;',
                                Url::toRoute(['food', 'id' => $food->id]),
                                ['title' => $food->name, 'class' => 'btn btn-default']); ?>
                        </p>
                    </div>

                <?php endforeach;
            }
            else {
                echo '<div class="alert alert-danger">Ничего не найдено</div>';
            }
            ?>

        </div>
        <div class="message alert alert-danger hidden"></div>
    </div>
</div>

<?php

$this->registerCssFile('/css/select2.min.css');
$this->registerCssFile('/css/select2-bootstrap.css');
$this->registerJsFile('/js/select2.full.min.js', ['depends' => [yii\web\JqueryAsset::className()]]);
$this->registerJs("
    $(function(){
        $('.ingredients-select').val([" . $tags . "]);
        $('.ingredients-select').select2({
            maximumSelectionLength: 5,
            maximumSelectionSize:5,
            allowClear: true,
        });
    });
    
    function checkTags(){
        if ($('.ingredients-select').select2('data').length<2)
        {
            $('.message.alert-danger').removeClass('hidden');
            $('.message.alert-danger').html('Выберите больще ингредиентов');
            return false;
        }
        else {
            $('.message.alert-danger').addClass('hidden');
        }
    }
    
 ", \yii\web\View::POS_END);
?>

