<?php

declare (strict_types=1);



//if (!function_exists('test')) {
//  function test($data)
//  {
//    var_dump($data);
//  }
//}

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

if (!function_exists('saveFile')) {
  /**
   * 设备或配置系统参数
   * @param string $name 参数名称
   * @param boolean $value 无值为获取
   * @return string|boolean
   */
  function sysconf($name, $value = null)
  {
    static $data = [];
    list($field, $raw) = explode('|', "$name|");
    if ($value !== null) {
      list($row, $data) = [['name' => $field, 'value' => $value], []];
      return \Qingclouds\Thinklib\Tools\Data::save('SystemConfig', $row, 'name');
    }
    if (empty($data)) {
      $db = new \think\Db();
      $data = $db->name('SystemConfig')->column('value', 'name');
    }
    return isset($data[$field]) ? (strtolower($raw) === 'raw' ? $data[$field] : htmlspecialchars($data[$field])) : '';
  }
}




