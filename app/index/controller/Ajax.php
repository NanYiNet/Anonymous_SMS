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
use app\Nathan\lib\Api;
use app\Nathan\lib\Nathan;
use think\facade\Db;
use think\facade\Request;
use think\facade\Validate;
use think\exception\ValidateException;

class Ajax extends BaseController
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
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

    public function check_username()
    {
        Nathan::UserLogin(2);
        $username = input('post.username','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',$username)->find();
        if (empty($userdata)){
            Nathan::error('该账号不存在，请核对后重新登录！');
        }else{
            Nathan::success('该账号存在，请继续！');
        }
    }

    public function update_username()
    {
        Nathan::UserLogin(1);
        $username = input('post.username','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',session('username'))->find();
        $user = Db::table('user')->where('username',$username)->find();
        if (empty($username)){
            Nathan::error('账号不能为空！');
        }
        if($user){
            Nathan::error('该用户名已存在，无法修改！');
        }
        if (empty($userdata)){
            Nathan::error('该账号不存在，请核对后重新登录！');
        }else{
            Db::table('user')->where('username',session('username'))->update(['username' => $username]);
            session('username',$username);
            Nathan::success('修改成功！');
        }
    }

    public function update_password()
    {
        Nathan::UserLogin(1);
        $old_password = input('post.old_password','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $password_two = input('post.password_two','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',session('username'))->find();
        if (empty($old_password || $password || $password_two)){
            Nathan::error('账号不能为空！');
        }elseif (strlen($password) < 5 || strlen($password) > 16){
            Nathan::error('新密码长度不得低于5位不得高于16位！');
        }elseif ($password != $password_two){
            Nathan::error('两次密码不一致！');
        }elseif (!password_verify($old_password,$userdata['password'])){
            Nathan::error('原密码错误！');
        }elseif (password_verify($password,$userdata['password'])) {
            Nathan::error('新密码不能与原密码相同哦~');
        }else{
            Db::table('user')->where('username',session('username'))->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
            session('username',null);
            session('loginip',null);
            Nathan::success('修改成功，请重新登录！');
        }

    }

    public function login()
    {
        Nathan::UserLogin(2);
        $username = input('post.username','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $code = input('post.code','','htmlspecialchars');
        $ip = Nathan::ip();
        $logincity = Nathan::getcity($ip);
        $userlogin['loginip'] = $ip;
        $userlogin['logincity'] = $logincity;
        $userlogin['logintime'] = Nathan::get_date(2);
        $userdata = Db::table('user')->where('username',$username)->find();
        //验证器规则
        $codedata = Validate::rule([
            'captcha|验证码'=>'require|captcha'
        ]);
        //和表单数据对比
        $result = $codedata->check([
            'captcha' => $code
        ]);
        $error = $codedata->getError();
        if ($result != $codedata){
            Nathan::error($error);
        }
        try {
            validate(\app\index\validate\Nathan::class)->scene('login')->check(Request::post());
        } catch (ValidateException $e) {
            return json(['code' => 0,'msg' => $e->getMessage()]);
        }
        if ($username == $password) {
            Nathan::error('账号密码不得相同!');
        }
        if ($userdata){
            if (empty($userdata)){
                Nathan::error('用户不存在，请核对后重新登录！');
            }
            if (!password_verify($password,$userdata['password'])) {
                Nathan::error('密码错误');
            }elseif ($userdata['status'] != 1){
                Nathan::error('此用户已被禁封，请核对后重新登录！');
            }else{
                Db::table('user')->where('username',$username)->update($userlogin);
                session('username',$username);
                session('loginip',$ip);
                Nathan::success('登录成功，你好：'.$username.' 请稍后...');
            }
        }else{
            $data = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'date' => Nathan::get_date(2),
                'loginip' => $ip,
                'logincity' => $logincity,
                'logintime' => Nathan::get_date(2),
                'money' => 0,
                'status' => 1,
            ];
            $config = Db::table('config')->where('id',1)->find();
            if ($config['regstate'] != 1){
                Nathan::error('当前网站未开启注册功能');
            }
            if ($userdata){
                Nathan::error('此用户已存在！');
            }
            $insert = Db::table('user')->insert($data);
            if ($insert){
                session('username',$username);
                session('loginip',$ip);
                Nathan::success('恭喜：'.$username.' 注册成功，已为您自动登录~');
            }else{
                Nathan::error('注册失败，原因未知');
            }
        }
    }

    public function withdrawal()
    {
        Nathan::UserLogin(1);
        $ali_pay = input('post.ali_pay','','htmlspecialchars');
        $ali_name = input('post.ali_name','','htmlspecialchars');
        $money = input('post.money','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $userdata = Db::table('user')->where('username',session('username'))->find();
        if (empty($ali_pay || $ali_name || $money)){
            Nathan::error('账号不能为空！');
        }elseif ($web['withdrawalstate'] != 1){
            Nathan::error('当前网站未开启提现功能');
        }elseif ($money < $web['least_money']){
            Nathan::error('单次最少提现金额为￥'.$web['least_money']);
        }elseif ($userdata['money'] < $money){
            Nathan::error('余额不足，请核对后重新提现！');
        }
        $data = [
            'username' => session('username'),
            'ali_pay' => $ali_pay,
            'ali_name' => $ali_name,
            'money' => $money,
            'date' => Nathan::get_date(2),
            'status' => 0,
        ];
        $insert = Db::table('order')->insert($data);
        if ($insert){
            Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] - $money]);
            Nathan::success('申请提现成功，请等待管理员打款！');
        }else{
            Nathan::error('申请提现失败，原因未知');
        }
    }

    public function revoke()
    {
        Nathan::UserLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',session('username'))->find();
        if (empty($id)){
            Nathan::error('订单ID不能为空！');
        }
        $order = Db::table('order')->where('id',$id)->find();
        if (empty($order)){
            Nathan::error('订单不存在！');
        }elseif ($order['status'] != 0){
            Nathan::error('该订单不可撤销提现！');
        }elseif ($order['username'] != session('username')){
            Nathan::error('该订单不属于您！');
        }
        $update = Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] + $order['money']]);
        if ($update){
            Db::table('order')->where('id',$id)->delete();
            Nathan::success('撤销提现成功，余额已退回！');
        }else{
            Nathan::error('撤销提现失败，原因未知');
        }
    }

    public function resend()
    {
        Nathan::UserLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $web = Db::table('config')->where('id',1)->find();
        $list = Db::table('list')->where('id',$id)->find();
        $userdata = Db::table('user')->where('username',$list['username'])->find();
        if (empty($id)){
            Nathan::error('订单ID不能为空！');
        }elseif (empty($list)){
            Nathan::error('订单不存在！');
        }elseif ($list['status'] != 0 && $list['status'] != 2){
            Nathan::error('该订单不可重新发送！');
        }
        if($web['smstype'] === 'close'){
            Nathan::error('发信通道已关闭，无法操作！');
        }elseif($web['smstype'] === 'phone'){
            if (empty($list['phone'])){
                Nathan::error('提交的手机号不能为空！');
            }elseif (!Nathan::check_mobile($list['phone'])){
                Nathan::error('提交的手机号不正确！');
            }
            $api = new Api();
            $CheckUser = $api->CheckUser($web['yun_username'],$web['yun_key']);
            $CheckUser = json_decode($CheckUser,true);
            $SendSms = $api->SendSms($list['phone'],'【'.$web['autograph'].'】'.$list['content'].'，来自“'.$list['name'].'”发送，'.$web['tail'],$web['yun_username'],$web['yun_key']);
            $SendSms = json_decode($SendSms,true);
            if (empty($web['yun_username']) || empty($web['yun_key']) || empty($web['autograph'])){
                Nathan::error('云端短信账号或云端短信密钥或手机短信签名未设置，无法操作！');
            }elseif ($CheckUser['code'] == 1){
                if ($SendSms['code'] == 1){
                    Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] - $list['money']]);
                    Db::table('list')->where('id',$id)->update(['status' => 1]);
                    Nathan::success('发送成功！扣费 '.$list['money'].' 元');
                }else{
                    Nathan::error($SendSms['msg']);
                }
            }else{
                Nathan::error($CheckUser['msg']);
            }
        }elseif($web['smstype'] === 'email'){
            if (empty($list['phone'])){
                Nathan::error('提交的邮箱不能为空！');
            }elseif (!Nathan::check_mail($list['phone'])){
                Nathan::error('提交的邮箱不正确！');
            }
            $api = new Api();
            $SendSms = $api->SendEmail($list['phone'],$list['content'].'，'.$web['tail'],'一份来自“'.$list['name'].'”的邮件');
            $SendSms = json_decode($SendSms,true);
            if (empty($web['mail_smtp']) || empty($web['mail_name']) || empty($web['mail_password']) || empty($web['mail_port'])){
                Nathan::error('SMTP信息未设置，无法操作！');
            }elseif ($SendSms['code'] == 1){
                Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] - $list['money']]);
                Db::table('list')->where('id',$id)->update(['status' => 1]);
                Nathan::success('发送成功！扣费 '.$list['money'].' 元');
            }else{
                Nathan::error($SendSms['msg']);
            }
        }
    }

    public function sendsms()
    {
        Nathan::UserLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        $mobile = input('post.mobile','','htmlspecialchars');
        $email = input('post.email','','htmlspecialchars');
        $content = input('post.content','','htmlspecialchars');
        $public = input('post.public','','htmlspecialchars');
        $anonymous = input('post.anonymous','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',session('username'))->find();
        if ($anonymous == 1){
            $name = '匿名';
        }else{
            $name = input('post.name','','htmlspecialchars');
        }
        if (empty($name) || empty($content)){
            Nathan::error('提交的内容不能为空！');
        }
        $black_words = explode(',',$web['black_words']);
        foreach ($black_words as $value){
            if (strpos($content,$value) !== false){
                Nathan::error('您的提交内容包含非法字符，请检查后重新提交！');
            }
        }
        if($userdata['money'] < $web['sms_money']){
            Nathan::error('您的余额不足，请充值后再发送！');
        }
        $publicdata = [
            'name' => $name,
            'phone' => $mobile,
            'content' => $content,
            'date' => Nathan::get_date(2),
        ];
        if($web['smstype'] === 'close'){
            Nathan::error('发信通道已关闭，无法操作！');
        }elseif($web['smstype'] === 'phone'){
            if (empty($mobile)){
                Nathan::error('提交的手机号不能为空！');
            }elseif (!Nathan::check_mobile($mobile)){
                Nathan::error('提交的手机号不正确！');
            }
            $api = new Api();
            $CheckUser = $api->CheckUser($web['yun_username'],$web['yun_key']);
            $CheckUser = json_decode($CheckUser,true);
            $SendSms = $api->SendSms($mobile,'【'.$web['autograph'].'】'.$content.'，来自“'.$name.'”发送，'.$web['tail'],$web['yun_username'],$web['yun_key']);
            $SendSms = json_decode($SendSms,true);
            if (empty($web['yun_username']) || empty($web['yun_key']) || empty($web['autograph'])){
                Nathan::error('云端短信账号或云端短信密钥或手机短信签名未设置，无法操作！');
            }elseif ($CheckUser['code'] == 1){
                if ($SendSms['code'] == 1){
                    Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] - $web['sms_money']]);
                    Db::table('list')->insert([
                        'username' => session('username'),
                        'name' => $name,
                        'phone' => $mobile,
                        'content' => $content,
                        'money' => $web['sms_money'],
                        'order' => date('YmdHis').mt_rand(000,999),
                        'date' => Nathan::get_date(2),
                        'status' => 1,
                    ]);
                    if ($public == 1){
                        Db::table('public')->insert($publicdata);
                    }
                    Nathan::success('发送成功！扣费 '.$web['sms_money'].' 元');
                }else{
                    Db::table('list')->insert([
                        'username' => session('username'),
                        'name' => $name,
                        'phone' => $mobile,
                        'content' => $content,
                        'money' => $web['sms_money'],
                        'order' => date('YmdHis').mt_rand(000,999),
                        'date' => Nathan::get_date(2),
                        'status' => 0,
                    ]);
                    Nathan::error($SendSms['msg']);
                }
            }else{
                Nathan::error($CheckUser['msg']);
            }
        }elseif($web['smstype'] === 'email'){
            if (empty($email)){
                Nathan::error('提交的邮箱不能为空！');
            }elseif (!Nathan::check_mail($email)){
                Nathan::error('提交的邮箱不正确！');
            }
            $api = new Api();
            $SendSms = $api->SendEmail($email,$content.'，'.$web['tail'],'一份来自“'.$name.'”的邮件');
            $SendSms = json_decode($SendSms,true);
            if (empty($web['mail_smtp']) || empty($web['mail_name']) || empty($web['mail_password']) || empty($web['mail_port'])){
                Nathan::error('SMTP信息未设置，无法操作！');
            }elseif ($SendSms['code'] == 1){
                Db::table('user')->where('username',session('username'))->update(['money' => $userdata['money'] - $web['sms_money']]);
                Db::table('list')->insert([
                    'username' => session('username'),
                    'name' => $name,
                    'phone' => $email,
                    'content' => $content,
                    'money' => $web['sms_money'],
                    'order' => date('YmdHis').mt_rand(000,999),
                    'date' => Nathan::get_date(2),
                    'status' => 1,
                ]);
                if ($public == 1){
                    Db::table('public')->insert($publicdata);
                }
                Nathan::success('发送成功！扣费 '.$web['sms_money'].' 元');
            }else{
                Db::table('list')->insert([
                    'username' => session('username'),
                    'name' => $name,
                    'phone' => $email,
                    'content' => $content,
                    'money' => $web['sms_money'],
                    'order' => date('YmdHis').mt_rand(000,999),
                    'date' => Nathan::get_date(2),
                    'status' => 0,
                ]);
                Nathan::error($SendSms['msg']);
            }
        }
    }

}