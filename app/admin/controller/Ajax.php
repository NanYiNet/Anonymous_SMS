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
use app\Nathan\lib\Api;
use app\Nathan\lib\Nathan;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;
use think\facade\Filesystem;
use think\facade\Request;
use think\facade\Validate;

class Ajax extends BaseController
{
    public function clear() {

        Nathan::AdminLogin(1);
        if (Nathan::delete_dir_file(app()->getRootPath()  . 'runtime')) {
            Nathan::success('清除缓存成功');
        } else {
            Nathan::error('清除缓存失败');
        }
    }

    public function upload()
    {
        Nathan::AdminLogin(1);
        $file = Request::file('file');
        $validate  = Validate::rule([
            'file' => 'file|fileExt:jpg,png,gif',
        ]);
        $result = $validate->check([
            'file' => $file
        ]);
        if ($result){
            $nathan_upload = Filesystem::disk('public')->putFile('upload',$file);
            $nathan_uploadurl = Filesystem::getDiskConfig('public','url').'/'.str_replace('\\','/',$nathan_upload);
            $jsondata = ['status' => '1','info' => '上传成功','location' => Request::domain().$nathan_uploadurl];
            return json($jsondata);
        } else {
            $nathan_errorinfo = $validate->getError();
            $jsondata = ['status' => '0','info' => '文件不符合要求，原因：'.$nathan_errorinfo,'location' => '文件不符合要求'];
            return json($jsondata);
        }
    }

    public function system_debug(){
        Nathan::AdminLogin(1);
        if(input('post.state','','htmlspecialchars')=='1'){
            $val='true';
        }else{
            $val='false';
        }
        Env::offsetSet('APP_DEBUG',$val);
        Env::offsetSet('DATABASE_DEBUG',$val);
        $envPath = root_path() . DIRECTORY_SEPARATOR . '.env';
        $envinidata = Env::get();
        $inicontent = Nathan::arr_trinsform_ini($envinidata);
        $fp = fopen($envPath, "w") or  Nathan::error("Couldn't open $envPath");
        fputs($fp,$inicontent);
        fclose($fp);
        Nathan::success('系统提醒您：操作成功');
    }

    public function system_feedback()
    {
        Nathan::AdminLogin(1);
        $webname = input('post.webname','','htmlspecialchars');
        $qq = input('post.qq','','htmlspecialchars');
        $url = input('post.url','','htmlspecialchars');
        $title = input('post.title','','htmlspecialchars');
        $content = input('post.content','','htmlspecialchars');
        if (empty($webname || $qq || $url || $title || $content)){
            Nathan::error('所需内容不能为空!');
        }
        $YunUrl = 'http://';
        $YunData = array(
            'app' => '你的匿名来信',
            'webname' => $webname,
            'qq' => $qq,
            'url' => $url,
            'title' => $title,
            'content' => $content
        );
        $resultdata = Nathan::request_post($YunUrl,$YunData);
        $resultdata = json_decode($resultdata,true);
        if ($resultdata['code'] == 1){
            Nathan::success('提交问题反馈成功！');
        }else{
            Nathan::error('提交问题反馈失败！<br>原因：'.$resultdata['msg']);
        }
    }


    public function login()
    {
        Nathan::AdminLogin(2);
        $adminname = input('post.adminname','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $code = input('post.code','','htmlspecialchars');
        $ip = Nathan::ip();
        $logincity = Nathan::getcity($ip);
        $login['loginip'] = $ip;
        $login['logincity'] = $logincity;
        $login['logintime'] = Nathan::get_date(2);
        $data = Db::table('admin')->where('adminname',$adminname)->find();
        try {
            validate(\app\admin\validate\Nathan::class)->scene('login')->check(Request::post());
        } catch (ValidateException $e) {
            return json(['code' => 0,'msg' => $e->getMessage()]);
        }
        $codedata = Validate::rule([
            'captcha|验证码'=>'require|captcha'
        ]);
        $result = $codedata->check([
            'captcha' => $code
        ]);
        $error = $codedata->getError();
        if ($result != $codedata){
            Nathan::error($error);
        }
        if (empty($data)){
            Nathan::error('管理员不存在，请核对后重新登录！');
        }elseif (!password_verify($password,$data['password'])) {
            Nathan::error('密码错误');
        }elseif ($data['status'] != 1){
            Nathan::error('此管理员已被禁封，请核对后重新登录！');
        }else{
            Db::table('admin')->where('adminname',$adminname)->update($login);
            session('adminname',$adminname);
            session('adminloginip',$ip);
            Nathan::success('登录成功，你好：'.$adminname.' 请稍后...');
        }
    }

    public function config($act)
    {
        Nathan::AdminLogin(1);
        $web = Db::table('config')->where('id',1)->find();
        switch ($act){
            case 'menu':
                return json(Config::get('menu'));
                break;
            default:
                return json(['code' => 0,'msg' => 'No Act']);
                break;
        }
    }

    public function self()
    {
        Nathan::AdminLogin(1);
        $password = input('post.password','','htmlspecialchars');
        $data = [
            'adminname' => input('post.adminname','','htmlspecialchars'),
            'qq' => input('post.qq','','htmlspecialchars'),
            'email' => input('post.email','','htmlspecialchars'),
        ];
        if (!$data['adminname']) {
            Nathan::error('所需信息不得为空!');
        }
        $admin = Db::table('admin')->where('adminname',$data['adminname'])->find();
        $admindata = Db::table('admin')->where('id',$admin['id'])->find();
        if (!empty($password)) {
            if (password_verify($password,$admindata['password'])) {
                Nathan::error('亲！新密码不能与原密码相同哦~');
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $update = Db::table('admin')->where('id',$admin['id'])->update($data);
        if ($update){
            session('adminname',null);
            session('adminloginip',null);
            Nathan::success('修改管理员：'.$admindata['adminname'].' 成功，请重新登录~');
        }else{
            Nathan::error('修改失败，原因未做修改');
        }
    }

    public function ip_add()
    {
        Nathan::AdminLogin(1);
        $ip = input('post.ip','','htmlspecialchars');
        $ipdata = Db::table('ip')->where('ip',$ip)->find();
        $data = [
            'ip' => $ip,
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        if (empty($ip)){
            Nathan::error('所需内容不能为空');
        }elseif ($ipdata){
            Nathan::error('此内容已存在，无法添加！');
        }
        $insert =  Db::table('ip')->insert($data);
        if ($insert){
            Nathan::success('添加成功！');
        }else{
            Nathan::error('添加失败，原因未知！');
        }
    }

    public function ip_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ip = input('post.ip','','htmlspecialchars');
        $ipdata = Db::table('ip')->where('ip',$ip)->find();
        $data = Db::table('ip')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误','index/ip_words',3);
        }elseif (empty($data)){
            Nathan::error('信息不存在','index/ip_words',3);
        }
        if (empty($ip)){
            Nathan::error('所需内容不能为空');
        }elseif ($ipdata){
            Nathan::error('此内容已存在，无法更新！');
        }
        $update =  Db::table('ip')->where('id',$id)->update(['ip' => $ip]);
        if ($update){
            Nathan::success('更新成功！');
        }else{
            Nathan::error('更新失败，原因未知！');
        }
    }
    
    public function ip_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('ip');
        if (input("get.ip")) {
            $data['ip'] = input("get.ip");
            $listdata = Db::table('ip')->where($data);
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function ip_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('ip')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('ip')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function ip_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('ip')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('ip')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function user_add()
    {
        Nathan::AdminLogin(1);
        $username = input('post.username','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $money = input('post.money','','htmlspecialchars');
        $userdata = Db::table('user')->where('username',$username)->find();
        try {
            validate(\app\index\validate\Nathan::class)->scene('useradd')->check(Request::post());
        } catch (ValidateException $e) {
            return json(['code' => 0,'msg' => $e->getMessage()]);
        }
        $data = [
            'username' => $username,
            'password'=> password_hash($password, PASSWORD_DEFAULT),
            'money' => $money,
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        if (empty($username)){
            Nathan::error('所需内容不能为空');
        }elseif ($userdata){
            Nathan::error('此内容已存在，无法添加！');
        }elseif($money < 0){
            Nathan::error('余额不得小于0');
        }
        $insert =  Db::table('user')->insert($data);
        if ($insert){
            Nathan::success('添加成功！');
        }else{
            Nathan::error('添加失败，原因未知！');
        }
    }

    public function user_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $username = input('post.username','','htmlspecialchars');
        $money = input('post.money','','htmlspecialchars');
        $password = input('post.password','','htmlspecialchars');
        $data = [
            'username' => $username,
            'money' => $money,
        ];
        try {
            validate(\app\index\validate\Nathan::class)->scene('useredit')->check(Request::post());
        } catch (ValidateException $e) {
            return json(['code' => 0,'msg' => $e->getMessage()]);
        }
        $msgdata = Db::table('user')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误','index/ip_words',3);
        }elseif (empty($msgdata)){
            Nathan::error('信息不存在','index/ip_words',3);
        }elseif (empty($username)) {
            Nathan::error('所需信息不得为空!');
        }elseif($money < 0){
            Nathan::error('余额不得小于0');
        }
        if ($password) {
            if (password_verify($password,$msgdata['password'])) {
                Nathan::error('亲！新密码不能与原密码相同哦~');
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $update =  Db::table('user')->where('id',$id)->update($data);
        if ($update){
            Nathan::success('更新成功！');
        }else{
            Nathan::error('更新失败，原因未修改！');
        }
    }

    public function user_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('user');
        if (input("get.username")) {
            $data['username'] = input("get.username");
            $listdata = Db::table('user')->where($data);
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function user_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('user')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('user')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function user_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('user')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('user')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function sms_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('list');
        $type = input("get.type");
        $value = input("get.value");
        if ($type) {
            if ($type == 'username'){
                $listdata = Db::table('list')->where($type,$value);
            }elseif ($type == 'phone'){
                $listdata = Db::table('list')->where($type,$value);
            }elseif ($type == 'order'){
                $listdata = Db::table('list')->where($type,$value);
            }
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function sms_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('list')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('list')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function public_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('public');
        $type = input("get.type");
        $value = input("get.value");
        if ($type) {
            if ($type == 'name'){
                $listdata = Db::table('public')->where($type,$value);
            }elseif ($type == 'phone'){
                $listdata = Db::table('public')->where($type,$value);
            }
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function public_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('public')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('public')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function type_add()
    {
        Nathan::AdminLogin(1);
        $name = input('post.name','','htmlspecialchars');
        $namedata = Db::table('type')->where('name',$name)->find();
        $data = [
            'name' => $name,
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        if (empty($name)){
            Nathan::error('所需内容不能为空');
        }elseif ($namedata){
            Nathan::error('此内容已存在，无法添加！');
        }
        $insert =  Db::table('type')->insert($data);
        if ($insert){
            Nathan::success('添加成功！');
        }else{
            Nathan::error('添加失败，原因未知！');
        }
    }

    public function type_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $name = input('post.name','','htmlspecialchars');
        $typedata = Db::table('type')->where('name',$name)->find();
        $data = Db::table('type')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误');
        }elseif (empty($data)){
            Nathan::error('信息不存在');
        }
        if (empty($name)){
            Nathan::error('所需内容不能为空');
        }elseif ($typedata){
            Nathan::error('此内容已存在，无法更新！');
        }
        $update =  Db::table('type')->where('id',$id)->update(['name' => $name]);
        if ($update){
            Nathan::success('更新成功！');
        }else{
            Nathan::error('更新失败，原因未知！');
        }
    }

    public function type_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('type');
        if (input("get.name")) {
            $data['name'] = input("get.name");
            $listdata = Db::table('type')->where($data);
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function type_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('type')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('type')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function type_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('type')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('type')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function word_add()
    {
        Nathan::AdminLogin(1);
        $data = [
            'typeid' => input('post.typeid','','htmlspecialchars'),
            'content' => input('post.content','','htmlspecialchars'),
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        $worddata = Db::table('word')->where([
            'typeid' => $data['typeid'],
            'content' => $data['content'],
        ])->find();
        if (!$data['content']) {
            Nathan::error('所需信息不得为空!');
        }elseif ($worddata){
            Nathan::error('当前信息已存在!');
        }
        $insert = Db::table('word')->insert($data);
        if ($insert){
            Nathan::success('添加成功');
        }else{
            Nathan::error('添加失败，原因未知');
        }
    }

    public function word_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $data = [
            'typeid' => input('post.typeid','','htmlspecialchars'),
            'content' => input('post.content','','htmlspecialchars'),
        ];
        if (!$id) {
            Nathan::error('参数错误');
        }elseif (!$data['content']) {
            Nathan::error('所需信息不得为空!');
        }
        $worddata = Db::table('word')->where('id',$id)->find();
        if (empty($worddata)) {
            Nathan::error('信息不存在');
        }
        $update = Db::table('word')->where('id',$id)->update($data);
        if ($update){
            Nathan::success('修改成功');
        }else{
            Nathan::error('修改失败，原因未做修改');
        }
    }

    public function word_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('word')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('word')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function word_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('word')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('word')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function word_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('word');
        if (input("get.content")) {
            $listdata = Db::table('word')->whereLike('content','%'.input("get.content").'%');
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function card_add()
    {
        Nathan::AdminLogin(1);
        $money = input('post.money','','htmlspecialchars');
        $count = input('post.count','','htmlspecialchars');
        $prefix = input('post.prefix','','htmlspecialchars');
        if ($count > 10){
            Nathan::error('一次生成不能大于10张！');
        }elseif ($count < 1){
            Nathan::error('一次生成最少1张！');
        }elseif ($money <= 0){
            Nathan::error('充值卡金额不能小于等于0！');
        }
        for ($i = 1;$i <= $count;$i++) {
            if (empty($prefix)){
                $key = Nathan::randomString(4, 4);
            }else{
                $key = $prefix.'_'.Nathan::randomString(4, 4);
            }
            $data['key'] = $key;
            $data['money'] = $money;
            $data['date'] = Nathan::get_date(2);
            $data['status'] = 1;
            $insert = Db::table('card')->insert($data);
        }
        if ($insert){
            Nathan::success('生成'.$count.'张卡密成功');
        }else{
            Nathan::error('生成失败，原因未知');
        }
    }

    public function card_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('card');
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function card_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('card')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('card')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function kamidel_all()
    {
        Nathan::AdminLogin(1);
        $delete = Db::table('card')->delete(true);
        if ($delete){
            Nathan::success('删除成功');
        }else{
            Nathan::error('删除失败，原因未知！');
        }
    }

    public function kamidel_all_no()
    {
        Nathan::AdminLogin(1);
        $delete = Db::table('card')->where('status',0)->delete();
        if ($delete){
            Nathan::success('删除成功');
        }else{
            Nathan::error('删除失败，原因未知！');
        }
    }

    public function notice_add()
    {
        Nathan::AdminLogin(1);
        $data = [
            'title' => input('post.title','','htmlspecialchars'),
            'content' => input('post.content','','htmlspecialchars'),
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        $noticedata = Db::table('notice')->where('title',$data['title'])->find();
        if (!$data['title']) {
            Nathan::error('所需信息不得为空!');
        }elseif ($noticedata){
            Nathan::error('当前公告已存在!');
        }
        $insert = Db::table('notice')->insert($data);
        if ($insert){
            Nathan::success('添加公告：'.$data['title'].' 成功');
        }else{
            Nathan::error('添加失败，原因未知');
        }
    }

    public function notice_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $data = [
            'title' => input('post.title','','htmlspecialchars'),
            'content' => input('post.content','','htmlspecialchars'),
        ];
        if (!$id) {
            Nathan::error('参数错误');
        }elseif (!$data['title']) {
            Nathan::error('所需信息不得为空!');
        }
        $noticedata = Db::table('notice')->where('id',$id)->find();
        if (empty($noticedata)) {
            Nathan::error('公告不存在');
        }
        $insert = Db::table('notice')->where('id',$id)->update($data);
        if ($insert){
            Nathan::success('修改公告：'.$noticedata['title'].' 成功');
        }else{
            Nathan::error('修改失败，原因未做修改');
        }
    }

    public function notice_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('notice')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('notice')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function notice_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('notice')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('notice')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function notice_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('notice');
        if (input("get.title")) {
            $data['title'] = input("get.title");
            $listdata = Db::table('notice')->where($data);
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function link_add()
    {
        Nathan::AdminLogin(1);
        $name = input('post.name','','htmlspecialchars');
        $linkdata = Db::table('link')->where('name',$name)->find();
        $data = [
            'name' => $name,
            'url' => input('post.url','','htmlspecialchars'),
            'img' => input('post.img','','htmlspecialchars'),
            'date' => Nathan::get_date(2),
            'status' => input('post.status','','htmlspecialchars'),
        ];
        if (empty($name)){
            Nathan::error('所需内容不能为空');
        }elseif ($linkdata){
            Nathan::error('此内容已存在，无法添加！');
        }
        $insert =  Db::table('link')->insert($data);
        if ($insert){
            Nathan::success('添加成功！');
        }else{
            Nathan::error('添加失败，原因未知！');
        }
    }

    public function link_edit()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $name = input('post.name','','htmlspecialchars');
        $url = input('post.url','','htmlspecialchars');
        $img = input('post.img','','htmlspecialchars');
        $data = Db::table('link')->where('id',$id)->find();
        if (!$id){
            Nathan::error('参数错误');
        }elseif (empty($data)){
            Nathan::error('信息不存在');
        }
        if (empty($name)){
            Nathan::error('所需内容不能为空');
        }
        $update =  Db::table('link')->where('id',$id)->update(['name' => $name,'url' => $url,'img' => $img]);
        if ($update){
            Nathan::success('更新成功！');
        }else{
            Nathan::error('更新失败，原因未知！');
        }
    }

    public function link_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('link');
        if (input("get.name")) {
            $data['name'] = input("get.name");
            $listdata = Db::table('link')->where($data);
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function link_status()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $statedata = Db::table('link')->where('id',$id)->find();
        if($statedata){
            $status = $statedata['status'] == 1?0:1;
            $update = Db::table('link')->where('id',$id)->update(['status' => $status]);
            if ($update){
                Nathan::success('修改ID：'.$id.' 状态成功');
            }else{
                Nathan::error('修改失败，原因未知');
            }
        }
    }

    public function link_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('link')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('link')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function pay_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('pay');
        $type = input("get.type");
        $value = input("get.value");
        if ($type) {
            if ($type == 'username'){
                $listdata = Db::table('pay')->where($type,$value);
            }elseif ($type == 'out_trade_no'){
                $listdata = Db::table('pay')->where($type,$value);
            }
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function pay_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('pay')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('pay')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function order_list()
    {
        Nathan::AdminLogin(1);
        $page = input("get.page")?input("get.page"):1;
        $page = intval($page);
        $limit = input("get.limit")?input("get.limit"):1;
        $limit = intval($limit);
        $start = $limit*($page-1);
        $listdata = Db::table('order');
        $type = input("get.type");
        $value = input("get.value");
        if ($type) {
            if ($type == 'username'){
                $listdata = Db::table('order')->where($type,$value);
            }elseif ($type == 'ali_pay'){
                $listdata = Db::table('order')->where($type,$value);
            }elseif ($type == 'ali_name'){
                $listdata = Db::table('order')->where($type,$value);
            }
            return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
        }
        return json(['code' => '0','msg' => '','count' => $listdata->count(),'data' => $listdata->limit($start,$limit)->select()]);
    }

    public function order_del()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        $ids = input('post.ids','','htmlspecialchars');
        if ($id) {
            if (Db::table('order')->where('id',$id)->delete()) {
                Nathan::success('删除成功');
            } else {
                Nathan::error('删除失败，原因未知');
            }
        } else {
            foreach (explode(",",$ids) as $nathan) {
                Db::table('order')->where('id',$nathan)->delete();
            }
            Nathan::success('删除成功');
        }
    }

    public function allowed()
    {
        Nathan::AdminLogin(1);
        $id = input('post.id','','htmlspecialchars');
        if (empty($id)){
            Nathan::error('订单ID不能为空！');
        }
        $order = Db::table('order')->where('id',$id)->find();
        if (empty($order)){
            Nathan::error('订单不存在！');
        }elseif ($order['status'] != 0){
            Nathan::error('该订单不可提现！');
        }
        $update = Db::table('order')->where('id',$id)->update(['status' => 1]);
        if ($update){
            Nathan::success('操作提现成功，状态已修改！');
        }else{
            Nathan::error('操作提现失败，原因未知');
        }
    }

    public function resend()
    {
        Nathan::AdminLogin(1);
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
}