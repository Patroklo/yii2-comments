<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \cyneek\comments\models\CommentModel */
/* @var $widget \cyneek\comments\widgets\Comment */

?>
<?php if ($commentModel->getIsNewRecord()): ?>
<div class="comment-form-container">
    <?php else: ?>
    <div class="comment-form-container-edit">
        <?php endif; ?>

        <?php
        $formOptions = [
            'options' => [
                'id' => (($commentModel->getIsNewRecord()) ?
                    'comment-form' :
                    'comment-edit-form'),
                'class' => 'comment-box',
            ],
            'action' => (($commentModel->getIsNewRecord()) ?
                Url::to('/comment/default/create') :
                Url::to(['/comment/default/update', 'comment_id' => $commentModel->id])),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnBlur' => false
        ];

        if ($widget->pjax === TRUE)
        {
            $formOptions['options']['data-pjax'] = TRUE;
        }

        $form = ActiveForm::begin($formOptions); ?>
        <?php echo Html::hiddenInput('entityData', $widget->encryptedEntity); ?>
        <?php if (Yii::$app->getUser()->getIsGuest()): ?>
            <?php echo $form->field($commentModel, 'anonymousUsername', ['template' => '{input}{error}'])->textInput(['placeholder' => Yii::t('app', 'Add a username...'), 'data' => ['comment' => 'anonymousUsername']]); ?>
        <?php endif; ?>
        <?php echo $form->field($commentModel, 'content', ['template' => '{input}{error}'])->textarea(['placeholder' => Yii::t('app', 'Add a comment...'), 'rows' => 4, 'data' => ['comment' => 'content']]) ?>
        <?php echo $form->field($commentModel, 'parentId', ['template' => '{input}'])->hiddenInput(['data' => ['comment' => 'parent-id']]); ?>
        <div class="comment-box-partial">
            <div class="button-container show">
                <?php
                if ($commentModel->getIsNewRecord())
                {
                    echo Html::a(Yii::t('app', Yii::t('app', 'Click here to cancel reply.')), '#', ['id' => 'cancel-reply', 'class' => 'pull-right', 'data' => ['action' => 'cancel-reply']]);
                    echo Html::submitButton(Yii::t('app', 'Comment'), ['class' => 'btn btn-primary comment-submit']);
                }
                else
                {
                    echo Html::a(Yii::t('app', Yii::t('app', 'Click here to cancel edition.')), '#', ['id' => 'cancel-edition', 'class' => 'pull-right', 'data' => ['action' => 'cancel-edition', 'comment-id' => $commentModel->id]]);
                    echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary comment-submit']);
                }
                ?>
            </div>
        </div>
        <?php $form->end(); ?>
        <div class="clearfix"></div>
    </div>