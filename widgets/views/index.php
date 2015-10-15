<?php
/* @var $this \yii\web\View */
/* @var $commentModel \cyneek\comments\models\CommentModel */
/* @var $widget \cyneek\comments\widgets\Comment */

?>
<div class="comments row">
    <div class="col-md-11 col-sm-11">
        <div class="title-block clearfix">
            <h3 class="h3-body-title"><?php echo  Yii::t('app','Comments'); ?></h3>

            <div class="title-seperator"></div>
        </div>
        <?php

        if ($widget->pjax === TRUE)
        {
            \yii\widgets\Pjax::begin([
                'id' => 'new_country',
                'enablePushState' => false,
            ]);
        }
        ?>
        <ol class="comments-list">
            <?php echo $this->render('_list', ['provider' => $provider, 'widget' => $widget]) ?>
        </ol>
        <?php
        if ($commentModel->canCreate() &&
            (!Yii::$app->getUser()->getIsGuest() or ($widget->allowAnonymousComments === TRUE && Yii::$app->getUser()->getIsGuest()))
        )
        {
            if ($widget->pjax === TRUE)
            {
                echo \common\widgets\Alert::widget();
            }

            echo $this->render('_form', ['commentModel' => $commentModel, 'widget' => $widget]);
        }
        if ($widget->pjax === TRUE)
        {
            \yii\widgets\Pjax::end();
        }

        ?>
    </div>
</div>