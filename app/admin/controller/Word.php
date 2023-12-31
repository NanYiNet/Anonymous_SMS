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

class Word extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function word_add()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $typedata = Db::table('type')->where('status',1)->select();
        View::assign([
            'web' => $web,
            'typedata' => $typedata,
        ]);
        return view('index/word_add');
    }

    public function word_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('get.id','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $data = Db::table('word')->where('id',$id)->find();
        $typedata = Db::table('type')->where('status',1)->select();
        if (!$id){
            Nathan::error('参数错误','index/word_list',3);
        }elseif (empty($data)){
            Nathan::error('信息不存在','index/word_list',3);
        }
        View::assign([
            'web' => $web,
            'data' => $data,
            'typedata' => $typedata,
        ]);
        return view('index/word_edit');
    }

    public function word_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/word_list');
    }

}