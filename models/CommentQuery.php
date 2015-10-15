<?php

namespace cyneek\comments\models;

use yii\db\ActiveQuery;
use cyneek\comments\models\enums\CommentStatus;

/**
 * Class CommentQuery
 * @package cyneek\comments\models
 */
class CommentQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => CommentStatus::ACTIVE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function deleted()
    {
        $this->andWhere(['status' => CommentStatus::DELETED]);
        return $this;
    }
}
