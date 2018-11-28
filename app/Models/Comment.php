<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/11/28
 * Time: 15:52
 */

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Comment extends Model
{
    /**
     * 与 Article 多对一
     *
     * @return Relation
     */
    public function article() :Relation
    {
        return $this->belongsTo(Article::class);
    }
}