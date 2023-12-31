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

class EPay
{
    public function __construct($epaylink, $epaypid, $epaykey, $name, $sitename, $money, $type, $notify_url, $return_url) {
        $out_trade_no = date('YmdHis').mt_rand(000,999);
        $ary = array(
            'money' => $money,
            'type' => $type,
            'out_trade_no' => $out_trade_no,
            'notify_url' => $notify_url,
            'pid' => $epaypid,
            'name' => $name,
            'return_url' => $return_url,
            'sitename' => $sitename
        );
        $pay['out_trade_no'] = $out_trade_no;
        $pay['money'] = $money;
        $pay['username'] = session('username');
        $pay['date'] = Nathan::get_date(4);
        $pay['type'] = $type;
        if (!Db::table('pay')->insert($pay)) {
            Nathan::error('创建订单失败', 'index/pay');
        }
        $this->submit($ary, $epaylink, $epaykey);
    }
    public function submit($ary, $epaylink, $epaykey) {
        $arylink = $this->arylink($this->arysort($ary));
        $ary['sign'] = $this->md5sign($arylink, $epaykey);
        $ary['sign_type'] = 'MD5';

        $action = $epaylink.'submit.php?_input_charset=utf-8';

        $html = '<form id="paysubmit" name="paysubmit" action="'.$action.'">';
        while (list ($key, $val) = Nathan::fun_adm_each($ary)) {
            $html.= '<input hidden name="'.$key.'" value="'.$val.'">';
        }
        $html .= '<input hidden type="submit"></form><script>document.forms["paysubmit"].submit();</script>';

        exit($html);
    }
    public static function returnp($out_trade_no) {
        $data = self::getpay($out_trade_no);
        if ($data['code'] == 1 && $data['status'] == 1) {
            $out_trade_no = $data['out_trade_no'];
            $paydata = Db::table('pay')->where(array('out_trade_no' => $out_trade_no))->find();
            if (empty($paydata)) {
                Nathan::error('订单不存在，有疑问请联系管理员。', 'index/pay');
            }
            if ($paydata['status'] == 1) {
                Nathan::error('订单已充值购买，有疑问请联系管理员。', 'index/pay');
            }
            if ($paydata['money'] != $data['money']) {
                Nathan::error('充值金额有误，有疑问请联系管理员。', 'index/pay');
            }
            $user = Db::table('user')->where('username',session('username'))->find();
            $update =  Db::table('user')->where(array('username' => session('username')))->update(['money'=> $user['money'] + $paydata['money']]);
            if (!$update) {
                Nathan::error('充值失败，有疑问请联系管理员。', 'index/pay');
            }
            if (!Db::table('pay')->where(array('out_trade_no' => $paydata['out_trade_no']))->update(['status'=>1])) {
                Nathan::error('修改状态失败，有疑问请联系管理员。', 'index/pay');
            }
            Nathan::success('充值成功，请稍后...', 'index/user');
        } else {
            Nathan::error('验证失败，订单不存在或未支付。', 'index/pay');
        }
    }
    public function arysort($ary) {
        ksort($ary);
        reset($ary);
        return $ary;
    }
    public function arylink($ary) {
        $arg = "";
        while (list ($key, $val) = Nathan::fun_adm_each ($ary)) {
            $arg.= $key."=".$val."&";
        }
        $arg = substr($arg,0,strlen($arg)-1);
        $arg = stripslashes($arg);
        return $arg;
    }
    public function md5sign($arylink, $key) {
        $arylink = $arylink.$key;
        return md5($arylink);
    }
    public static function getpay($out_trade_no) {
        $config = Db::table('config')->where('id',1)->find();
        $epaylink = $config['epaylink'];
        $epaypid = $config['epaypid'];
        $epaykey = $config['epaykey'];
        $apiurl = $epaylink.'api.php?act=order&pid='.$epaypid.'&key='.$epaykey.'&out_trade_no='.$out_trade_no;
        return json_decode(Nathan::curl_request($apiurl),true);
    }
}