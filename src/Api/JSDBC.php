<?php

namespace Qingclouds\Thinklib\Api;

use Qingclouds\Thinklib\facade\Db;
use Qingclouds\Thinklib\media\RichService;

//use app\common\model\OpenApps;

class JSDBC extends ApiBaseController
{

  public $ERROR_MSG = [
    'DATA_NOT_FOUND' => '您查询的信息不存在或者被删除',
    'ERROR' => '获取数据失败,请稍后重试',
  ];
  /**
   * 数据对象主键名称
   * @var array|string
   */
  protected $pkField;
  /**
   * 数据对象主键值
   * @var string
   */
  protected $pkValue;

  /**
   * @return void
   */
  public function query()
  {

    $base_rules = [
      'table|数据库名称' => 'require',
    ];

    $action = input('action', 'list');

    if ($action === 'query') {
      $base_rules = [
        'query|查询脚本' => 'require',
      ];
    }
    if ($action === 'queryTable') {
      $base_rules = [
        'tableName|查询的table' => 'require',
      ];
    }

    $param = $this->request->post();
    $this->validate($param, $base_rules);
    $this->table = input('table');


    //设置一些参数
    $this->setSql($param);

    switch ($action) {
      case 'list':
        $this->_query($param);
        break;
      case 'read':
        $this->_read($param);
        break;
      case 'deleted':
        $this->_deleted($param);
        break;
      case 'save':
        $this->_save($param);
        break;
      case 'query':
        $this->_diyQuery($param);
        break;
      case 'queryTable':
        $tableName = input('tableName');
        $table_schema = env('database.database', '');
        if (empty($table_schema)) {
          error('table_schema is empty');
        }
        if (empty($tableName)) {
          error('tableName is empty');
        }
        $param['query'] = "select distinct *,column_name as filed,column_comment as comment,IS_NULLABLE as isnull from information_schema.columns where table_schema = '{$table_schema}' and table_name = '{$tableName}'";
        $this->_diyQuery($param);
        break;
      default:
        error('action必传');
        break;
    }
  }

  public function _query($param)
  {


    $query = Db::name($this->table);


    //如果表里有merchantId,就自动加
    //$db = Db::name($this->table);
    //if (in_array('merchant_id', $db->getTableFields())) {
    //  //hack移动端
    //  if (in_array(input('env'), ['wx_lp', 'wap', 'wx_mp', 'app', 'tt_lp', 'ali_lp'])) {
    //    $app_id = input('app_id');
    //    $merchantId = OpenApps::getMerchantID($app_id);
    //  } else {
    //    $merchantId = $this->getMerchantId(false, false);
    //  }
    //
    //  if ($merchantId !== false) {
    //    $this->maps['merchant_id'] = $merchantId;
    //  }
    //}

    // 主键限制处理
    $this->pkField = $query->getPk();

    if (!$this->order && $this->pkField && is_string($this->pkField)) {
      $this->order = $this->pkField . ' desc';//主键逆序
    }

    if ($this->filter && $this->op) {
      $filter = explode('|', $this->filter);
      $op = explode('|', $this->op);

      if (count($filter) > 0 && count($filter) == count($op)) {

        $extraWhere = [];
        foreach ($filter as $key => $filter_item) {
          $filterData = explode(':::', $filter_item);
          if (isset($filterData[0]) && isset($filterData[1])) {
            $filterKey = $filterData[0];
            if ($op[$key] === 'whereBetweenTime') {
              if (strpos($filterData[1], '###') !== false) {
                $extraWhere[] = array($filterKey, 'between time', explode('###', $filterData[1]));//构建请求体
              } else {
                $extraWhere[] = array($filterKey, '>=', $filterData[1]);//构建请求体
              }

            } else {
              $extraWhere[] = array($filterKey, $op[$key], $filterData[1]);//构建请求体
            }

            //如果传了就用这个覆盖掉
            //if ($filterKey === 'merchant_id' && isset($this->maps['merchant_id'])) {
            //  unset($this->maps['merchant_id']);
            //}

          }

        }


        $query = $query->where($extraWhere);
      }
    }


    //如果传了就用这个覆盖掉
    //if (input('clear_merchant_id') === '' && isset($this->maps['merchant_id'])) {
    //  unset($this->maps['merchant_id']);
    //}

    if (is_array($this->maps)) {
      $query = $query->where($this->maps);
    }


    //$filter = [];
    //$filter[] = ['id','in',implode(',',$campsite_ids)];
    //$filter[] = ['rate','>=',$params['rate_min']];
    //$filter[] = ['rate','<=',$params['rate_max']];
    //if($this->condition){
    //    $query = $query->where([$this->condition]);
    //}

    // 筛选字段 优先级要比忽略的高
    if ($this->filed) {
      $query = $query->field($this->filed);
    } else {
      //列表忽略字段
      if ($this->nofiled) {
        $query->withoutField($this->nofiled);
      }
    }


    // 排序
    if ($this->order) {
      if ($query) {
        $query = $query->order($this->order);
      } else {
        $query = $query->order($this->order);
      }
    }

    $total = $query->count();

    if ($this->pageSize > 0) {
      $query = $query->limit(($this->page - 1) * $this->pageSize, $this->pageSize);
    }
    $list = $query->select()->toArray();

    try {
      if (!empty($with = input('with'))) {
        $with_rules = [
          'table|关联表名' => 'require',
          'self_key|当前表key' => 'require',
          'with_key|指定表key' => 'require',
          //'with_filed|仅获取指定字段'=>'require',
          //'with_nofiled|关联表不要的字段'=>'require',
          //'bind_attr_name|绑定属性名字'=>'require',
        ];

        $this->validate($with, $with_rules);
        $with_table = $with['table'];
        $self_key = $with['self_key'];
        $with_key = $with['with_key'];
        $bind_attr_name = isset($with['bind_attr_name']) ? $with['bind_attr_name'] : 'withInfo';

        $with_nofiled = isset($with['with_nofiled']) ? $with['with_nofiled'] : '';
        $with_filed = isset($with['with_filed']) ? $with['with_filed'] : '';
        $withListQuery = Db::name($with_table)->where($with_key, 'in', get_arr_column($list, $self_key));

        if (!empty($with_nofiled)) {
          $withListQuery = $withListQuery->withoutField($with_nofiled);
        }
        if (!empty($with_filed)) {
          $withListQuery = $withListQuery->field($with_filed);
        }

        $withList = $withListQuery->select()->toArray();

        foreach ($list as &$item) {
          foreach ($withList as $withItem) {
            if ($item[$self_key] == $withItem[$with_key]) {
              $item[$bind_attr_name] = $withItem;
            }
          }
        }

      }
    } catch (\Exception $e) {
      //with的错误就不管了吧
      error('with错误' . $e->getMessage());
    }

    $result = ['total' => $total, 'list' => $list];

    success(null, $result);

  }

  protected function _read($param)
  {

    $query = Db::name($this->table);

    if ($this->filter && $this->op) {
      $filter = explode('|', $this->filter);
      $op = explode('|', $this->op);
      if (count($filter) > 0 && count($filter) == count($op)) {
        $extraWhere = [];
        foreach ($filter as $key => $filter_item) {
          $filterData = explode(':::', $filter_item);
          $extraWhere[] = array($filterData[0], $op[$key], $filterData[1]);//构建请求体
        }
        $query = $query->where($extraWhere);
      }
    }


    // 主键限制处理
    $this->pkField = $query->getPk();
    $params = $this->request->param();
    if (isset($params[$this->pkField]) && !empty($params[$this->pkField])) {
      $this->maps[$this->pkField] = $params[$this->pkField];
    }


    if (is_array($this->maps)) {
      $query = $query->where($this->maps);
    }


    // 筛选字段 优先级要比忽略的高
    if ($this->filed) {
      $query = $query->field($this->filed);
    } else {
      //列表忽略字段
      if ($this->nofiled) {
        $query->withoutField($this->nofiled);
      }
    }

    $vo = $query->find();


    if (empty($vo)) {
      error($this->ERROR_MSG['DATA_NOT_FOUND']);
    }

    success(null, $vo);
  }

  protected function _deleted($param)
  {


    $query = Db::name($this->table);

    if ($this->filter && $this->op) {
      $filter = explode('|', $this->filter);
      $op = explode('|', $this->op);
      if (count($filter) > 0 && count($filter) == count($op)) {
        $extraWhere = [];
        foreach ($filter as $key => $filter_item) {
          $filterData = explode(':::', $filter_item);
          $extraWhere[] = array($filterData[0], $op[$key], $filterData[1]);//构建请求体
        }
        $query = $query->where($extraWhere);
      }
    }


    if (is_array($this->maps)) {
      $query = $query->where($this->maps);
    }


    // 主键限制处理
    $this->pkField = $query->getPk();
    $this->pkValue = $this->request->post($this->pkField, null);
    //map中不存在，但是数据中有id,就换一下
    if (!isset($this->maps[$this->pkField])) {
      $query->whereIn($this->pkField, explode(',', $this->pkValue));
      if (isset($param)) unset($param[$this->pkField]);
    }

    //不再走软删除的路了，太提心吊胆了
    $delNum = $query->delete();

    if ($delNum) {
      success('删除成功', $delNum);
    } else {
      error('未删除数据');
    }

  }

  protected function _save($param)
  {

    //去掉无用的数据，避免影响插入和更新
    if (isset($param['maps'])) unset($param['maps']);

    $query = Db::name($this->table);

    //如果表里有merchantId,就自动加
    //if (in_array('merchant_id', $query->getTableFields())) {
    //  $merchantId = $this->getMerchantId(false, false);
    //  if ($merchantId !== false) {
    //    $param['merchant_id'] = $merchantId;
    //  }
    //}


    $hasFilter = false;
    // 主键限制处理
    $this->pkField = Db::name($this->table)->getPk();

    //可以直接传主键
    if (isset($param[$this->pkField])) unset($param[$this->pkField]);
    $this->pkValue = $this->request->post($this->pkField, null);

    //map中替换
    if (empty($this->pkValue) && isset($this->maps[$this->pkField]) && !empty($this->maps[$this->pkField])) {
      $this->pkValue = $this->maps[$this->pkField];
      //map中就不用了
      unset($this->maps[$this->pkField]);
    }


    $pkValues = [];
    if (!empty($this->pkValue)) {
      if (is_string($this->pkValue)) {
        $pkValues = explode(',', $this->pkValue);
      } elseif (is_array($this->pkValue)) {
        $pkValues = $this->pkValue;
      } else {
        $pkValues = [$this->pkValue];
      }
    }


    if (is_array($this->maps) && !empty($this->maps)) {
      $query = $query->where($this->maps);
      $hasFilter = true;
    }

    if (!empty($pkValues)) {
      $hasFilter = true;
      $query = $query->where($this->pkField, 'in', $pkValues);
    }


    //这样就也支持条件更新了哈哈
    if ($this->filter && $this->op) {
      $filter = explode('|', $this->filter);
      $op = explode('|', $this->op);
      if (count($filter) > 0 && count($filter) == count($op)) {
        $extraWhere = [];
        foreach ($filter as $key => $filter_item) {
          $filterData = explode(':::', $filter_item);
          $extraWhere[] = array($filterData[0], $op[$key], $filterData[1]);//构建请求体
        }
        $query = $query->where($extraWhere);
        $hasFilter = true;
      }
    }


    //替换外站地址
    if (isset($param['content'])) {
      $traslateRet = RichService::mk()->saveImgFromList($param['content']);
      $param['content'] = $traslateRet['content'];//htmlspecialchars();
    }

    //过滤无效的
    if (isset($param['create_at']) && empty($param['create_at'])) {
      unset($param['create_at']);
    }

    if (!empty($hasFilter)) {
      $ids = $query->column($this->pkField);
      $hasFilter = Db::name($this->table)->where($this->pkField, 'in', $ids)->count();
    }


    //区分更新和新增
    if (!empty($hasFilter)) {

      // 先获取主键
      $ids = $query->column($this->pkField);
      $rt = Db::name($this->table)->where($this->pkField, 'in', $ids)->strict(false)->data($param)->update();
      $result = $rt !== false ? $rt : false;

    } else {
      $result = $query->strict(false)->insertGetId($param);
    }


    if ($result !== false) {
      success('数据保存成功', $result);
    } else {
      error('数据保存失败, 请稍候再试!');
    }
  }

  public function _diyQuery($param)
  {
    if (!isset($param['query']) || empty($param['query'])) {
      error('query参数必传');
    }
    $queryStr = $param['query'];
    $result = Db::query($queryStr);
    success(null, $result);
  }

  protected function deleted($param)
  {

  }

  protected function save($param)
  {

  }


}