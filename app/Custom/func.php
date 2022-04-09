<?php
//公共函数库

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
 * 生成随机码
 *
 * @param $length
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
