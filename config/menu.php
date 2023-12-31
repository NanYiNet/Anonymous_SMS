<?php
return array([
    "id"=> 10,
    "title"=> "控制后台",
    "icon"=> "layui-icon layui-icon-console",
    "type"=> 1,
    "openType"=> "_iframe",
    "href" => "/admin/index/console",
],[
    "id" => 1,
    "title" => "系统管理",
    "icon" => "pear-icon pear-icon-electronics",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 11,
        "title"=> "网站配置",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/web"
    ],[
        "id"=> 12,
        "title"=> "个人信息",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/self"
    ],[
        "id"=> 13,
        "title"=> "IP黑名单",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/ip/ip_words"
    ],[
        "id"=> 14,
        "title"=> "调试模式",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/system/system_debug"
    ],[
        "id"=> 15,
        "title"=> "问题反馈",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/system/system_feedback"
    ],[
        "id"=> 16,
        "title"=> "授权官网",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "http://auth.nanyinet.com"
    ])
],[
    "id" => 2,
    "title" => "用户管理",
    "icon" => "pear-icon pear-icon-user",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 21,
        "title"=> "添加用户",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/user/user_add"
    ],[
        "id"=> 22,
        "title"=> "用户列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/user/user_list"
    ])
],[
    "id" => 3,
    "title" => "短信管理",
    "icon" => "pear-icon pear-icon-comment",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 31,
        "title"=> "短信列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/sms_list"
    ],[
        "id"=> 32,
        "title"=> "公开信列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/public_list"
    ])
],[
    "id" => 4,
    "title" => "文案管理",
    "icon" => "pear-icon pear-icon-fabulous",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 41,
        "title"=> "分类添加",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/type/type_add"
    ],[
        "id"=> 42,
        "title"=> "分类列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/type/type_list"
    ],[
        "id"=> 43,
        "title"=> "文案添加",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/word/word_add"
    ],[
        "id"=> 44,
        "title"=> "文案列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/word/word_list"
    ])
],[
    "id" => 5,
    "title" => "卡密管理",
    "icon" => "pear-icon pear-icon-image-text",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 51,
        "title"=> "添加卡密",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/card/card_add"
    ],[
        "id"=> 52,
        "title"=> "卡密列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/card/card_list"
    ])
],[
    "id" => 6,
    "title" => "公告管理",
    "icon" => "pear-icon pear-icon-edit",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 61,
        "title"=> "添加公告",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/notice/notice_add"
    ],[
        "id"=> 62,
        "title"=> "公告列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/notice/notice_list"
    ])
],[
    "id" => 8,
    "title" => "友情管理",
    "icon" => "pear-icon pear-icon-link",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 81,
        "title"=> "添加友情链接",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/link/link_add"
    ],[
        "id"=> 82,
        "title"=> "友情链接列表",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/link/link_list"
    ])
],[
    "id" => 9,
    "title" => "订单管理",
    "icon" => "pear-icon pear-icon-history",
    "type" => 0,
    "href" => "",
    "children" => array([
        "id"=> 91,
        "title"=> "支付记录",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/pay_log"
    ],[
        "id"=> 92,
        "title"=> "提现记录",
        "icon"=> "layui-icon layui-icon-console",
        "type"=> 1,
        "openType"=> "_iframe",
        "href" => "/admin/index/order_list"
    ])
]);