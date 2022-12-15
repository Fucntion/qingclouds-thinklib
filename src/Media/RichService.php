<?php

namespace Qingclouds\Thinklib\Media;


/**
 * Class RichService
 * @package service
 */
class RichService
{
  private $imgExt = ['jpg', 'png'];

  /**
   * 过滤富文本中的图片，替换成本站oss的地址
   * @param $content
   * @param $dist
   * @param $url
   * @return array|void
   */
//  public function saveImgFromList($content, $dist = null, $url = null)
//  {
//
//    try {
//      $list = $this->getImgByReg($content);
//      //豁免的地址,包含的qingclouds-server
//      $accessUrl = sysconf('storage_oss_domain');
//
//      $upLogic = new UploadService();
//
//      $imgRetList = [];
//      foreach ($list as $key => $val) {
//        if (strpos($val['src'], $accessUrl) !== false) {
//          $arr = explode('/', $val['src']);
//          $name = array_pop($arr);
//          $list[$key]['src'] = $name;
//          continue;
//        }
//        $arr = explode('.', $val['src']);
//        $ext = array_pop($arr);
//        if (!$ext || !in_array($ext, $this->imgExt)) {
//          $ext = 'jpg';
//        }
//        $name = md5(uniqid()) . '.' . $ext;
//        $list[$key]['src'] = $name;
//
//        //$file = file_get_contents($val['src']);
//        //file_put_contents($dist . $name, $file);
//        $tempFilePath = $upLogic->downFile($val['src']);
//        $rt = $upLogic->upload($tempFilePath);
//        if ($rt['code'] === 1) {
//          $imgRetList[] = $rt['url'];
//        } else {
//          echo $rt['msg'];
//          die;
//        }
//
//      }
//
//      $newImgInfo = $this->replaceImg($list, $imgRetList);
//      $newImgTags = $newImgInfo['newImgTags'];
//      $newImgUrls = $newImgInfo['newImgUrls'];
//
  /*      $patterns = array('/<img\s.*?>/');*/
//      $callback = function ($matches) use (&$newImgTags) {
//        $matches[0] = array_shift($newImgTags);
//        return $matches[0];
//      };
//
//      $res = array();
//      $res['content'] = preg_replace_callback($patterns, $callback, $content);
//      $res['image_urls'] = $newImgUrls;
//
//      return $res;
//    }catch (\Exception $e){
//      $res = array();
//      $res['content'] = $content;
//      return $res;
//    }
//
//  }
  public function downloadImageFromWeixin($url)
  {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_NOBODY, 0); // 只取body头
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $package = curl_exec($ch);
    $httpinfo = curl_getinfo($ch);

    curl_close($ch);
    $imageAll = array_merge(array(
      'imgBody' => $package
    ), $httpinfo);
    return $imageAll;
  }



  protected function getImgByReg($str)
  {
    $list = array();
    $c1 = preg_match_all('/<img\s.*?>/', $str, $m1);
    for ($i = 0; $i < $c1; $i++) {
      $c2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(["\'])(.*?)(?=\2))|([^\/\s]*))/', $m1[0][$i], $m2);
      for ($j = 0; $j < $c2; $j++) {
        $list[$i][$m2[1][$j]] = !empty($m2[4][$j]) ? $m2[4][$j] : $m2[3][$j];
      }
    }
    return $list;
  }

//  public function testWxImg()
//  {
//
//    $url = 'http://mmbiz.qpic.cn/mmbiz_png/lnZQE7bzTT3K4D0GyAU57qhXyaJtiaXyageBpT8qbY8DgchGmOtye53hDwG9987Ze7hBurA4H13q2jyqll9b7PQ/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1';
//    $imageAll = $this->downloadImageFromWeixin($url);
//    // 保存图像
//    if ($imageAll['content_type'] == 'image/gif') {
//      file_put_contents('php.gif', $imageAll["imgBody"]);
//    } elseif ($imageAll['content_type'] == 'image/webp') {
//      file_put_contents('php.webp', $imageAll["imgBody"]);
//      $im = imagecreatefromwebp('./php.webp');
//      imagejpeg($im, './example.jpg', 100);
//      imagedestroy($im);
//    }
//  }

  protected function replaceImg($list, $replaceImg)
  {
    $newImgTags = array();
    $newImgUrls = array();

    foreach ($list as $key => $val) {
      $imgTag = '<img ';
      foreach ($val as $attr => $v) {
        if ($attr === 'src') {
          $imgTag .= $attr . '="' . $replaceImg[$key] . '" ';
          $newImgUrls[] = $replaceImg[$key];
        } else {
          $imgTag .= $attr . '="' . $v . '" ';
        }
      }
      $imgTag .= ' >';

      $newImgTags[$key] = $imgTag;
    }

    return array('newImgTags' => $newImgTags, 'newImgUrls' => $newImgUrls);
  }


}