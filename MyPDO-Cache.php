<?php
class MyPDO{
    private $type;
    private $host;
    private $port;
    private $dbname;
    private $charset;
    private $username;
    private $pwd;
    private $pdo;
    private static $instance;   //保存单例
    private function __construct($param) {
        $this->initParam($param);
        $this->initPDO();
        $this->initException();
    }
    private function __clone() {
    }
    public static function getInstance($param=array()){
        if(!self::$instance instanceof self)
            self::$instance=new self($param);
        return self::$instance;
    }
    //初始化参数
    private function initParam($param){
        $this->type=$param['type']??'mysql';
        $this->host=$param['host']??'localhost';
        $this->port=$param['port']??'3306';
        $this->dbname=$param['dbname']??'data';
        $this->charset=$param['charset']??'utf8';
        $this->username=$param['username']??'root';
        $this->pwd=$param['pwd']??'root';
    }
    /*
     * 显示异常信息的方法
     * @param $ex Exception 对象
     * @param $sql string SQL语句
     */
    private function showException($ex,$sql=''){
        if($sql!=''){
            echo 'SQL语句执行失败<br>';
            echo '错误的SQL是：',$sql,'<br>';
        }
        echo '错误信息：',$ex->getMessage(),'<br>';
        echo '错误码：',$ex->getCode(),'<br>';
        echo '错误文件：',$ex->getFile(),'<br>';
        echo '错误行号：',$ex->getLine(),'<br>';
        exit;
    }
    //创建pdo对象（连接数据库）
    private function initPDO(){
        try{
            $dsn="{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo=new PDO($dsn,$this->username,$this->pwd);
        } catch (PDOException $ex) {
            $this->showException($ex);
        }
    }
    //设置异常模式
    private function initException(){
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    /*
     * 执行数据操作语句
     * @param $sql string SQL语句
     * @return 成功返回收影响的记录数，失败返回false
     */
    public function exec($sql){
        try{
            return $this->pdo->exec($sql);
        } catch (Exception $ex) {
            $this->showException($ex, $sql);
        }
    }
    //获取自动增长的编号
    public function getLastInsertId(){
        return $this->pdo->lastInsertId();
    }
    //获取匹配格式
    private function fetchType($type){
         switch($type){
            case 'num':
                return PDO::FETCH_NUM;
            case 'both':
                return PDO::FETCH_BOTH;
            default:
                return PDO::FETCH_ASSOC;
        }
    }
    //获取所有数据，返回二维数组
    public function fetchAll($sql,$type='assoc'){
       $type=$this->fetchType($type);
        $stmt=$this->pdo->query($sql);
        return $stmt->fetchAll($type);
    }
    //获取一条数据,返回一维数组
    public function fetchRow($sql,$type='assoc'){
        $type=$this->fetchType($type);
        $stmt= $this->pdo->query($sql);
        return $stmt->fetch($type);
    }
    //获取一行一列的数据
    public function fetchColumn($sql){
        $stmt=$this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
}
//测试
$param=array(
    'dbname'    =>  'php72',
);
$mypdo= MyPDO::getInstance($param);
/*
//$mypdo->exec('delete from news where id=10');
if($mypdo->exec("insert into news values (null,'aa','aa',1231212)"))
    echo $mypdo->getLastInsertId ();
 */
//$rs=$mypdo->fetchAll('select * from news','assoc');
//$rs=$mypdo->fetchRow('select * from news');
$rs=$mypdo->fetchColumn('select count(*) from news');
var_dump($rs);