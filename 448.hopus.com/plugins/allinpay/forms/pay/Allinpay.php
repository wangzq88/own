<?php
/**
 * @copyright ©2019 浙江禾匠信息科技
 * Created by PhpStorm.
 * User: Andy - Wangjie
 * Date: 2019/8/7
 * Time: 15:04
 */

namespace app\plugins\allinpay\forms\pay;

use app\plugins\allinpay\models\AllinpayConfig;
use app\helpers\CurlHelper;

class Allinpay
{
    public $appid;
    public $version;
    public $cusid;
    public $sub_appid;
    public $orgid;
    public $alipay_app_id;
    public $public_key;
    public $private_key;
	public $curl;
    public $data;
    public $order_result;
    public $tt_sign;
    public $ip;

    const UNIFIED_ORDER = 'https://vsp.allinpay.com/apiweb/unitorder';

    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }		
        $this->curl = CurlHelper::getInstance();
        $this->ip = \Yii::$app->request->userIP;
    }

	 //RSA签名
     public function makeSign(array $array){	 
        ksort($array);
        $bufSignSrc = $this->toUrlParams($array);
        if(openssl_sign($bufSignSrc, $signature, $this->private_key )){
            //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
            return base64_encode($signature);
        }
        return false;
    }

    private function toUrlParams(array $array)
	{
		$buff = "";
		foreach ($array as $k => $v)
		{
			if($v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		$buff = trim($buff, "&");
		return $buff;
	}

    public function checkSign($array)
    {
        $sign = $array['sign'];
		unset($array['sign']);
		ksort($array);
		$bufSignSrc = $this->ToUrlParams($array);
        $result = openssl_verify($bufSignSrc,base64_decode($sign), $this->public_key );
        return $result;          
    }   

    public function genData($param)
    {
        $this->unifiedOrder($param);
        $url = self::UNIFIED_ORDER."/pay";
        try {
           $rspArray = $this->curl->httpPost($url, null, $this->data);
        } catch (\Exception $e) {
            \Yii::error($e);
        }
        if($rspArray && is_array($rspArray)){
            if($this->checkSign($rspArray)){
                return $rspArray;
            }
        }
		return false;
    }

    public function cancel($param)
    {
        $this->data = [
            'appid' => $this->appid,
            'cusid' => $this->cusid,
            'version' => $this->version,
            'signtype' => 'RSA',
            'randomstr' => uniqid()
        ];
        $this->data = array_merge($this->data,$param);
        //签名  
        $this->data["sign"] = $this->makeSign($this->data);   
        $url = self::UNIFIED_ORDER . "/cancel";
        try {
            \Yii::warning($this->data);
            $rspArray = $this->curl->httpPost($url, null, $this->data);
         } catch (\Exception $e) {
             \Yii::error($e);
         }        
         if($rspArray && is_array($rspArray)){
            if($this->checkSign($rspArray)){
                return $rspArray;
            }
        }
		return false;
    }

    public function refund($param)
    {
        $this->data = [
            'appid' => $this->appid,
            'cusid' => $this->cusid,
            'version' => $this->version,
            'signtype' => 'RSA',
            'randomstr' => uniqid()
        ];
        $this->data = array_merge($this->data,$param);
        //签名  
        $this->data["sign"] = $this->makeSign($this->data);   
        $url = self::UNIFIED_ORDER . "/refund";
        try {
            \Yii::warning($this->data);
            $rspArray = $this->curl->httpPost($url, null, $this->data);
         } catch (\Exception $e) {
             \Yii::error($e);
         }        
         if($rspArray && is_array($rspArray)){
            if($this->checkSign($rspArray)){
                return $rspArray;
            }
        }
		return false;
    }    

    private function unifiedOrder($param)
    { 
        $this->data = [
            'appid' => $this->appid,
            'cusid' => $this->cusid,
            'version' => $this->version,
            'orgid' => $this->orgid,
            'sub_appid' => $this->sub_appid,
        ];
        $this->data = array_merge($this->data,$param);
        //签名  
        $this->data["sign"] = $this->makeSign($this->data);        
    }
}