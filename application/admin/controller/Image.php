<?php
namespace app\admin\controller;
class Image extends Base {
	/**
	 * @Author   Tsubasa
	 * @DateTime 2019-03-12T11:15:16+0800
	 * @Describe 图片上传
	 * @return   [type]
	 */
	public function upload(){
		//上传验证
		// $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,gif'])->move( '../uploads');
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('image');
	    // 移动到框架应用根目录/uploads/ 目录下
	    $info = $file->move( '../uploads');
	    if($info){
	    	$data = [
	    		'status' => 1,
	    		'message' => 'OK',
	    		'data' => $info->getSavename()
	    	];
	    	echo json_encode($data);
	    }else{
	    	$data = [
	    		'status' => 0,
	    		'message' => '上传失败'
	    	];
	    	echo json_encode($data);
	    }
	}
}
