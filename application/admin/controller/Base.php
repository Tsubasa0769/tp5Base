<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Request;
class Base extends Controller {
	public function initialize(){
		//判断是否登陆
		// if(!$this->isLogin()){
		// 	$this->error('您尚未登录系统',url('login/index')); 
		// }
	
		// if(!$this->isAuth()){
		// 	$this->error('没有权限');
		// }

	}

	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T09:47:42+0800
	 * @Describe 判断是否登陆
	 * @return   boolean
	 */
	public function isLogin(){
        if(!session('?admin')) return false;
        return true;
	}


	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T10:42:49+0800
	 * @Describe 判断是否有权限
	 * @return   boolean
	 */
	public function isAuth(){
		//判断是否有权限
		$auth = new Auth();
		$userInfo = $auth->getGroups(1);
		$module = Request::module();
		$con = Request::controller();
		$action = Request::action();	
		$name = $con.'/'.$action;
		// echo $name;exit;
		return $auth->check($name,1);
	}
}
