<?php

namespace cyneek\comments\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use cyneek\comments\models\CommentModel;
use cyneek\comments\Module;


/**
 * Class DefaultController
 * @package cyneek\comments\controllers
 */
class DefaultController extends Controller
{
    /**
     * Behaviors
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post', 'delete']
                ],
            ],
        ];
    }

    /**
     * Create comment.
     * @return array|null|Response
     */
    public function actionCreate()
    {
        return $this->saveComment();
    }

    /**
     * Update comment page
     *
     * @param $comment_id
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate($comment_id)
    {
        return $this->saveComment($comment_id);
    }


    public function saveComment($commentId = NULL)
    {
        if (is_null($commentId)) {
            $actionType = 'insert';
        } else {
            $actionType = 'update';
        }

        $entity = base64_decode(Yii::$app->getRequest()->post('entityData'));

        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);

        $decryptEntity = Yii::$app->getSecurity()->decryptByKey($entity, $module::$name);

        $entityData = [];

        if ($decryptEntity !== FALSE) {
            $entityData = Json::decode($decryptEntity);

            if ($actionType == 'update') {
                $model = $this->findModel($commentId);
            } else {
                $commentModelData = $module->model('comment',
                    [
                        'entity' => $entityData['entity'],
                        'entityId' => $entityData['entityId']
                    ]);

                /** @var CommentModel $model */
                $model = Yii::createObject($commentModelData);
            }

            $load = $model->load(Yii::$app->request->post());

            if (Yii::$app->request->isAjax && $load && !Yii::$app->request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }
            if ($load && $model->save()) {
                if ($actionType == 'update') {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Comment has been updated.'));
                } else {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Comment has been created.'));
                }
            }
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Oops, something went wrong. Please try again later.'));
        }

        if (Yii::$app->request->isPjax && !empty($entityData)) {
            return \cyneek\comments\widgets\Comment::widget($entityData);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Delete comment page.
     *
     * @param integer $id Comment ID
     * @return string Comment text
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->deleteComment()) {
            return Yii::t('app', 'Comment was deleted.');
        } else {
            Yii::$app->response->setStatusCode(500);

            return Yii::t('app', 'Comment has not been deleted. Please try again!');
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Comment ID
     * @return null|CommentModel
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);
        $commentModelData = $module->model('comment');
        /** @var CommentModel $commentModel */
        $commentModel = Yii::createObject($commentModelData);
        if (($model = $commentModel::findOne($id)) !== NULL) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
