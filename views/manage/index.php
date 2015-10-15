<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use cyneek\comments\models\enums\CommentStatus;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel \cyneek\comments\models\CommentSearchModel */

$this->title = Yii::t('app', 'Comments Management');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comments-index">


    <h1><?php echo Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['enablePushState' => FALSE, 'timeout' => 5000]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['style' => 'max-width: 50px;']
            ],
            [
                'attribute' => 'content',
                'contentOptions' => ['style' => 'max-width: 350px;'],
                'value' => function ($model)
                {
                    return \yii\helpers\StringHelper::truncate($model->content, 100);
                }
            ],
            [
                'attribute' => 'createdBy',
                'value' => function ($model)
                {
                    return $model->getAuthorName();
                },
            ],
            [
                'attribute' => 'status',
                'filter' => array("1" => "Active", "2" => "Deleted"),
                'value' => function ($model)
                {
                    return ($model->status == 1 ? 'Active' : 'Deleted');
                }
            ],
            [
                'attribute' => 'createdAt',
                'value' => function ($model)
                {
                    return Yii::$app->formatter->asDatetime($model->createdAt);
                },
                'filter' => FALSE,
            ],
            [
                'header' => Yii::t('app', 'Actions'),
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}{delete}',
            ]
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>