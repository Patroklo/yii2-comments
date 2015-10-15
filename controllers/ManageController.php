<?php

namespace cyneek\comments\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use cyneek\comments\models\CommentModel;
use cyneek\comments\models\CommentSearchModel;
use cyneek\comments\Module;

/**
 * Manage comments in admin panel
 *
 * Class ManageController
 * @package cyneek\comments\controllers
 */
class ManageController extends Controller
{
    /**
     * @var string path to index view file, which is used in admin panel
     */
    public $indexView = '@vendor/yii2mod/yii2-comments/views/manage/index';

    /**
     * @var string path to update view file, which is used in admin panel
     */
    public $updateView = '@vendor/yii2mod/yii2-comments/views/manage/update';

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
                    'index' => ['get'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all users.
     * @return mixed
     */
    public function actionIndex()
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);
        $commentSearchModelData = $module->model('commentSearch');
        /** @var CommentSearchModel $searchModel */
        $searchModel = Yii::createObject($commentSearchModelData);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $commentModelData = $module->model('comment');
        /** @var CommentModel $commentModel */
        $commentModel = Yii::createObject($commentModelData);

        return $this->render($this->indexView, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'commentModel' => $commentModel
        ]);
    }

    /**
     * Updates an existing CommentModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // if the model has an anonymousUsername value
        // we will consider it as an anonymous message
        if (!is_null($model->anonymousUsername))
        {
            $model->scenario = $model::SCENARIO_ANONYMOUS;
        }


        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Comment has been saved.'));

            return $this->redirect(['index']);
        }

        return $this->render($this->updateView, [
            'model' => $model,
        ]);

    }

    /**
     * Finds the UserModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CommentModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);
        $commentModelData = $module->model('comment');
        /** @var CommentModel $commentModel */
        $commentModel = Yii::createObject($commentModelData);
        if (($model = $commentModel::findOne($id)) !== NULL)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Deletes an existing CommentModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Comment has been deleted.'));

        return $this->redirect(['index']);
    }
}
