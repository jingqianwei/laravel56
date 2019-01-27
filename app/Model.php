<?php
/**
 * Created by PhpStorm.
 * User: chinwe.jing
 * Date: 2018/11/28
 * Time: 15:41
 */

namespace App;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Validator;
//use App\Utils\Helper;

class Model extends BaseModel
{
    // ------------------------ 定义基础属性 --------------------------

    /**
     * 加载辅助 Trait 类：可自行修改定义，根据项目情况可多加载几个
     */
    //use Helper;

    /**
     * 数据容器：存放请求数据、模型生成临时数据、响应数据等
     *
     * @var array
     */
    protected $data = [];

    /**
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'string',
        'updated_at' => 'string'
    ];

    // ------------------------ 定义基础数据容器操作方法 --------------------------

    /**
     * 存储数据：向数据容器中合并数据，一般在方法结尾处使用
     *
     * @param array $data 经过系统 compact 方法处理好的数组
     * @return Model
     */
    protected function compact(array $data) :Model
    {
        $this->data = $data + $this->data;
        return $this;
    }

    /**
     * 获取数据：从数据容器中获取数据，一般在方法开始，或方法中使用
     *
     * @param string $keys 支持点语法，访问多维数组
     * @param null $default
     * @return array|mixed|null
     */
    protected function extract(string $keys = '', $default = null)
    {
        $result = $this->data;
        if ($keys == '') {
            return $result;
        }

        $keys = explode('.', $keys);
        foreach ($keys as $key) {
            if (!array_key_exists($key, $result)) {
                return $default;
            }
            $result = $result[$key];
        }
        return $result;
    }

    // ------------------------ 请求开始：验证数据，加工数据 --------------------------

    /**
     * 请求：使用模型的入口
     *
     * @param string $method 控制器方法名称
     * @param array $request 请求的原始数据
     * @param BaseModel|null $auth 核心入口模型，一般由授权模型获得。
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function request(string $method, array $request, BaseModel $auth = null) :Model
    {
        return $this->compact(compact('method', 'request', 'auth'))
            ->validate()
            ->process();
    }

    /**
     * 验证数据：根据 rule 方法返回的规则，进行数据验证
     *
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate() :Model
    {
        $rules = $this->rule();
        if (!$rules) return $this;
        $validator = Validator::make($this->extract('request'), $rules['rules'], config('message'), $rules['attrs']);
        if (isset($rules['sometimes']) && count($rules['sometimes'])) {
            foreach ($rules['sometimes'] as $v) {
                $validator->sometimes(...$v);
            }
        }
        $validator->validate();
        return $this;
    }

    /**
     * 数据验证规则：此方法将在 validate 方法中被调用，用以获取验证规则
     *
     * 注：此方法需根据情况，在子模型中重写
     *
     * @return array
     */
    protected function rule() :array
    {
        return [];
    }

    /**
     * 加工请求：请求数据验证通过后，用此方法进行数据加工与方法派遣操作
     *
     * 注：此方法需根据情况，在子模型中重写
     *
     * @return Model
     */
    protected function process() :Model
    {
        return $this;
    }

    // ------------------------ 操作类请求：映射字段、生成模型、保存数据 --------------------------

    /**
     * 生成数据模型：此方法定义一个统一名为 model 的对外接口，建议在控制器中调用
     *
     * @return Model
     */
    public function model() :Model
    {
        return $this->createDataModel();
    }

    /**
     * 生成数据模型（内置方法）
     *
     * @return Model
     */
    protected function createDataModel() :Model
    {
        $request = $this->extract('request');
        $maps = $this->map();
        if (!$maps) return $this;
        foreach (array_keys($request) as $v) {
            if (array_key_exists($v, $maps)) {
                $k = $maps[$v];
                $this->$k = $request[$v];
            }
        }
        return $this;
    }

    /**
     * 数据映射：请求字段 => 数据库字段 的映射，用以生成含有数据的数据表模型
     *
     * 注：此方法需根据情况，在子模型中重写
     *
     * @return array
     */
    protected function map() :array
    {
        return [];
    }

    /**
     * 保存模型：同 save 方法，可重写 save 逻辑，而不影响原 save，保证其它模块正常工作
     *
     * @param array $options
     * @return Model
     */
    public function reserve(array $options = []) :Model
    {
        if ($this->save($options)) {
            return $this;
        } else {
            abort(422, '保存失败');
        }
    }

    // ------------------------ 获取类请求：根据规则和映射获取数据，加工数据，返回数据 --------------------------

    /**
     * 取出数据：从数据库获取数据，建议在控制器中调用
     *
     * @return Model
     */
    public function fetch() :Model
    {
        $response = [];
        $map = $this->fetchMap();
        if (!$map) return $this;
        $gathers = $this->isShow()->getChainRule($this, $map['chain']);
        foreach ($gathers as $k => $gather) {
            foreach ($map['data'] as $_k => $v) {
                if (is_array($v)) {
                    foreach ($gather->$_k as $n => $relevancy) {
                        foreach ($v as $m => $_v) {
                            $response[$k][$_k][$n][$m] = $this->getDataRule($relevancy, explode('.', $_v));
                        }
                    }
                } else {
                    $response[$k][$_k] = $this->getDataRule($gather, explode('.', $v));
                }
            }
        }
        return $this->compact(compact('response'))->epilogue();
    }

    /**
     * 区分展示详情或展示列表
     *
     * @return Model
     */
    protected function isShow() :Model
    {
        $isShow = $this->id ? true : false;
        return $this->compact(compact('isShow'));
    }

    /**
     * 取数据的映射规则
     *
     * 注：此方法需根据情况，在子模型中重写
     *
     * @return array
     */
    protected function fetchMap() :array
    {
        return [];
    }

    /**
     * 递归链式操作：封装查询构造器，根据数组参数调用查询构造顺序。
     *
     * @param $model
     * @param  array $chains
     * @return object
     */
    protected function getChainRule($model, array $chains)
    {
        if (!$chains) {
            if ($this->extract('isShow')) {
                return Collection::make([$model]);
            }
            return $model->get();
        }

        $chain = array_shift($chains);
        foreach ($chain as $k => $v) {
            $model = $model->$k(...$v);
        }

        if ($k == 'paginate') {
            $page = [
                'total' => $model->total()
            ];
            $this->compact(compact('page'));
            return $model;
        } else if ($chains) {
            return $this->getChainRule($model, $chains);
        } else if ($this->extract('isShow')) {
            return Collection::make([$model]);
        } else {
            return $model->get();
        }
    }

    /**
     * 递归取值：取关联模型的数据
     *
     * @param $gather
     * @param array $rules
     * @return mixed
     */
    protected function getDataRule($gather, array $rules)
    {
        $rule = array_shift($rules);
        $gather = $gather->$rule;
        if ($rules) {
            return $this->getDataRule($gather, $rules);
        } else {
            return $gather;
        }

    }

    // ------------------------ 响应数据 --------------------------

    /**
     * 发送响应：请在控制器调用，操作类请求传 message，获取类请求不要传 message
     *
     * @param null $message
     * @return JsonResponse
     */
    public function response($message = null) :JsonResponse
    {
        if ($message !== null) {
            $this->setMessage($message);
        }

        return $this->send();
    }

    /**
     * 操作类请求设置操作成功的 message
     *
     * @return Model
     */
    protected function setMessage($message = null) :Model
    {
        $response = [
            'code' => 200,
            'message' => $message !== null ? $message : '操作成功',
        ];
        return $this->compact(compact('response'))->epilogue();
    }

    /**
     * 收尾：对获取的数据进行最后加工
     *
     * @return Model
     */
    protected function epilogue() :Model
    {
        return $this;
    }

    /**
     * 发送数据
     *
     * @return JsonResponse
     */
    protected function send() :JsonResponse
    {
        return response()->json($this->extract('response'));
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement', 'request'])) {
            return $this->$method(...$parameters);
        }

        return $this->newQuery()->$method(...$parameters);
    }
}