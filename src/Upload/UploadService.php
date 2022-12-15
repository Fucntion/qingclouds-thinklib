<?php

namespace Qingclouds\Thinklib\Upload;

use Exception;
use OSS\Core\OssException;
use OSS\OssClient;
use think\facade\Filesystem;
use think\File;

/**
 * 阿里云oss上传
 */
class UploadService
{


  private $storage_oss_bucket;
  private $storage_oss_keyid;
  private $storage_oss_secret;
  private $storage_oss_domain;

  public function __construct($configs = [])
  {
    $diyConfig = config('alioss');
    if (empty($configs) && !empty($diyConfig)) {
      $configs = $diyConfig;
    }

    if (empty($configs)) throw new Exception('初始化上传类失败');

    $this->storage_oss_bucket = $configs['bucket'];
    $this->storage_oss_keyid = $configs['keyid'];
    $this->storage_oss_secret = $configs['secret'];
    $this->storage_oss_domain = $configs['domain'];

  }

  public static function mk($data = [])
  {
    return new static($data);
  }

  /**
   * @param File $file 前端传来的文件
   * @param false $full
   * @param false $up 是否需要上传
   * @param string $savePath
   * @return array|string
   */
  public function handlerUpFile($file, $full = false, $up = false, $savePath = 'uptemp')
  {
    //runtime/storage/uptemp/20160510/42a79759f284b767dfcb2a0197904287.jpg
    $saveName = Filesystem::putFile($savePath, $file);
    $local_path = app()->getRootPath() . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . $saveName;
    if ($up) {
      return $this->upload($local_path);
    } else {
      if ($full) {
        return [
          'fullPath' => $local_path,
          'saveName' => $saveName
        ];
      } else {
        return $local_path;
      }
    }
  }

  /**
   * @param string|File $local_file_path 可以是路径也可以是文件对象think\File
   * @param null $fileName 自定义文件名称
   * @param null $dir 自定义文件夹
   * @return array
   * @throws OssException
   */
  public function upload($local_file_path, $fileName = null, $dir = null)
  {
    try {

      if (is_string($local_file_path)) {
        $fullPath = $local_file_path;
        $file = new File($local_file_path);
      } else {
        $file = $local_file_path;
        $fullPath = $file->getRealPath() ?: $file->getPathname();
      }

      if (!$file || !$fullPath) return ['code' => 0, 'msg' => 'fullPath获取失败', 'url' => ''];

      if (!$fileName) {
        $fileName = md5(randString(10) . time()) . '.' . $this->getExt($fullPath);
      }

      //if($file && empty($file['info'])){
      //    $file['info'] = ['name'=>$fileName,'filename'=>$fileName];
      //}

      //var_dump($file);die;

      if ($file) {
        $bucket = $this->storage_oss_bucket;

        $full_oss_path = date('Ymd', time());
        //diy文件夹
        if ($dir) {
          $full_oss_path = ($dir . '/' . $full_oss_path);
        }
        $full_oss_path = $full_oss_path . '/' . $fileName;
        $info = $this->getOssInstance()->uploadFile($bucket, $full_oss_path, $fullPath);

        $oss_url = $info['info']['url'];
        $https_oss_url = str_replace('http://', 'https://', $oss_url);
        return ['code' => 1, 'url' => $https_oss_url, 'msg' => '文件上传成功'];
      } else {
        return ['code' => 0, 'msg' => '获取文件失败', 'url' => ''];
      }
    } catch (Exception $e) {
      return ['code' => 0, 'msg' => $e->getMessage(), 'url' => ''];
    }
  }

  private function getExt($url)
  {
    $path = parse_url($url);
    $str = explode('.', $path['path']);
    return end($str);
  }

  private function getOssInstance()
  {
    $keyid = $this->storage_oss_keyid;
    $secret = $this->storage_oss_secret;

    //这样就走了cdn了
    $storage_oss_domain = $this->storage_oss_domain;
    return new OssClient($keyid, $secret, $storage_oss_domain, true);
  }

  /**
   * 直接将任意在线地址转换成oss地址
   * @param $online_url
   * @param null $fileName
   * @return mixed|string
   * @throws OssException
   */
  public function onlineResourceTranslateOss($online_url, $fileName = null)
  {
    $tempFile = $this->downFile($online_url);
    $ret = $this->upload($tempFile, $fileName);
    return $ret['code'] === 1 ? $ret['url'] : '';
  }

  //public function downFile($online_url)
  //{
  //
  //  $result = file_get_contents($online_url);
  //  $filename = uniqid() . '.jpg';            //定义图片名字及格式
  //  $tempFile = saveFile($filename, $result);
  //  return $tempFile;
  //
  //}

  /**
   * 下载远程文件到本地
   * @param $online_url
   * @param $ext
   * @return string
   * @throws \think\Exception
   */
  public function downFile($online_url,$ext='jpg')
  {

    $result = file_get_contents($online_url);

    $fileExt = '';
    try {
      if(strpos($online_url,'.')!==false){
        $fileExt = $this->getExt($online_url);
      }
    }catch (Exception $e){
      $fileExt = '';
    }
    if(empty($fileExt))$fileExt = $ext;
    $filename = uniqid() . '.'.$fileExt;            //定义图片名字及格式
    $tempFile = saveFile($filename, $result);
    return $tempFile;

  }

}