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
use think\facade\App;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

class Index extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        Nathan::AdminLogin(1);
        $logout = input('get.logout','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
            'photo' => Nathan::get_qqphoto($web['webqq']),
            'Version' => Config::get('Auth.app_version'),
        ]);
        if ($logout){
            session('adminname',null);
            session('adminloginip',null);
            Nathan::success('退出成功');
        }
        return view();
    }

    public function login()
    {
        Nathan::AdminLogin(2);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view();
    }

    public function console()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $thinkphp = App::version();
        if (Nathan::is_mobile()){
            $area = '60%';
        }else{
            $area = '30%';
        }
        View::assign([
            'web' => $web,
            'ThinkPHP_Version' => 'ThinkPHP V'.$thinkphp,
            'Version' => Config::get('Auth.app_version'),
            'Copyright' => Config::get('copyright'),
            'System' => Config::get('system'),
            'user' => Db::table('user')->count(),
            'sms' => Db::table('list')->count(),
            'public_sms' => Db::table('public')->count(),
            'card' => Db::table('card')->count(),
            'area' => $area,
        ]);
        return view();
    }

    public function web()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
            'template' => Nathan::getFolderList(App::getRootPath().'app/index/view'),
        ]);
        if (Request::isPost()){
            foreach (Request::post() as $k => $v) {
                $data[$k] = $v;
                Db::table('config')->where('id',1)->save($data);
            }
            Nathan::success('修改成功');
        }
        return view();
    }

    public function self()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $data = Db::table('admin')->where('adminname',session('adminname'))->find();
        View::assign([
            'web' => $web,
            'photo' => Nathan::get_qqphoto($data['qq']),
            'self' => $data,
            'hitokoto' => Nathan::hitokoto(),
        ]);
        return view();
    }

    public function sms_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view();
    }

    public function public_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view();
    }

    public function pay_log()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view();
    }

    public function order_list()
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view();
    }

}