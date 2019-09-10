<?php
namespace app\index\Controller;
use think\Controller;
header('Content-type:text/html;charset=utf-8');
class Wx extends Controller {
	const APPKEY = 'wxdf2982c8f9eff5fc';
	const APPSECRET = 'c3887065dddc9cb3f62ddc5ad14218ea';
    public function index(){//用来设置接口的配置信息
    	$nonce=input('get.nonce');
    	$timestamp=input('get.timestamp');
    	$token="zc_weixin";
    	$echostr=input('get.echostr');
    	$signature=input('get.signature');
    	$arr=array($nonce,$timestamp,$token);
    	sort($arr);
    	$str = sha1(implode($arr));
    	if($str==$signature && $echostr){//echostr应该在配置url的时候才有
    		echo $echostr;
    		exit;
    	}else{
    		$this->responseMsg();
    	}
	}

	//接收事件推送处理
	public function responseMsg(){
		try{
	        $xml = file_get_contents('php://input');
	        $postArr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	        //----------------------关注公众号\点击自定义菜单\扫二维码触发-------------------
			if(strtolower($postArr['MsgType']) == 'event'){
				if(strtolower($postArr['Event']) == 'subscribe'){
					$this->addErrorMsg('wx_error.txt',json_encode($postArr));
					//----------------------扫描二维码，未关注的事件推送----------------------
					if(!empty($postArr['EventKey'])){
						$content = explode('_',$postArr['EventKey'])[1].'未关注';
						$this->responseText($postArr,$content);
					}else{
						$content = '欢迎使用';
						$this->responseText($postArr,$content);						
					}	
				}
				//----------------------扫描二维码，已关注的事件推送----------------------	
				if(strtolower($postArr['Event']) == 'scan'){
					$content = $postArr['EventKey'];
					$this->responseText($postArr,$content);
				}			
			}	
			//----------------------接收普通消息触发----------------------
			if(strtolower($postArr['MsgType']) == 'text') $this->responseKeyWord($postArr);
		}catch (\Exception $e){
			$this->addErrorMsg('wx_error.txt',$e->getMessage());
			echo '';
		}

	}


	//关键词回复
	protected function responseKeyWord($postArr){
		$wxContent = trim($postArr['Content']);
		if($wxContent != 'tuwen'){
			switch($wxContent){
				case 'feel':
					$content = 'is awesome';
					break;
				default:
					$content = '小编在休息中';
					break;
			}
			$this->responseText($postArr,$content);
		}else{
			$arr=array(
					array(
						"title"=>"hao123",
						"description"=>"hao123",
						"picUrl"=>"https://www.baidu.com/img/bd_logo1.png",
						"url"=>"http://www.imooc.com"
					),				
					array(
						"title"=>"baidu",
						"description"=>"baidu",
						"picUrl"=>"https://www.baidu.com/img/bd_logo1.png",
						"url"=>"http://www.baidu.com"
					)					
			);			
			$this->responseArticle($postArr,$arr);
		}

	}
//-------------------------------二维码----------------------------------------------------------------------------------	
	//这个应该是临时的二维码
	public function createQRCode(){
		$access_token = $this->getAccessToken();
		$tem_url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		//$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
		// {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
		$arr = array(
			"expire_seconds"=>604800,
			"action_name"=>"QR_SCENE",
			"action_info"=>array(
				"scene"=>array(
					"scene_id"=>3120
				)
			)
		);
		$arr = json_encode($arr,JSON_UNESCAPED_UNICODE);
		$res = $this->http_post($tem_url,$arr);
		$res = json_decode($res,true);
		$src = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($res['ticket']);
		echo "<img width='120' height='120' src='".$src."'>";
	}
//-------------------------------用户信息----------------------------------------------------------------------------------
	//直接根据公众号进行获取
	public function getUserInfo(){
		$openid = 'oCayB1vJ96QrH2Y7z7ops82vrS_Q';
		$access_token = $this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid;
		$res = $this->http_get($url);
		$res = json_decode($res,true);
		print_r($res);
	}

	public function getOpenId(){
		$redirect_uri = $this->curPageURL(true);
		$redirect_uri = urlencode($redirect_uri);
		$code = input('get.code','');
		if(!$code){
			$codeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPKEY.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
			$this->redirect($codeUrl);			
		}
		$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.self::APPKEY.'&secret='.self::APPSECRET.'&code='.$code.'&grant_type=authorization_code';
		$res = json_decode($this->http_get($token_url),true);
		var_dump($res);
	}
	//网页授权获取
	public function getUserInfo2(){
		$redirect_uri = $this->curPageURL(true);
		$redirect_uri = urlencode($redirect_uri);
		$code = input('get.code','');
		if(!$code){
			$codeUrl='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPKEY.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
			$this->redirect($codeUrl);
		}
		$token_url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::APPKEY."&secret=".self::APPSECRET."&code=".$code."&grant_type=authorization_code";
		$res = http_get($token_url);
		$res = json_decode($res,true);
		$user_info_url="https://api.weixin.qq.com/sns/userinfo?access_token=".$res['access_token']."&openid=".$res['openid']."&lang=zh_CN";
		$res = http_get($user_info_url);
		$res = json_decode($res,true);	
		var_dump($res);	
	}

//-------------------------------消息管理-----------------------------------
	//发送模板消息
	public function sendTemplateMsg(){
		$access_token = $this->getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$arr = array(
			'touser'=>'oCayB1vJ96QrH2Y7z7ops82vrS_Q',
			'template_id'=>'Yr0SK74ex4nHcto1Wh9kAxxBwLkI-qPzvEze88ePL-U',
			'url'=>'http://www.baidu.com',
			'data'=>array(
				'name'=>array('value'=>'强哥','color'=>'#173177'),
				'money'=>array('value'=>'$123','color'=>'#000'),
				'date'=>array('value'=>date("Y-m-d H:i:s"),'color'=>'#173177')
			)
		);
		$arr = json_encode($arr,JSON_UNESCAPED_UNICODE);
		$res = $this->http_post($url,$arr);
		$res = json_decode($res,true);
		print_r($res);		
	}
	//文本回复
	protected function responseText($postArr,$Content){//推送纯文本事件
		$toUser = $postArr['FromUserName'];
		$fromUser = $postArr['ToUserName'];
		$CreatTime=time();
		$MsgType="text";
		$template="<xml>
				   <ToUserName><![CDATA[%s]]></ToUserName>
				   <FromUserName><![CDATA[%s]]></FromUserName>
				   <CreateTime>%s</CreateTime>
				   <MsgType><![CDATA[%s]]></MsgType>
				   <Content><![CDATA[%s]]></Content>
				   </xml>";
		$str=sprintf($template,$toUser,$fromUser,$CreatTime,$MsgType,$Content);
		echo $str;
	}
	//推送多图文或者单图文
	protected function responseArticle($postArr,$arr){
		$toUser = $postArr['FromUserName'];
		$fromUser = $postArr['ToUserName'];
		$createtime=time();		
		$msgType="news";
		$template= "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<ArticleCount>".count($arr)."</ArticleCount>
					<Articles>";
		foreach($arr as $v){
			$template.=	"<item>
						<Title><![CDATA[".$v['title']."]]></Title> 
						<Description><![CDATA[".$v['description']."]]></Description>
						<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
						<Url><![CDATA[".$v['url']."]]></Url>
						</item>";
		}

		$template.=	"</Articles>
					</xml> ";
		echo sprintf($template,$toUser,$fromUser,$createtime,$msgType);
	}



//-------------------------------页面调用微信-----------------------------------


	public function scan(){
		$signatureData = $this->getSignature();
		$this->assign('wx',$signatureData);
		return view();
	}

	protected function getSignature(){
		$timestamp = time();
		$url = $this->curPageURL(true);
		$jsapi_ticket=$this->getJsapiTicket();
		$noncestr = $this->createRandStr();
	    $string = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url;
	    $signature = sha1($string);
	    return ['appid'=>self::APPKEY,'timestamp'=>$timestamp,'noncestr'=>$noncestr,'signature'=>$signature];
	}
	//产生16位随机字符串
	protected function createRandStr(){
		//产生noncestr
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < 16; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}
	//获取全局票据   这个值也是要保存到数据库才行
	protected function getJsapiTicket(){
		$ticket = cache('wx_js_ticket');
		if(!$ticket){
			$this->addErrorMsg('wx_error.txt','ticket---');
			$access_token = $this->getAccessToken();
			$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
			$res = http_get($url);
			$res = json_decode($res,true);
			cache('wx_js_ticket',$res['ticket'],7000);		
			$ticket = $res['ticket'];	
		}
		return $ticket;
	}


//-------------------------------页面调用微信-----------------------------------







	//获取微信服务IP
	public function get_wx_ip(){
		$token = $this->getAccessToken();
		$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$token;
		$res = json_decode($this->http_get($url));
		var_dump($res);
	}


	//微信全局AccessToken，可进行保存
	public function getAccessToken(){
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::APPKEY.'&secret='.self::APPSECRET;
		$res = json_decode($this->http_get($url),true);
		if(isset($res['errcode'])){
			return show(1,$res['msg']);
		}
		return $res['access_token'];
	}



	//公用方法，可以放到公众文件里
	protected function show($code=0,$msg='',$data=[]){
		return json(['code'=>$code,'data'=>$data,'msg'=>$msg]);
	}

	protected function http_get($url){
			$oCurl = curl_init();
			if(stripos($url,"https://")!==FALSE){
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
				curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
			}
			curl_setopt($oCurl, CURLOPT_URL, $url);
			curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
			$sContent = curl_exec($oCurl);
			$aStatus = curl_getinfo($oCurl);
			curl_close($oCurl);
			if(intval($aStatus["http_code"])==200){
				return $sContent;
			}else{
				return false;
			}
		//	exit;
	}

 	protected function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}	
    protected function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }


    protected function addErrorMsg($filename,$msg){
    	$date = date('Y-m-d H:i:s');
    	file_put_contents($filename, $date.' '.$msg.PHP_EOL, FILE_APPEND);
    }


 	protected function curPageURL($param=false){
 	$pageURL="http";
 	if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')){//其实感觉这个$_SERVER['HTTPS']=="on"可以改成这个方法判断stripos($url,"https://")
 		$pageURL .='s';
 	}
 	$pageURL .="://";
 	if($_SERVER['SERVER_PORT'] != 80){
 		//$_SERVER['SERVER_NAME']与$_SERVER['HTTP_HOST']基本一致
 		//$_SERVER['PHP_SELF']与$_SERVER['REQUEST_URI']前者不带参数，后者带参数
 		if(!$param){
 			$pageURL .= $_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
 		}else{
 			$pageURL .= $_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
 		}
 	}else{
 		if(!$param){
 			$pageURL .= $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
 		}else{
 			$pageURL .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 		}
 	}
 	return $pageURL;
 }
}

?>