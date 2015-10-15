<?php

namespace cyneek\comments;

use yii\helpers\ArrayHelper;
use cyneek\comments\models\CommentModel;
use cyneek\comments\models\CommentQuery;
use cyneek\comments\models\CommentSearchModel;

/**
 * Class Module
 * @package cyneek\comments
 */
class Module extends \yii\base\Module
{
    /**
     * @var string module name
     */
    public static $name = 'comment';

    /**
     * @var string|null
     */
    public $userIdentityClass = null;

    /**
     * @var string the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     */
    public $controllerNamespace = 'cyneek\comments\controllers';
    
    /**
     * Array that will store the models used in the package
     * e.g. :
     * [
     *     'Comment' => 'frontend/models/comments/CommentModel'
     * ]
     *
     * The classes defined here will be merged with getDefaultModels()
     * having he manually defined by the user preference.
     *
     * @var array
     */
    public $modelMap = [];


    /**
     * Comments use RBA security to grant CRUD permissions
     *
     * @var boolean
     */
    public $useRbac = FALSE;


    /**
     * Array that will store the user defined assets for this package
     * e.g. :
     * [
     *      'js' => ['file1'],
     *      'css' => ['file2'],
     *      'sourcePath' => 'url',
     *      'depends' => ['file3', 'file4']
     * ]
     *
     * If defined, will be used INSTEAD OF THE DEFAULT ONES
     *
     * @var array
     */
    public $assetMap = [];


    /**
     * Initializes the module.
     *
     * This method is called after the module is created and initialized with property values
     * given in configuration. The default implementation will initialize [[controllerNamespace]]
     * if it is not set.
     *
     * If you override this method, please make sure you call the parent implementation.
     */
    public function init()
    {
        if ($this->userIdentityClass === null) {
            $this->userIdentityClass = \Yii::$app->getUser()->identityClass;
        }

        parent::init();
        
        $this->defineModelClasses();
        
    }

    /**
     * Merges the default and user defined model classes
     * Also let's the developer to set new ones with the
     * parameter being those the ones with most preference.
     *
     * @param array $modelClasses
     */
    public function defineModelClasses($modelClasses = [])
    {
        $this->modelMap = ArrayHelper::merge(
            $this->getDefaultModels(),
            $this->modelMap,
            $modelClasses
        );
    }

    /**
     * Get default model classes
     */
    public function getDefaultModels()
    {
        return [
            'Comment' => CommentModel::className(),
            'CommentQuery' => CommentQuery::className(),
            'CommentSearch' => CommentSearchModel::className()
        ];
    }

    /**
     * Get defined className of model
     *
     * Returns an string or array compatible
     * with the Yii::createObject method.
     *
     * @param string $name
     * @param array $config // You should never send an array with a key defined as "class" since this will
     *                      // overwrite the main className defined by the system.
     * @return string|array
     */
    public function model($name, $config = [])
    {
        $modelData = $this->modelMap[ucfirst($name)];

        if (!empty($config)) {
            if (is_string($modelData)) {
                $modelData = ['class' => $modelData];
            }

            $modelData = ArrayHelper::merge(
                $modelData,
                $config
            );
        }

        return $modelData;
    }
    
}
