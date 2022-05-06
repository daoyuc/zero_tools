<?php
/*
 * @Author: daoyu
 * @Date: 2022-04-09 17:30:27
 * @LastEditors: daoyu
 * @LastEditTime: 2022-04-26 17:11:30
 * @FilePath: \zero_tool\app\Custom\func.php
 * @Description:公共函数库
 * Mail mouha@vip.qq.com
 * Copyright (c) 2022 by phpf5.com, All Rights Reserved.
 */

/**
 * 判断字符串的组成类别
 *
 * @param string $str
 * @return string
 */
function stringType($str)
{
    $strA = trim($str);
    $lenA = strlen($strA);
    $lenB = mb_strlen($strA, "utf-8");
    if ($lenA === $lenB) { //如果strlen返回的字符长度和mb_strlen以当前编码计算的长度一致，可以判断是纯英文字符串
        return "1"; //全英文
    } else {
        if ($lenA % $lenB == 0) {
            return "2"; //全中文
        } else {
            return "3"; //中英混合
        }
    }
}

/**
 * @description:生成随机码
 * @param int $length
 * @param bool $onlyNumber
 * @param string $surString
 * @return string
 */
function randStr($length = 4, $onlyNumber = false, $surString = '')
{
    $chars = true === $onlyNumber ? '012356789' : (null === $onlyNumber ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' :
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ2356789');
    $chars = $chars . $surString;
    $password = '';
    for ($i = 0; $i < $length; ++$i) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    return $password;
}
