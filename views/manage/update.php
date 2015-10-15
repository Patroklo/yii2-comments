<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use cyneek\comments\models\enums\CommentStatus;

/* @var $this yii\web\View */
/* @var $model \cyneek\comments\models\CommentModel */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Update Comment: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Comments Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="comment-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="comment-form">
        <?php $form = ActiveForm::begin(); ?>

        <?php
        if (!is_null($model->anonymousUsername))
        {
            echo $form->field($model, 'anonymousUsername')->textInput();
        }

        ?>

        <?php echo $form->field($model, 'content')->textarea([
            'id' => 'content',
        ]);
        ?>
        <?php echo $form->field($model, 'status')->dropDownList(CommentStatus::listData()); ?>
        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
