<?php

namespace cyneek\comments\models\enums;

use yii2mod\enum\helpers\BaseEnum;

/**
 * Class CommentStatus
 * @package cyneek\comments\models\enums
 */
class CommentStatus extends BaseEnum
{
    const ACTIVE = 1;
    const DELETED = 2;

    /**
     * @var array
     */
    public static $list = [
        self::ACTIVE => 'Active',
        self::DELETED => 'Deleted'
    ];
}