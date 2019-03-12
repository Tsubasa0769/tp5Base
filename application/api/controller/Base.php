<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\facade\Cache;
class Base extends Controller {

	public $headers = '';
	public $page = 1;
	public $size = 10;
	public $from = 0;

	public function initialize() {
		$this->checkRequestAuth();
	}

	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T14:24:16+0800
	 * @Describe 检查请求是否合法
	 * @return   [type]
	 */
	public function checkRequestAuth() {
		$headers = request()->header();

		if(empty($headers['sign'])) {
			throw new ApiException('sign不存在', 400);
		}

		if(!IAuth::checkSignPass($headers)) {
			throw new ApiException('授权码sign失败', 401);
		}
		Cache::set($headers['sign'], 1, config('appApi.app_sign_cache_time'));

		$this->headers = $headers;
	}

	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T14:20:03+0800
	 * @Describe 获取分页内容
	 * @param    [type]
	 * @return   [type]
	 */
	public function getPageAndSize($data) {
		$this->page = !empty($data['page']) ? $data['page'] : 1;
		$this->size = !empty($data['size']) ? $data['size'] : 10;
		$this->from = ($this->page - 1) * $this->size;
	}
}
