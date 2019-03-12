<?php
namespace app\api\controller\v1;
use app\api\controller\Base;
use app\common\lib\IAuth;
use app\common\lib\Aes;
class Test extends Base {
    public function index(){
        $data = [
            array(1,2,3),
            array(4,5,6)
        ];
        return show(0, 'OK', $data, 200);
    }

    public function test(){
        $data = [
            'version' => 1,
            'time' => time()
        ];
        echo IAuth::setSign($data);
    }
}
