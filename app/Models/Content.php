<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/11/28
 * Time: 15:51
 */

namespace App\Models;

use App\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Content extends Model
{
    /**
     * @return Relation
     */
    public function article() :Relation
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 数据映射：请求字段 => 数据库字段 的映射，用以生成含有数据的数据表模型
     *
     * @return array
     */
    protected function map() :array
    {
        return [
            'content' => 'content',
        ];
    }
}