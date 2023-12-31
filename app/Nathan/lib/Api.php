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
namespace app\Nathan\lib;

use think\facade\Db;
use think\facade\Request;

class Api
{
    public static function CheckUser($username,$key)
    {
        $url = '';
        $data = array(
            'username' => $username,
            'key' => $key,
        );
        $resultdata = Nathan::curl_request($url,$data);
        $resultdata = json_decode($resultdata,true);
        if ($resultdata['code'] == 1) {
            $data = json_encode(['code' => 1, 'msg' => $resultdata['msg']]);
        } else {
            $data = json_encode(['code' => 0, 'msg' => $resultdata['msg']]);
        }
        return $data;
    }

    public static function SendSms($phone,$content,$username,$key)
    {
        $url = '';
        $data = array(
            'phone' => $phone,
            'content' => $content,
            'username' => $username,
            'key' => $key,
        );
        $resultdata = Nathan::curl_request($url,$data);
        $resultdata = json_decode($resultdata,true);
        if ($resultdata['code'] == 1) {
            $data = json_encode(['code' => 1, 'msg' => $resultdata['msg']]);
        } else {
            $data = json_encode(['code' => 0, 'msg' => $resultdata['msg']]);
        }
        return $data;
    }

    public static function SendEmail($email,$msg,$title) {
        $web = Db::table('config')->where('id',1)->find();
        $YunEmailUrl = 'http://login.nanyinet.com/index/smtp/YunEmail';
        $YunEmailData = array(
            'url' => Request::host(),
            'webname' => $web['webname'],
            'email' => $email,
            'content' => $msg,
            'title' => $title,
            'mail_smtp' => $web['mail_smtp'],
            'mail_name' => $web['mail_name'],
            'mail_password' => $web['mail_password'],
            'mail_port' => $web['mail_port'],
        );
        $resultdata = Nathan::curl_request($YunEmailUrl,$YunEmailData);
        $resultdata = json_decode($resultdata, TRUE);
        if ($resultdata['code'] == 1) {
            $data = json_encode(['code' => 1, 'msg' => $resultdata['msg']]);
        } else {
            $data = json_encode(['code' => 0, 'msg' => $resultdata['msg']]);
        }
        return $data;
    }

}