<?php

declare (strict_types=1);


use Qingclouds\Thinklib\Tools\Emoji;
use think\facade\Log;
use think\facade\Validate;

//if (!function_exists('test')) {
//  function test($data)
//  {
//    var_dump($data);
//  }
//}


if (!function_exists('success_only_data')) {
  function success_only_data($data = null, $msg = 'success', $code = 1)
  {
    success($msg, $data, $code);
  }
}

if (!function_exists('success')) {
  /**
   * @param string $msg 消息内容
   * @param array $data 返回数据
   * @param int $code 返回代码
   * @return void
   */
  function success($msg = 'success', $data = null, $code = 1)
  {
    if ($msg == null) $msg = 'success';
    $result = ['code' => $code, 'msg' => $msg, 'data' => $data];
    //write_log($result, 'api_result');
    //header("Content-type:text/json");
    //die(json_encode($result));
    throw new \think\exception\HttpResponseException(json($result));
  }
}


if (!function_exists('error')) {
  function error($errMsg = '', $errCode = '', $msg = 'fail', $code = 0)
  {
    $errData = [];
    if (!empty($errMsg)) {
      $errData['errMsg'] = $errMsg;
    }
    if (!empty($errCode)) {
      $errData['errCode'] = $errCode;
    }

    abort_api($msg, $errData, $code);
  }
}

if (!function_exists('abort_api')) {
  /**
   * 返回失败的请求
   * @param mixed $msg 消息内容
   * @param array $data 返回数据
   * @param integer $code 返回代码
   * @return void
   */
  function abort_api($msg = 'error', $data = null, $code = 0)
  {
    $result = ['code' => $code, 'msg' => $msg, 'data' => $data];
    //write_log($result, 'api_result');
    //header("Content-type:text/json");
    //die(json_encode($result));
    throw new \think\exception\HttpResponseException(json($result));
  }
}

if (!function_exists('randString')) {
  /**
   * 随机返回字符串
   * @param int $len
   * @desc 生成验证码
   * @return bool|string
   */
  function randString($len = 6)
  {
    $chars = str_repeat('0123456789', 3);
    // 位数过长重复字符串一定次数
    $chars = str_repeat($chars, $len);
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
  }
}


if (!function_exists('getTempFilePath')) {
  /**
   * 获取文件暂时使用地址
   * @return string
   */
  function getTempFilePath()
  {
    $basePath = app()->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . "files";
    if (!is_dir($basePath)) {
      mkdir($basePath);
    }
    $path = $basePath . DIRECTORY_SEPARATOR . date("Ymd");
    if (!is_dir($path)) {
      mkdir($path);
    }
    return $path;
  }
}

if (!function_exists('saveFile')) {
  /*
* 定义文件路径，写入图片流
*/
  function saveFile($filename, $filecontent)
  {
    $upload_dir = getTempFilePath();//保存路径，以时间作目录分层
    $mkpath = $upload_dir;

    if (!is_dir($mkpath)) {
      if (!mkdir($mkpath)) {
        throw new \Exception('no mkdir power');
      }
      //if (!chmod($mkpath, 0777)) {//若服务器在阿里云上不建议使用0644
      //    die('no chmod power');
      //}
    }
    $savepath = $upload_dir . DIRECTORY_SEPARATOR . $filename;
    if (file_put_contents($savepath, $filecontent)) {
      //写入图片流生成图片
      return $upload_dir . DIRECTORY_SEPARATOR . $filename;//返回图片路径
    } else {
      throw new \Exception('save failed');
    }

  }
}




if (!function_exists('validate_func')) {
  function validate_func($data, $rules)
  {
    $validate = Validate::rule($rules);
    if (!$validate->check($data)) {
      return $validate->getError();
    }
    return true;
  }
}


if (!function_exists('cutstr')) {
  /**
   * @param $str
   * @param $len
   * @return string
   */
  function cutstr($str, $len = 20)
  {
    return mb_substr($str, 0, $len, 'utf-8');
  }
}


if (!function_exists('randPasswordSalt')) {
  function randPasswordSalt($len = 10)
  {
    $chars = str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 3);
    // 位数过长重复字符串一定次数
    $chars = str_repeat($chars, $len);
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
  }
}

if (!function_exists('isEmptyObject')) {
  /**
   * 查看一个变量是不是常见的空值。空字符串在php里面居然等同true。。。
   * @param $obj
   * @param bool $strice
   * @return bool
   */
  function isEmptyObject($obj, $strice = false)
  {


    if ($obj === '' || $obj === null || $obj === 'null') {
      return true;
    }

    if ($strice && $obj == 0) {
      return true;
    }

    return false;

  }
}

if (!function_exists('get_arr_column')) {
  /**
   * 获取数组中的某一列
   * @param array $arr 数组
   * @param string $key_name 列名
   * @return array  返回那一列的数组
   */
  function get_arr_column($arr, $key_name)
  {
    $arr2 = array();
    if (!empty($arr)) {
      foreach ($arr as $val) {
        $arr2[] = $val[$key_name];
      }
    }
    return $arr2;
  }
}

if (!function_exists('format_number')) {
  function format_number($val)
  {
    if (gettype($val) === 'string') {
      return (float)$val;
    }
    return $val;
  }
}

if (!function_exists('computed_array_column_sum')) {
  /**
   * 获取某个数组中的指定列的和
   * @param $arr
   * @param $key_name
   * @return float|int
   */
  function computed_array_column_sum($arr, $key_name)
  {
    $sum = 0;
    if (!empty($arr)) {
      foreach ($arr as $val) {
        $sum += format_number($val[$key_name]);
      }
    }
    return $sum;
  }
}


if (!function_exists('randString')) {
  /**
   * 随机返回字符串
   * @param int $len
   * @desc 生成验证码
   * @return bool|string
   */
  function randString($len = 6)
  {
    $chars = str_repeat('0123456789', 3);
    // 位数过长重复字符串一定次数
    $chars = str_repeat($chars, $len);
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
  }
}



if (!function_exists('isMobile')) {
  /**
   * 验证手机号是否正确
   * @param string $mobile
   * @author honfei
   */
  function isMobile($mobile)
  {
    if (!is_numeric($mobile)) {
      return false;
    }

    return (bool)preg_match('#^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$#', (string)$mobile);
  }
}



if (!function_exists('uuid')) {
  /**
   * @param string $prefix
   * @return string
   */
  function uuid($prefix = "")
  {    //可以指定前缀
    $str = md5(uniqid());
    $uuid = substr($str, 0, 8) . '-';
    $uuid .= substr($str, 8, 4) . '-';
    $uuid .= substr($str, 12, 4) . '-';
    $uuid .= substr($str, 16, 4) . '-';
    $uuid .= substr($str, 20, 12);
    return $prefix . $uuid;
  }
}

if (!function_exists('getUpFilePath')) {

  function getUpFilePath()
  {
    $basePath = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . "upload";
    if (!is_dir($basePath)) {
      mkdir($basePath);
    }
    $path = $basePath . DIRECTORY_SEPARATOR . date("Ymd");
    if (!is_dir($path)) {
      mkdir($path);
    }
    return ['full' => $path, 'path' => 'upload' . DIRECTORY_SEPARATOR . date("Ymd")];
  }
}

if (!function_exists('getTempFilePath')) {
  /**
   * @return string
   */
  function getTempFilePath()
  {
    $basePath = app()->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . "files";
    if (!is_dir($basePath)) {
      mkdir($basePath);
    }
    $path = $basePath . DIRECTORY_SEPARATOR . date("Ymd");
    if (!is_dir($path)) {
      mkdir($path);
    }
    return $path;
  }
}

if (!function_exists('getUploadFilePath')) {
  /**
   * @return string
   */
  function getUploadFilePath()
  {
    $basePath = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . "upload";
    if (!is_dir($basePath)) {
      mkdir($basePath);
    }
    $path = $basePath . DIRECTORY_SEPARATOR . date("Ymd");
    if (!is_dir($path)) {
      mkdir($path);
    }
    return $path;
  }
}

if (!function_exists('saveFile')) {
  /*
* 定义文件路径，写入图片流
*/
  function saveFile($filename, $filecontent)
  {
    $upload_dir = getTempFilePath();//保存路径，以时间作目录分层
    $mkpath = $upload_dir;

    if (!is_dir($mkpath)) {
      if (!mkdir($mkpath)) {
        throw new \think\Exception('no mkdir power');
      }
      //if (!chmod($mkpath, 0777)) {//若服务器在阿里云上不建议使用0644
      //    die('no chmod power');
      //}
    }
    $savepath = $upload_dir . DIRECTORY_SEPARATOR . $filename;
    if (file_put_contents($savepath, $filecontent)) {
      //写入图片流生成图片
      return $upload_dir . DIRECTORY_SEPARATOR . $filename;//返回图片路径
    } else {
      throw new \think\Exception('save failed');
    }

  }
}

if (!function_exists('getNowTime')) {
  function getNowTime($format = 'Y-m-d h:i:s')
  {
    return date($format, time());
  }
}


if (!function_exists('formatTimeStamp')) {
  function formatTimeStamp($timeStampNumber, $format = 'Y-m-d h:i:s')
  {
    return date($format, $timeStampNumber);
  }
}


if (!function_exists('validation_filter_id_card')) {
  /**
   * 身份证号码验证（真正要调用的方法）
   * @param string $id_card 身份证号码
   */
  function validation_filter_id_card($id_card)
  {
    if (strlen($id_card) == 18) {
      $idcard_base = substr($id_card, 0, 17);
      if (idcard_verify_number($idcard_base) != strtoupper(substr($id_card, 17, 1))) {
        return false;
      } else {
        return true;
      }
    } elseif ((strlen($id_card) == 15)) {
      // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
      if (in_array(substr($id_card, 12, 3), array('996', '997', '998', '999')) !== false) {
        $idcard = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
      } else {
        $idcard = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
      }
      $idcard = $idcard . idcard_verify_number($idcard);
      if (strlen($idcard) != 18) {
        return false;
      }
      $idcard_base = substr($idcard, 0, 17);
      if (idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }
}


if (!function_exists('idcard_verify_number')) {
  /**
   * 计算身份证校验码，根据国家标准GB 11643-1999
   * @param string $idcard_base 身份证号码
   */
  function idcard_verify_number($idcard_base)
  {
    try {
      if (strlen($idcard_base) != 17) {
        return false;
      }
      //加权因子
      $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
      //校验码对应值
      $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
      $checksum = 0;
      for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
      }
      $mod = $checksum % 11;
      $verify_number = $verify_number_list[$mod];
      return $verify_number;
    } catch (Exception $e) {
      return false;
    }
  }
}


if (!function_exists('downloadExcel')) {
  /**
   * 导出excel
   * @param string $strTable 表格内容
   * @param string $filename 文件名
   */
  function downloadExcel($strTable, $filename)
  {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=" . $filename . "_" . date('Y-m-d') . ".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html lang="en"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $strTable . '</html>';
  }
}

if (!function_exists('write_log')) {
  function write_log($log, $level = 'fun')
  {
    Log::write($log, $level);
  }
}

if (!function_exists('create_full_log')) {
  /**
   * 快速创建完整的错误日志
   * @param $e
   * @param $text
   * @param string $level
   */
  function create_full_log($e, $text, $level = 'fun')
  {
    $text = $text . ':' . $e->getMessage() . ',文件' . $e->getFile() . ',行号' . $e->getLine() . '时间:' . getNowTime();
    write_log($text, $level);
  }
}

if (!function_exists('create_api_error')) {
  function create_api_error($txt = '接口错误')
  {

    $request = request();
    $requestInfo = [
      'ip' => $request->ip(),
      'method' => $request->method(),
      'host' => $request->host(),
      'uri' => $request->url()
    ];
    $debugInfo = [
      'reason' => $txt,
      'param' => '[ PARAM ] ' . var_export($request->param(), true),
      'header' => '[ HEADER ] ' . var_export($request->header(), true)
    ];
    $info = [];
    foreach ($debugInfo as $row) {
      array_unshift($info, $row);
    }
    $system_log_config = config('log');
    //读取默认配置
    $config = $system_log_config['channels'][$system_log_config['default']];
    // 日志信息封装
    $time = date($config['time_format'], time());
    //DateTime::createFromFormat('0.u00 U', microtime())->setTimezone(new DateTimeZone(date_default_timezone_get()))->format($config['time_format']);
    array_unshift($info, "---------------------------------------------------------------\r\n[{$time}] {$requestInfo['ip']} {$requestInfo['method']} {$requestInfo['host']}{$requestInfo['uri']}");
    foreach ($info as $row) {
      write_log($row, 'api_error');
    }
  }
}


if (!function_exists('arraySort')) {
  /**
   * 二维数组根据某个字段排序
   * @param array $array 要排序的数组
   * @param string $keys 要排序的键字段
   * @param string $sort 排序类型  SORT_ASC     SORT_DESC
   * @return array 排序后的数组
   */
  function arraySort($array, $keys, $sort = SORT_ASC)
  {
    $keysValue = [];
    foreach ($array as $k => $v) {
      $keysValue[$k] = $v[$keys];
    }
    array_multisort($keysValue, $sort, $array);
    return $array;
  }
}


if (!function_exists('filter_price')) {
  function filter_price($num)
  {

    $precision = 2;
    $pow = pow(10, $precision);
    //判断不进1的情况（五后为0且为奇）
    if ((floor($num * $pow * 10) % 5 == 0) && (floor($num * $pow * 10) == $num * $pow * 10) && (floor($num * $pow) % 2 == 0)) {
      $round = floor($num * $pow) / $pow;
    } else {
      $round = round($num, $precision);
    }
    return $round;
  }
}

if (!function_exists('writeln')) {
  function writeln($str)
  {
    echo $str . "\n\r";
  }
}

if (!function_exists('encode')) {
  /**
   * 加密 UTF8 字符串
   * @param string $string
   * @return string
   */
  function encode($string)
  {
    list($chars, $length) = ['', strlen($content = iconv('UTF-8', 'GBK//TRANSLIT', $string))];
    for ($i = 0; $i < $length; $i++) $chars .= str_pad(base_convert(ord($content[$i]), 10, 36), 2, 0, 0);
    return $chars;
  }
}

if (!function_exists('decode')) {
  /**
   * 解密 UTF8 字符串
   * @param string $encode
   * @return string
   */
  function decode($encode)
  {
    $chars = '';
    foreach (str_split($encode, 2) as $char) {
      $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('GBK//TRANSLIT', 'UTF-8', $chars);
  }
}


if (!function_exists('emoji_encode')) {
  /**
   * 编码 Emoji 表情
   * @param string $content
   * @return string
   */
  function emoji_encode($content)
  {
    return Emoji::encode($content);
  }
}

if (!function_exists('emoji_decode')) {
  /**
   * 解析 Emoji 表情
   * @param string $content
   * @return string
   */
  function emoji_decode($content)
  {
    return Emoji::decode($content);
  }
}

if (!function_exists('emoji_clear')) {
  /**
   * 清除 Emoji 表情
   * @param string $content
   * @return string
   */
  function emoji_clear($content)
  {
    return Emoji::clear($content);
  }
}
if (!function_exists('_getFloat')) {
  /**
   * 获取浮点数
   * @param $newspay
   * @param $newsuser
   * @param int $isPercent
   * @return float|int
   */
  function _getFloat($newspay, $newsuser, $isPercent = 0)
  {
    $floatNum = 0;
    if (!empty($newspay) && !empty($newsuser)) {
      $floatNum = round($newspay / $newsuser, 2);
    }
    if ($isPercent == 1) {
      $floatNum = $floatNum * 100;
    }
    return (float)sprintf("%.2f", $floatNum);
  }
}

if (!function_exists('formatGoodsSpec')) {
  /**
   * 转换商品规格的方法
   * @param string $goods_spec 规格字符串
   * @param int $isString 是否需要转化为字符串
   * @return array|false|string|string[]
   */
  function formatGoodsSpec($goods_spec, $isString = 1, $strSeparator = ' ')
  {
    if (!$goods_spec) return $isString == 1 ? '' : [];
    $rt = [];
    $tempArr1 = explode(';;', $goods_spec);
    foreach ($tempArr1 as $row) {
      $tempArr2 = explode('::', $row);
      $rt[] = $tempArr2[1];
    }
    if ($isString == 1) {
      return implode($strSeparator, $rt);
    }
    return $rt;
  }
}


if (!function_exists('new_addslashes')) {
  /**
   * 返回经addslashes处理过的字符串或数组
   * @param string $string 需要处理的字符串或数组
   * @return mixed
   */
  function new_addslashes($string)
  {
    if (!is_array($string)) return addslashes($string);
    foreach ($string as $key => $val) $string[$key] = new_addslashes($val);
    return $string;
  }
}

if (!function_exists('buildFullUrl')) {
  function buildFullUrl($url)
  {
    $parseInfo = parse_url($url);
    return [
      'scheme' => $parseInfo['scheme'],
      'host' => $parseInfo['host'],
      'port' => $parseInfo['port'],
      'user' => $parseInfo['user'],
      'pass' => $parseInfo['pass'],
      'path' => $parseInfo['path'],
      'query' => $parseInfo['query'],
      'fragment' => $parseInfo['fragment'],
      'full' => $parseInfo['scheme']
    ];
  }
}


if (!function_exists('getParamsByUrl')) {
  /**
   * 从字符串中获取参数
   * @param string $url http链接
   * @param string $key 默认为空字符串，返回序列化的对象，如果不是则返回全部
   * @return array|false|mixed|string
   */
  function getParamsByUrl($url, $key = '')
  {
    try {
      $parseInfo = parse_url($url);
      if (!isset($parseInfo['query'])) {
        throw new \think\Exception($url . '中不存在query' . json_encode($parseInfo));
      }
      $queryParts = explode('&', $parseInfo['query']);
      $params = array();
      foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
      }
      if (empty($key)) return $params;
      //可能不存在这个属性
      if (!isset($params[$key])) {
        throw new \think\Exception($url . '中不存在' . $key . '参数');
      }
      return $params[$key];
    } catch (Exception $e) {
      return false;
    }
  }
}


if (!function_exists('deldir')) {
  function deldir($dir)
  {
    //先删除目录下的文件：
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
      if ($file != "." && $file != "..") {
        $fullpath = $dir . "/" . $file;
        if (!is_dir($fullpath)) {
          unlink($fullpath);
        } else {
          deldir($fullpath);
        }
      }
    }

    closedir($dh);
    //删除当前文件夹：
    if (rmdir($dir)) {
      return true;
    } else {
      return false;
    }
  }
}


if (!function_exists('getCsvData')) {
  /**
   * 获取cvs格式文件
   * @param $filePath
   * @return array
   */
  function getCsvData($filePath)
  {
    $handle = fopen($filePath, "rb");
    $data = [];
    while (!feof($handle)) {
      $row = fgetcsv($handle);
      if (isset($row[0]) && is_numeric($row[0])) {
        $data[] = (int)$row[0];
      }
    }
    fclose($handle);
    //$data = eval('return ' . iconv('gb2312', 'utf-8', var_export($data, true)) . ';');  //字符转码操作
    return $data;
  }
}


if (!function_exists('array_paginate_func')) {
  /**
   * 数组分页函数  核心函数  array_slice
   * @param array $array 查询出来的所有数组
   * @param int $page 当前第几页
   * @param int $size 每页多少条数据
   * @param int $order 0 - 不变     1- 反序
   * @return mixed
   */
  function array_paginate_func($array, $page = 1, $size = 10, $order = 0)
  {
    $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面
    $start = ($page - 1) * $size; #计算每次分页的开始位置
    if ($order == 1) {
      $array = array_reverse($array);
    }
    $pagedata = array_slice($array, $start, $size);
    return ['list' => $pagedata, 'total' => count($array)];  #返回查询数据
  }
}


if (!function_exists('array_to_object')) {
  /**
   * 数组 转 对象
   *
   * @param array $arr 数组
   * @return object
   */
  function array_to_object($arr)
  {
    if (gettype($arr) != 'array') {
      return (object)[];
    }
    foreach ($arr as $k => $v) {
      if (gettype($v) == 'array' || getType($v) == 'object') {
        $arr[$k] = (object)array_to_object($v);
      }
    }

    return (object)$arr;
  }
}

if (!function_exists('object_to_array')) {
  /**
   * 对象 转 数组
   *
   * @param object $obj 对象
   * @return array
   */
  function object_to_array($obj)
  {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
      if (gettype($v) == 'resource') {
        return [];
      }
      if (gettype($v) == 'object' || gettype($v) == 'array') {
        $obj[$k] = (array)object_to_array($v);
      }
    }

    return $obj;
  }
}

if (!function_exists('is_week')) {
  function is_week($str)
  {
    if ((date('w', strtotime($str)) == 6) || (date('w', strtotime($str)) == 0)) {
      return true;
    } else {
      return false;
    }
  }
}

if (!function_exists('get_string_number')) {
  function get_string_number($num)
  {
    if ($num < 10) return '0' . $num;
    return $num;
  }
}


if (!function_exists('view_debug')) {
  function view_debug($arg)
  {
    echo '<pre>';
    var_dump($arg);
  }
}



