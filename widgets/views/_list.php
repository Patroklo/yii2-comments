<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $widget \cyneek\comments\widgets\Comment */
?>
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $provider,
    'itemOptions' => ['class' => 'item'],
    'layout' => "{items}\n{pager}",
    'itemView' => function ($model, $key, $index) use ($widget)
    {
        return $this->render('_item', ['model' => $model, 'widget' => $widget]);
    }
]) ?>