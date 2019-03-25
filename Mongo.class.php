<?php
class Mongo {
 
 
    //--------------  定义变量  --------------//
    private $_conn = null;
    private $_db = null;
    private $writeConcern = null;
    private $_order = null; //排序
    private $_field = null; //字段
    private $_page = null;  //分页
    private $_limit = 10;   //每页的数据
    private $_config = [
        'host' => 'localhost',
        'port' => '27017',
        'username' => '',
        'pwd' => '',
        'dbname' => 'test'
    ];
    /**
     * 构造函数
     */
    public function __construct($config = array()){
        if(!empty($config)){
            if($config['host']) $this->_config['host'] = $config['host'];
            if($config['port']) $this->_config['port'] = $config['port'];
            if($config['username']) $this->_config['username'] = $config['username'];
            if($config['pwd']) $this->_config['pwd'] = $config['pwd'];
            if($config['dbname']) $this->_config['dbname'] = $config['dbname'];            
        }
        if($this->_config['username']){
            $this->_conn = new MongoDB\Driver\Manager(sprintf("mongodb://%s:%s@%s:%s",$this->_config['username'],$this->_config['pwd'],$this->_config['host'],$this->_config['port']));
        }else{
            $this->_conn = new MongoDB\Driver\Manager(sprintf("mongodb://%s:%s",$this->_config['host'],$this->_config['port']));
        }
        $this->_db = $this->_config['dbname'];
        $this->writeConcern   = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);        
    }



    public function query($collName, $where = [], $options = []){
        //排序
        if($this->_order){
            $options['sort'] = $this->_order;
        }
        //需要获取的字段
        if($this->_field){
            foreach($this->_field as $v){
                $options['projection'][$v] = 1;
            }
        }
        //分页
        if($this->_page){
            $count = $this->getCount($collName, $where);
            $endPage = ceil($count / $this->_limit);
            if($this->_page < 1) $this->_page = 1;
            if($this->_page > $endPage) $this->_page = $endPage;
            $skip = ($this->_page - 1) * $this->_limit;
            $options['skip'] = $skip;
            $options['limit'] = $this->_limit;
        }
        $query = new MongoDB\Driver\Query($where,$options);
        $result = $this->_conn->executeQuery($this->_db.'.'.$collName, $query);
        $result = $result->toArray();   
        $this->cleanOperHistory();
        return $result;
    }

    /**
     * [find description]
     * @Author   Tsubasa
     * @DateTime 2019-03-25T14:20:05+0800
     * @Describe 查寻一个
     * @param    [type]                   $collName [description]
     * @param    array                    $where    [description]
     * @param    array                    $option   [description]
     * @return   [type]                             [description]
     */
    public function find($collName, $where = [], $option = []){
        $result = $this->query($collName, $where, $option);
        return $result[0];
    }


    /**
     * [insert description]
     * @Author   Tsubasa
     * @DateTime 2019-03-25T14:20:30+0800
     * @Describe 插入
     * @return   [type]                   [description]
     */
    public function insert($collName, $data){
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($data);
        $result = $this->_conn->executeBulkWrite($this->_db.'.'.$collName, $bulk, $this->writeConcern);
        return $result->getInsertedCount();
    }

    public function update($collName, $where = [], $update = [], $upsert = false){
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update($where,['$set' => $update], ['multi' => true, 'upsert' => $upsert]);
        $result = $this->_conn->executeBulkWrite($this->_db.'.'.$collName, $bulk, $this->writeConcern);
        return $result->getModifiedCount();        
    }


    public function delete($collName, $where = []){
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete($where);
        $result = $this->_conn->executeBulkWrite($this->_db.'.'.$collName, $bulk, $this->writeConcern);
        return $result->getDeletedCount();        
    }

    /**
     * [order description]
     * @Author   Tsubasa
     * @DateTime 2019-03-25T11:37:55+0800
     * @Describe 排序
     * @param    array                    $order [description]
     * @return   [type]                          [description]
     */
    public function order($order = array()){
        if(!empty($order)){
            $this->_order = $order;
        }
        return $this;
    }

    /**
     * [field description]
     * @Author   Tsubasa
     * @DateTime 2019-03-25T11:57:14+0800
     * @Describe 获取字段
     * @param    array                    $field [description]
     * @return   [type]                          [description]
     */
    public function field($field = array()){
        if(!empty($field)){
            $this->_field = $field;
        }
        return $this;
    }

    /**
     * [page description]
     * @Author   Tsubasa
     * @DateTime 2019-03-25T13:53:48+0800
     * @Describe 分页
     * @param    integer                  $page  [description]
     * @param    integer                  $limit [description]
     * @return   [type]                          [description]
     */
    public function page($page = 1, $limit = 10){
        $this->_page = $page;
        $this->_limit = $limit;
        return $this;
    }


    public function getCount($collName, $where = array()){
            $command = new MongoDB\Driver\Command(['count' => $collName,'query'=>$where]);
            $result = $this->_conn->executeCommand($this->_db,$command);
            $res = $result->toArray();
            $cnt = 0;
            if ($res) {
             $cnt = $res[0]->n;
            }
            return $cnt;
    }

    protected function cleanOperHistory(){
        $this->_order = null;
        $this->_field = null;
        $this->_page = null;
    }
}
