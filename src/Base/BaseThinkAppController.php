<?php

namespace Qingclouds\Thinklib\Base;

use think\App;
use think\facade\Validate;
use think\Request;

class BaseThinkAppController
{

  /**
   * Request实例
   * @var Request
   */
  protected $request;

  /**
   * 应用实例
   * @var App
   */
  protected $app;

  public function __construct(App $app = null)
  {

    $this->request = request();
    $this->app = $app;

    // 控制器初始化
    $this->initialize();
  }

  // 初始化
  protected function initialize()
  {
  }

  /**
   * @param $data
   * @param $rules
   * @return array|bool|string
   */
  protected function validate($data, $rules)
  {
    $validate = Validate::rule($rules);
    if (!$validate->check($data)) {
      return $validate->getError();
    }
    return true;
  }

}