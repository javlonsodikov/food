<?php

use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $food app\modules\admin\models\Food */

$this->title = 'Блюда';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <h3>Блюда: <?= $food->name ?></h3>

                <p>Ингридиенты:   <?= $food->ingredients ?></p>
            </div>
        </div>
    </div>
</div>
