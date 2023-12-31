<?php

/**
 *  支付宝配置参数
 * */
return $alipay_config = array(
    //签名方式,默认为RSA2(RSA2048)
    'sign_type' => "RSA2",

    //编码格式
    'charset' => "UTF-8",

    //最大查询重试次数
    'MaxQueryRetry' => "10",

    //查询间隔
    'QueryDuration' => "3",
    'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
);