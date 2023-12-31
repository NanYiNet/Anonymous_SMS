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
namespace app\index\controller;

use app\BaseController;
use app\Nathan\lib\Nathan;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use think\facade\View;
use app\Nathan\lib\EPay;
class Index extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $Nathan_Auth = Config::get('Auth');
        if (empty($Nathan_Auth) || empty($Nathan_Auth['app_name']) || empty($Nathan_Auth['authcode'])){
            Nathan::exitContent(View(Config('app.Auth'),[
                'msg' => '检测到有文件缺失，可尝试重新下载程序',
                'date' => date('Y')
            ]));
        }elseif ($Nathan_Auth['app_author'] != 'Nathan' || $Nathan_Auth['app_qq'] != '2322796106' || $Nathan_Auth['app_url'] != 'auth.nanyinet.com'){
            Nathan::exitContent(View(Config('app.Auth'),[
                'msg' => '请勿修改授权信息文件',
                'date' => date('Y')
            ]));
        }

        $web = Db::table('config')->where('id',1)->find();
        if ($web['status'] != 1){
            Nathan::exitContent(View(Config('app.maintain_tmpl'),[
                'web' => $web,
                'QQ_Contact' => Nathan::get_qq_contact($web['webqq']),
                'date' => date('Y')
            ]));
        }
        $ip = Db::table('ip')->where([
            'status' => 1,
            'ip' => Nathan::ip()
        ])->find();
        if ($ip){
            die('<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
                    <style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style>
                    <div style="padding: 24px 48px;"> <h1>:) </h1>
                    <h3>'.$web['ip_tips'].'</h3>');
        }
    }

    public function index()
    {
        $logout = input('get.logout','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $list = Db::table('link')->where('status',1)->paginate(10);
        View::assign([
            'web' => $web,
            'photo' => Nathan::get_qqphoto($web['webqq']),
            'list' => $list,
        ]);
        if ($logout){
            session('username',null);
            session('loginip',null);
            Nathan::success('退出成功');
        }
        return view($web['template'].'/index');
    }

    public function sendsms()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $list = Db::table('link')->where('status',1)->paginate(10);
        View::assign([
            'web' => $web,
            'photo' => Nathan::get_qqphoto($web['webqq']),
            'list' => $list,
        ]);
        return view($web['template'].'/sendsms');
    }

    public function public_sms()
    {
        $web = Db::table('config')->where('id',1)->find();
        if (empty(input('get.keywords'))) {
            $list = Db::table('public')->paginate(10);
        } else {
            $list = Db::table('public')->whereLike('content','%'.input('get.keywords').'%')->paginate(10);
        }
        View::assign([
            'web' => $web,
            'list' => $list,
            'photo' => Nathan::get_qqphoto($web['webqq']),
        ]);
        return view($web['template'].'/public_sms');
    }

    public function notice()
    {
        $web = Db::table('config')->where('id',1)->find();
        $list = Db::table('notice')->where('status',1)->paginate(10);
        View::assign([
            'web' => $web,
            'photo' => Nathan::get_qqphoto($web['webqq']),
            'list' => $list,
        ]);
        return view($web['template'].'/notice');
    }

    public function login()
    {
        Nathan::UserLogin(2);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view($web['template'].'/login');
    }

    public function user()
    {
        Nathan::UserLogin(1);
        $user = Db::table('user')->where('username',session('username'))->find();
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
            'user' => $user,
            'photo' => Nathan::get_qqphoto($web['webqq']),
        ]);
        return view($web['template'].'/user');
    }

    public function password()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        View::assign([
            'web' => $web,
        ]);
        return view($web['template'].'/password');
    }

    public function send()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $list = Db::table('list')->where('username',session('username'))->paginate(10);
        View::assign([
            'web' => $web,
            'list' => $list,
        ]);
        return view($web['template'].'/send');
    }

    public function pay()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        if ($web['userpay'] == 0) {
            Nathan::error('管理员未开启此功能','index/user',3);
        }
        $out_trade_no = input('get.out_trade_no','','htmlspecialchars');
        $type = input('post.type','','htmlspecialchars');
        $user = Db::table('user')->where('username',session('username'))->find();
        $money = input('post.money','','htmlspecialchars');
        if (!empty($out_trade_no)) {
            EPay::returnp($out_trade_no);
        }
        if ($type == 'card'){
            $key = input('post.key','','htmlspecialchars');
            $card = Db::table('card')->where('key',$key)->find();
            if(empty($card)){
                Nathan::error('卡密不存在','index/pay',3);
            }elseif ($card['status'] != 1){
                Nathan::error('卡密已使用','index/pay',3);
            }else{
                $money = $card['money'];
                $card['status'] = 0;
                $card['usetime'] = date('Y-m-d H:i:s');
                Db::table('card')->where('id',$card['id'])->update($card);
                Db::table('user')->where('username',session('username'))->update(['money'=> $user['money'] + $money]);
                Nathan::success('卡密充值成功','index/pay',3);
            }
        }elseif (!empty($money)) {
            $url = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']. '/index/index/pay';
            if ($web['alipay'] == 'f2f' && $type == 'alipay') {
                $out_trade_no = date('YmdHis').mt_rand(000,999);
                $pay['out_trade_no'] = $out_trade_no;
                $pay['money'] = $money;
                $pay['username'] = session('username');
                $pay['date'] = Nathan::get_date(4);
                $pay['type'] = $type;
                if (!Db::table('pay')->insert($pay)) {
                    Nathan::error('创建订单失败', 'index/pay');
                }
                $pay = new F2fpay($this->app);
                return $pay->topay($out_trade_no, $money);
            }
            if (($web['alipay'] == 'epay' && $type == 'alipay') || ($web['qqpay'] == 'epay' && $type == 'qqpay') || ($web['wxpay'] == 'epay' && $type == 'wxpay')) {
                $epayurl = $web['epaylink'].'api.php?act=query&pid='.$web['epaypid'].'&key='.$web['epaykey'];
                $epaydata = json_decode(Nathan::curl_request($epayurl),true);
                if ($epaydata['code'] != 1) {
                    Nathan::error('易支付配置有误，请联系管理员','index/pay',3);
                }
                new EPay(
                    $web['epaylink'],
                    $web['epaypid'],
                    $web['epaykey'],
                    $web['webname'] . '在线购买',
                    $web['webname'],
                    $money,
                    $type,
                    $url,
                    $url
                );
            }
        }
        View::assign([
            'web' => $web,
            'user' => $user,
        ]);
        return view();
    }

    public function withdrawal()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        if ($web['withdrawalstate'] != 1){
            Nathan::error('当前网站未开启提现功能','index/user',3);
        }
        $list = Db::table('order')->where('username',session('username'))->paginate(10);
        $user = Db::table('user')->where('username',session('username'))->find();
        View::assign([
            'web' => $web,
            'list' => $list,
            'user' => $user,
        ]);
        return view($web['template'].'/withdrawal');
    }

    public function official()
    {
        Nathan::UserLogin(1);
        $type = input('get.type','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $typedata = Db::table('type')->where('status',1)->select();
        $user = Db::table('user')->where('username',session('username'))->find();
        if (!$type) {
            $word = Db::table('word')->where('status',1)->select();
        } elseif($type) {
            $word = Db::table('word')->where([
                'status' => 1,
                'typeid' => $type,
            ])->select();
        }
        View::assign([
            'web' => $web,
            'type' => $typedata,
            'word' => $word,
            'user' => $user,
        ]);
        return view($web['template'].'/official');
    }
}