<?php

namespace cyneek\comments\widgets;

use yii\web\AssetBundle;
use cyneek\comments\Module;

/**
 * Class CommentAsset
 * @package cyneek\comments
 */
class CommentAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/cyneek/yii2-comments/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/comment.js'
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/comment.css'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset'
    ];


    public function init()
    {
        parent::init();

        /** @var Module $module */
        $module = \Yii::$app->getModule(Module::$name);
        $assetMap = $module->assetMap;

        $typeList = ['sourcePath', 'js', 'css', 'depends'];

        foreach ($typeList as $type)
        {
            if (array_key_exists($type, $assetMap))
            {
                $this->$type = $assetMap[$type];
            }
        }

    }
}