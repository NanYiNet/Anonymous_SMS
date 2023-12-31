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

class Card extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function card_add()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/card_add');
    }

    public function card_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view('index/card_list');
    }

}