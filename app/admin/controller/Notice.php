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

class Notice extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function notice_add()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/notice_add');
    }

    public function notice_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('get.id','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $data = Db::table('notice')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误','index/notice_list',3);
        }elseif (empty($data)){
            Nathan::error('信息不存在','index/notice_list',3);
        }
        View::assign([
            'web' => $web,
            'data' => $data,
        ]);
        return view('index/notice_edit');
    }

    public function notice_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/notice_list');
    }

}