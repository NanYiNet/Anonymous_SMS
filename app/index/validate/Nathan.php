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
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Nathan extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require|min:5|max:16',
        'password' => 'require|min:5|max:16',
        'money' => 'require|number',
        'code' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '账号不能为空',
        'username.min' => '请输入不低于5位的账号',
        'username.max' => '请输入5-16位的账号',
        'password.require' => '密码不能为空',
        'password.min' => '请输入不低于5位的用户密码',
        'password.max' => '请输入5-16位的用户密码',
        'code.require' => '验证码不能为空',
        'money.require' => '余额不能为空',
        'money.number' => '请输入纯数字，不包含负数和小数点',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        'login'  =>  ['username', 'password','code'],
        'useradd'  =>  ['username','password'],
        'useredit'  =>  ['username'],
    ];
}
