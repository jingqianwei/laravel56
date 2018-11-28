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
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    /**
     * 与 Content 一对一
     *
     * @return Relation
     */
    public function content() :Relation
    {
        return $this->hasOne(Content::class)->withDefault();
    }

    /**
     * 与 Comment 一对多
     *
     * @return Relation
     */
    public function comments() :Relation
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 数据验证规则：此方法将在 validate 方法中被调用，用以获取验证规则
     *
     * @return array
     */
    protected function rule() :array
    {
        switch ($this->extract('method')) {
            case 'store':
                return [
                    'rules' => [
                        'title' => 'required|string|max:140',
                        'content' => 'required|string',
                    ],
                    'attrs' => [
                        'title' => '文章标题',
                        'content' => '文章内容',
                    ]
                ];
                break;
            case 'update':
                return [
                    'rules' => [
                        'title' => 'required_without:content|string|max:140',
                        'content' => 'required_without:title|string',
                    ],
                    'attrs' => [
                        'title' => '文章标题',
                        'content' => '文章内容',
                    ]
                ];
                break;
            case 'index':
            case 'show':
                return [
                    'rules' => [
                        'page' => 'required|integer|min:1',
                        'num' => 'sometimes|integer|min:1',
                    ],
                    'attrs' => [
                        'page' => '页码',
                        'num' => '每页数量',
                    ]
                ];
                break;
        }
        return [];
    }

    /**
     * 加工请求：请求数据验证通过后，用此方法进行数据加工与方法派遣操作
     *
     * @return Model
     */
    protected function process() :Model
    {
        switch ($this->extract('method')) {
            case 'store':
            case 'update':
                $request = array_map(function ($item) {
                    return trim($item);
                }, $this->extract('request'));
                return $this->compact(compact('request'));
                break;
        }
        return $this;
    }

    /**
     * 数据映射：请求字段 => 数据库字段 的映射，用以生成含有数据的数据表模型
     *
     * @return array
     */
    protected function map() :array
    {
        return [
            'title' => 'title',
        ];
    }

    /**
     * 保存模型：同 save 方法，可重写 save 逻辑，而不影响原 save，保证其它模块正常工作
     *
     * @param array $options
     * @return Model
     */
    public function reserve(array $options = []) :Model
    {
        DB::beginTransaction();
        if (
            $this->save($options)
            &&
            $this->content->request('store', $this->extract('request'))
                ->model()
                ->save()
        ) {
            DB::commit();
            return $this;
        } else {
            DB::rollBack();
            abort(422, '保存失败');
        }
    }

    /**
     * 删除
     *
     * @return $this|bool|null
     * @throws \Exception
     */
    public function delete()
    {
        DB::beginTransaction();
        if (
            $this->content->delete()
            &&
            parent::delete()

        ) {
            DB::commit();
            return $this;
        } else {
            DB::rollBack();
            abort(422, '删除失败');
        }
    }

    /**
     * 取数据的映射规则
     *
     * @return array
     */
    protected function fetchMap() :array
    {
        switch ($this->extract('method')) {
            case 'index':
                return [
                    'chain' => [
                        ['paginate' => [$this->extract('request.num', 10)]]
                    ],
                    'data' => [
                        'id' => 'id',
                        'title' => 'title',
                        'c_time' => 'created_at',
                        'u_time' => 'updated_at',
                    ]
                ];
                break;
            case 'show':
                return [
                    'chain' => [
                        ['load' => ['content']],
                        ['load' => [['comments' => function ($query) {
                            $paginate = $query->paginate($this->extract('request.num', 10));
                            $page = [
                                'total' => $paginate->total(),
                            ];
                            $this->compact(compact('page'));
                        }]]],
                    ],
                    'data' => [
                        'id' => 'id',
                        'title' => 'title',
                        'content' => 'content.content',
                        'comments' => [
                            'id' => 'id',
                            'comment' => 'comment',
                        ],
                        'c_time' => 'created_at',
                        'u_time' => 'updated_at',
                    ]
                ];
                break;

        }
        return [];
    }

    /**
     * 收尾：对获取的数据进行最后加工
     *
     * @return Model
     */
    protected function epilogue() :Model
    {
        switch ($this->extract('method')) {
            case 'index':
                $response = [
                    'code' => 200,
                    'message' => '获取成功',
                    'data' => $this->extract('response'),
                    'total' => $this->extract('page.total'),
                ];
                return $this->compact(compact('response'));
                break;
            case 'show':
                $response = ['comments' => [
                        'total' => $this->extract('page.total'),
                        'data' => $this->extract('response.0.comments')
                    ]] + $this->extract('response.0');
                return $this->compact(compact('response'));
                break;
        }
        return $this;
    }
}