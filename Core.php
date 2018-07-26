<?php
class Core
{

	protected $accessKeyId;//Aliyun accessKeyId
	protected $secretkey;//Aliyun  secretkey
	protected $action;//API action
	protected $signatureMethod = 'HMAC-SHA1';
	protected $signatureVersion = '1.0';
	protected $version = '2014-05-26';
	protected $format = 'json';

	function  __construct($accessKeyId,$secretkey,$action)
	{
		$this->accessKeyId = $accessKeyId;
		$this->secretkey = $secretkey;
		$this->action =$action;
	}

    /**
     * @param $action_param 除公共参数，action API请求参数
     */
	public function getData($url,$action_param = [])
    {
        date_default_timezone_set("GMT");
        $Timestamp = date('Y-m-d\TH:i:s\Z',time());
        $signatureNonce = $this->getRandStr(6,1);
        $param =
            [
                'Action'    =>  $this->action,
                'AccessKeyId'   =>  $this->accessKeyId,
                'SignatureMethod'   =>  $this->signatureMethod,
                'SignatureVersion'  =>  $this->signatureVersion,
                'SignatureNonce'    =>  $signatureNonce,
                'Timestamp' =>  $Timestamp,
                'Version' =>    $this->version,
                'Format'    =>  $this->format
            ];
        $url_param = array_merge($param,$action_param);
        $signature = $this->getSignature($url_param);
        $str_url_param = '';
        foreach ($url_param as $key => $val)
        {
            $str_url_param .= '&'.$key.'='.$val;
        }
        $str_url_param = substr($str_url_param,1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "$url?$str_url_param&Signature=".$signature);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        return $data;
    }


    //获取随机字符串
    protected function getRandStr($length,$num = 0)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if($num == 1)
        {
            $str = '0123456789';
        }
        $len = strlen($str)-1;
        $rand_str = '';
        for($i = 0 ;$i < $length ;++$i)
        {
            $num = mt_rand(0,$len);
            $rand_str .=$str[$num];
        }
        return $rand_str;
    }

    // 获取签名
    protected function getSignature($param)
    {
        ksort($param);
        $arr = [];
        foreach ($param as $key => $val)
        {
            $arr[] = $this->percentEncode($key).'='.$this->percentEncode($val);
        }
        $str = join('&',$arr);
        $stringToSign = 'POST&%2F&'.$this->percentEncode($str);
        $signature = base64_encode(hash_hmac('sha1',utf8_encode($stringToSign),$this->secretkey.'&',true));
        return urlencode($signature);
    }

    //格式化字符
	protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }



}