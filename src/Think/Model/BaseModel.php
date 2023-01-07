<?php
use think\model;

class BaseModel extends model
{
    /**
     * 快速创建实例
     * @return BaseModel
     */
    public static function mk($data = [])
    {
        return new static ($data);
    }


    /**
     * 快速返回model的db引用
     * @param $data
     * @return \think\db\Query
     */
    public static function mkDb($data = [])
    {
        $instance = new static ($data);
        return \Qingclouds\Thinklib\Facade\Db::name($instance->db()->getName());
    }
}