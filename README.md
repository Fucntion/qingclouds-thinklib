# 天下武功,唯快不破
## 宗旨
快一点,更快一点，再快一点
## thinkphp6
方便thinkphp6下快速开发

## 常用方法

## 文件与文件夹操作

## 上传类

## 时间类

## 中间件
### 日志中间件
格式化日志，tp6自带的格式没有tp5时代的好用

### 快速搭建CURD的api
```php
<?php

namespace app\api\controller;

use Qingclouds\Thinklib\Api\JSDBC;

class Query
{

  /**
   * curd
   */
  public function jsdbc()
  {

    $jsdbc = new JSDBC();
    $jsdbc->query();
  }

}

