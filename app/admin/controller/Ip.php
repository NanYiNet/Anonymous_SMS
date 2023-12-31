<?php
/*
 * // +--------------------------------------------+
 * // | Name: Nathan-你的匿名来信
 * // +--------------------------------------------+
 * // | Author: Nathan<www.nanyinet.com>
 * // +--------------------------------------------+
 * // | Contact: QQ：2322796106
 * // +--------------------------------------------+
 * // | Created: PHPStorm
 * // +--------------------------------------------+
 * // | Date: 2022年07月27日
 * // +--------------------------------------------+
 */
namespace app\admin\controller;

use app\BaseController;
use app\Nathan\lib\Nathan;
use think\facade\Db;
use think\facade\View;

class Ip extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function ip_words()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/ip_words');
    }

    public function ip_add()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/ip_add');
    }

    public function ip_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('get.id','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $data = Db::table('ip')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误','index/ip_words',3);
        }elseif (empty($data)){
            Nathan::error('信息不存在','index/ip_words',3);
        }
        View::assign([
            'web' => $web,
            'data' => $data,
        ]);
        return view('index/ip_edit');
    }

}