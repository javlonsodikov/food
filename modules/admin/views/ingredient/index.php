<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\IngredientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ingredients');
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="ingredient-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a(Yii::t('app', 'Create Ingredient'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'name',
                [
                    'attribute' => 'active',
                    'format' => 'raw',
                    'value' => function ($model, $index, $widget) {
                        return Html::checkbox('active[]', $model->active, ['value' => $index, 'class' => 'allowthisaction']) .
                        ' <img class="loading" style="display: none" src="/loading.gif">';
                    },
                ],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

<?php
$url = Url::to(['ingredient/checkit']);
$script = <<< JS
$(".allowthisaction").change(function(){
    var loading = $(this).next('.loading');
   loading.show();
    var id = $(this).parents('tr').attr('data-key');
     $.get({
        url: '{$url}',
        data: {id: id, 'checked':($(this).is(":checked") ? 1 : 0)},
        success: function(data) {
           loading.hide();
        }
    });
    
});
JS;
$this->registerJs($script, \yii\web\View::POS_END);