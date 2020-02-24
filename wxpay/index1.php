<?php
/**
 * Created by PhpStorm.
 * User: 90853
 * Date: 2019/8/8
 * Time: 9:56
 */
//微信支付类
class Wxpay
{
    const APPID = '';                   //开发者编号
    const APPSECRET = ''; //开发者密钥
    const MCHID = '';                           //商户号
    const KEY = '';       //支付密钥

    /**
     * 签名方法
     * @param  array $params 参数集合
     */
    public function getSign($params)
    {
        #1.对参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串
        ksort($params);//按照键名对关联数组进行升序
        #2.连接商户key:使用http_build_query将key=>value的数组转变为url字符串
        $str = http_build_query($params).'&key='.self::KEY;
        #3.生成sign并转成大写：//使用strtoupper函数将字符串转成大写
        return strtoupper(md5($str));
        #4.最终的提交xml：
    }

    /**
     * 生成二维码内容
     * @param  int $pid 商品ID
     * @return string
     */
    public function getUrl($pid)
    {
        #1.声明请求参数
        $apiParam = [
            'appid' => self::APPID,
            'mch_id' => self::MCHID,
            'time_stamp' =>time(),//时间戳
            'nonce_str' => md5(time()),//随机字符串
            'product_id' => $pid,//商品id
        ];
        $apiParam['sign'] = $this->getSign($apiParam);
        #2.生成二维码内容 //使用http_build_query将key=>value的数组转变为url字符串
        return 'weixin：//wxpay/bizpayurl?'.http_build_query($apiParam);
    }
}
// 1.引入类
include './phpqrcode/phpqrcode.php';
// 2.生成二维码链接
$url = (new Wxpay)->getUrl(1);
QRcode::png($url);
