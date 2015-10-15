<?php

namespace cyneek\comments\models;

use yii2mod\behaviors\PurifyBehavior;
use cyneek\comments\models\enums\CommentStatus;
use cyneek\comments\Module;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class CommentModel
 *
 * @property integer $id
 * @property string $entity
 * @property integer $entityId
 * @property integer $parentId
 * @property string $content
 * @property integer $anonymousUsername
 * @property integer $createdBy
 * @property integer $updatedBy
 * @property integer $status
 * @property integer $level
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 */
class CommentModel extends ActiveRecord
{
    /**
     * @var null|array|ActiveRecord[] Comment children
     */
    protected $_children;


    const SCENARIO_LOGGED = 'logged';
    const SCENARIO_ANONYMOUS = 'anonymous';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app->getUser()->getIsGuest())
        {
            $this->scenario = self::SCENARIO_ANONYMOUS;
        }
        else
        {
            $this->scenario = self::SCENARIO_LOGGED;
        }
    }

    /**
     * Declares the name of the database table associated with this AR class.
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%Comment}}';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_LOGGED => ['content', 'entity', 'entityId', 'parentId', 'createdBy', 'updatedBy', 'status', 'createdAt', 'updatedAt', 'level'],
            self::SCENARIO_ANONYMOUS => ['content', 'entity', 'entityId', 'parentId', 'anonymousUsername', 'createdBy', 'updatedBy', 'status', 'createdAt', 'updatedAt', 'level'],
        ];
    }


    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules()
    {
        return [
            [['entity', 'entityId', 'content'], 'required'],
            [['anonymousUsername'], 'required', 'on' => self::SCENARIO_ANONYMOUS],
            [['content', 'entity', 'anonymousUsername'], 'string'],
            ['parentId', 'validateParentID'],
            [['entityId', 'parentId', 'createdBy', 'updatedBy', 'status', 'createdAt', 'updatedAt', 'level'], 'integer'],
        ];
    }

    /**
     * Validate parentId attribute
     * @param $attribute
     */
    public function validateParentID($attribute)
    {
        if ($this->{$attribute} !== NULL)
        {
            $comment = self::find()->where(['id' => $this->{$attribute}, 'entity' => $this->entity, 'entityId' => $this->entityId])->active()->exists();
            if ($comment === FALSE)
            {
                $this->addError('content', Yii::t('app', 'Oops, something went wrong. Please try again later.'));
            }
        }
    }


    /**
     * Returns a list of behaviors that this component should behave as.
     * @return array
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'updatedBy',
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt']
                ]
            ],
            'purify' => [
                'class' => PurifyBehavior::className(),
                'attributes' => ['content']
            ]
        ];
    }

    /**
     * Returns the attribute labels.
     * @return array attribute labels (name => label)
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content' => Yii::t('app', 'Comment'),
            'entity' => Yii::t('app', 'Entity'),
            'status' => Yii::t('app', 'Status'),
            'level' => Yii::t('app', 'Level'),
            'createdBy' => Yii::t('app', 'Created by'),
            'updatedBy' => Yii::t('app', 'Updated by'),
            'createdAt' => Yii::t('app', 'Created date'),
            'updatedAt' => Yii::t('app', 'Updated date'),
        ];
    }

    /**
     * @inheritdoc
     * @return CommentQuery
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if ($this->parentId > 0)
            {
                $parentNodeLevel = (int)self::find()->select('level')->where(['id' => $this->parentId])->scalar();
                $this->level = $parentNodeLevel + 1;
            }

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * @return bool
     */
    public static function canCreate()
    {
        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);

        return $module->useRbac === TRUE
            ? \Yii::$app->getUser()->can(CommentPermission::CREATE)
            : TRUE;
    }

    /**
     * @return bool
     */
    public function canUpdate()
    {
        if (Yii::$app->getUser()->getIsGuest())
        {
            return FALSE;
        }

        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);

        return $module->useRbac === TRUE
            ? \Yii::$app->getUser()->can(CommentPermission::UPDATE) || \Yii::$app->getUser()->can(CommentPermission::UPDATE_OWN, ['Comment' => $this])
            : $this->createdBy === \Yii::$app->get('user')->id;
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        if (Yii::$app->getUser()->getIsGuest())
        {
            return FALSE;
        }

        /* @var $module Module */
        $module = Yii::$app->getModule(Module::$name);

        return $module->useRbac === TRUE
            ? \Yii::$app->getUser()->can(CommentPermission::DELETE) || \Yii::$app->getUser()->can(CommentPermission::DELETE_OWN, ['Comment' => $this])
            : $this->createdBy === \Yii::$app->get('user')->id;
    }


    /**
     * Author relation
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        $module = Yii::$app->getModule(Module::$name);

        return $this->hasOne($module->userIdentityClass, ['id' => 'createdBy']);
    }

//    /**
//     * Get comments tree.
//     *
//     * @param string $entity model class id
//     * @param int $entityId model id
//     * @param boolean $showDeletedComments
//     * @param boolean $nestedBehavior
//     * @param null|int $maxLevel
//     * @return array|\yii\db\ActiveRecord[] Comments tree
//     */
//    public static function getTree($entity, $entityId, $showDeletedComments, $nestedBehavior, $maxLevel = null)
//    {
//        $query = self::find()->where([
//            'entityId' => $entityId,
//            'entity' => $entity,
//        ])->with(['author']);
//        if ($maxLevel > 0) {
//            $query->andWhere(['<=', 'level', $maxLevel]);
//        }
//        if ($showDeletedComments === FALSE) {
//            $query->andWhere(['!=', 'status', CommentStatus::DELETED]);
//        }
//        $models = $query->orderBy(['createdAt' => SORT_ASC, 'parentId' => SORT_ASC])->all();
//
//        if (!empty($models) and $nestedBehavior === TRUE) {
//            $models = self::buildTree($models);
//        }
//
//        return $models;
//    }

//    /**
//     * Build comments tree.
//     *
//     * @param array $data Records array
//     * @param int $rootID parentId Root ID
//     * @return array|ActiveRecord[] Comments tree
//     */
//    protected static function buildTree(&$data, $rootID = 0)
//    {
//        reset($data);
//        $tree = [];
//        foreach ($data as $id => $node) {
//            if ($node->parentId == $rootID) {
//                unset($data[$id]);
//              //  $node->children = self::buildTree($data, $node->id);
//                $tree[] = $node;
//            }
//        }
//
//        return $tree;
//    }

    /**
     * Delete comment.
     *
     * @return boolean Whether comment was deleted or not
     */
    public function deleteComment()
    {
        $this->status = CommentStatus::DELETED;

        return $this->save(FALSE, ['status', 'updatedBy', 'updatedAt']);
    }

    /**
     * $_children getter.
     *
     * @return null|array|ActiveRecord[] Comment children
     */
    public function getChildren()
    {
        return $this->hasMany(self::className(), ['parentId' => 'id'])
            ->with('author');
    }


    /**
     * $_children setter.
     *
     * @param array|ActiveRecord[] $value Comment children
     */
    public function setChildren($value)
    {
        $this->_children = $value;
    }

    /**
     * Check if comment has children comment
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children) ? TRUE : FALSE;
    }


    /**
     * @return boolean Whether comment is active or not
     */
    public function getIsActive()
    {
        return $this->status === CommentStatus::ACTIVE;
    }

    /**
     * @return boolean Whether comment is deleted or not
     */
    public function getIsDeleted()
    {
        return $this->status === CommentStatus::DELETED;
    }

    /**
     * Get comment posted date as relative time
     * @return string
     */
    public function getPostedDate()
    {
        return Yii::$app->formatter->asRelativeTime($this->createdAt);
    }

    /**
     * Get author name
     * @return mixed
     */
    public function getAuthorName()
    {
        if (is_null($this->createdBy))
        {
            return $this->anonymousUsername;
        }

        return $this->author->username;
    }

    /**
     * Get comment content
     * @param string $deletedCommentText
     * @return string
     */
    public function getContent($deletedCommentText = NULL)
    {
        if (is_null($deletedCommentText))
        {
            $deletedCommentText = Yii::t('app', 'Comment was deleted.');
        }

        return $this->isDeleted ? $deletedCommentText : Yii::$app->formatter->asNtext($this->content);
    }

    /**
     * Get avatar user
     * @param array $imgOptions
     * @return string
     */
    public function getAvatar($imgOptions = [])
    {
        $imgOptions = ArrayHelper::merge($imgOptions, ['class' => 'img-responsive']);

        if (is_null($this->createdBy))
        {
            return Html::img("http://gravatar.com/avatar/1/?s=50", $imgOptions);
        }

        return Html::img("http://gravatar.com/avatar/{$this->author->id}/?s=50", $imgOptions);
    }

    /**
     * Return hash value of class name
     * @param $className
     * @return string
     */
    public static function hashEntityClass($className)
    {
        return hash('crc32', $className);
    }

    /**
     * This function used for filter in gridView, for attribute `createdBy`.
     * @return array
     */
    public static function getListAuthorsNames()
    {
        return ArrayHelper::map(self::find()->joinWith('author')->all(), 'createdBy', 'author.username');
    }
}