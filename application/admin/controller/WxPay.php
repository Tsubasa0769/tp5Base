<?php
namespace app\admin\controller;
class WxPay {
	//二维码支付
	public function native(){
		require_once "../extend/WxPay/lib/WxPay.NativePay.php";
		require_once "../extend/WxPay/lib/phpqrcode.php";
		$notify = new \NativePay();
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no("sdkphp123456789".date("YmdHis"));
		$input->SetTotal_fee("10100");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 7200));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/notify.php");
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id("123456789");
		$result = $notify->GetPayUrl($input);
		// var_dump($result);
		$url2 = $result["code_url"];
		Header("Content-type: image/png");
		\QRcode::png($url2);
		exit(0);
	}
}
