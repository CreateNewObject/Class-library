<?php
/**
 * Created by PhpStorm.
 * User: 90853
 * Date: 2019/8/8
 * Time: 9:56
 */
//xml和数组转换类
class Util
{
    /**
     * PHP发送请求
     * @param string $api      接口地址
     * @param mixed  $postData POST请求数据
     * @return bool|string
     */
    public static function httpRequest($api,$postData)
    {
        //1.初始化
        $ch = curl_init();
        //2.配置
            //2.1设置请求地址
            curl_setopt($ch,CURLOPT_URL,$api);
            //2.2数据流不直接输出
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            //2.3 POST请求
            if($postData){
                curl_setopt($ch,CURLOPT_POST,true);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
            }
            //curl注意事项,如果发送的请求是https,必须禁止服务器端校验SSL证书
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        //3.发送请求
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 将XML文档转化为数组
     * @param string $xml XML 文档
     * @return mixed|string
     */
    public static function XmlToArr($xml)
    {
        if ($xml == '') return '';
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        return$arr;
    }

    /**
     * 将数组转化为XML文档
     * @param array $arr 数组
     * @return string
     */
    public static function ArrToXml($arr)
    {
        if (!is_array($arr) || count($arr) == 0) return '';
        $xml = "<xml>";
        foreach ($arr as $key => $val)
        {
            if(is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
}
//微信支付类
class Wxpay
{
    const APPID = '';                        //开发者编号
    const APPSECRET = '';      //开发者密钥
    const MCHID = '';                                //商户号
    const KEY = '';            //支付密钥
    const NOTIFY_URL = '';  //异步通知回调

    /**
     * 签名方法
     * @param  array $params 参数集合
     */
    public function getSign($params)
    {
        #1.对参数按照key=value的格式，并按照参数名ASCII字典序排序生成字符串
        ksort($params);//按照键名对关联数组进行升序
        #2.连接商户key:使用http_build_query将key=>value的数组转变为url字符串
        $str = urldecode(http_build_query($params).'&key='.self::KEY);
        #3.生成sign并转成大写：//使用strtoupper函数将字符串转成大写
        return strtoupper(md5($str));
        #4.最终的提交xml：
    }

    /**
     * 获取 code_url
     * $link https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
     * @param int $pid   商品ID
     * @param int $price 价格/分
     * @return string
     */
    public function getUrl($pid,$price = 1)
    {
        #1.声明请求参数
        $apiParam = [
            'appid'            => self::APPID,
            'mch_id'           => self::MCHID,              //商户号
            'nonce_str'        => md5(time()),              //随机字符串
            'body'             => '扫码支付模式二',         //商品描述
            'out_trade_no'     => date('YmdHis'),   //商户订单号
            'total_fee'        => $price,                   //标价金额 订单总价 单位(分)
            'spbill_create_ip' => $_SERVER['SERVER_ADDR'],  //终端IP
            'notify_url'       => self::NOTIFY_URL,         //异步通知地址
            'trade_type'       => 'NATIVE',                //交易类型 NATIVE -Native支付
            'product_id'       => $pid,                     //商品id
        ];
        $apiParam['sign'] = $this->getSign($apiParam);
        #2.讲数组转换为xml
        $xml = Util::ArrToXml($apiParam);
        #3.发送请求
        $str = Util::httpRequest('https://api.mch.weixin.qq.com/pay/unifiedorder',$xml);
        $arr = Util::XmlToArr($str);
        if($arr['return_code'] == 'SUCCESS'){
            return $arr['code_url'];
        }else{
            file_put_contents('./http_request_error.txt',$str);
            return false;
        }
    }
}
// 1.引入类
include './phpqrcode/phpqrcode.php';
// 2.生成二维码链接
$url = (new Wxpay)->getUrl(1,1);
// 3.根据二维码内容制作二维码
QRcode::png($url);

