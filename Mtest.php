<?php
require_once "Mongo.class.php";
$m = new Mongo();
// 不带条件查寻
// $data = $m->query('emp');

//带条件查寻
// $map['ename'] = 'haha';
// $data = $m->query('emp', $map);

//查寻一个
// $data = $m->find('emp');

//排序
// $order['depno'] = -1;
// $data = $m->order($order)->query('emp');

//取个别字段
// $data = $m->field(['ename','depno'])->order(['depno' => -1])->query('emp');

//分页
$map['depno'] = array('$gt' => 20);
$data = $m->field(['ename','depno'])->order(['depno' => 1])->page(1, 2)->query('emp',$map);
var_dump($data);
// 插入
// $data['deptno'] = 50;
// $data['dname'] = '研发部';
// $data['loc'] = '东莞';
// $res = $m->insert('dept', $data);
// 修改
// $data['deptno'] = '60';
// $data['dname'] = '新莞人';
// $map['deptno'] = '研发部';
// $res = $m->update('dept', $map, $data);
// 删除
// $map['loc'] = '东莞';
// $res = $m->delete('dept',$map);
// var_dump($res);