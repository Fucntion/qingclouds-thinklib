<?php

namespace Qingclouds\Thinklib\api;

use Qingclouds\Thinklib\base\BaseThinkAppController;

class ApiBaseController extends BaseThinkAppController
{

  public $ERROR_MSG = [
    'DATA_NOT_FOUND' => '您查询的信息不存在或者被删除',
    'ERROR' => '获取数据失败,请稍后重试',
  ]; //验证签名，根据需要开吧
  public $db = null;
  public $table;
  public $column = null; //五分钟内时间戳过期
  public $condition = null; //前后端交互数据的加密秘钥
  public $filed = null;
  public $nofiled = null;
  public $order = null;
  public $filter = null;
  public $op = null;
  public $maps = null;//用来传奇葩东西
  protected $sign_auth;
  protected $appid = 'hceUF6Vy0WhvhKMJ';
  protected $secret = 'BSIrt05IGhUnriX7i0b4NXlQgC9TPG7v';
  protected $OUT_OF_DATE_TIME_LONG = 5 * 60 * 100;
  protected $key = 'bd514c52-5363-4364-b73f-a2ec93ae6b34';
  protected $commonWhere = ['status' => 1];

  //分页新参数
  protected $page = 1;
  protected $pageSize = 10;

  protected function setSql($params)
  {

    $columnArr = $this->column ? explode(',', $this->column) : [];
    foreach ($params as $key => $value) {

      //注入其他条件
      if (strpos('id,filed,nofiled,order,with,limit,pageSize,paginate,page,filter,op,maps', $key) > -1) {
        $this->$key = $value;
      }

      //注入maps参数
      if (in_array($key, $columnArr)) {
        $this->maps[$key] = $value;
      }


    }

  }

}