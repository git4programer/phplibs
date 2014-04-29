<?php



/**
 * mysql的超级操作类
 * 
 **/
class mysql
{
    private $host;
    private $user;
    private $passwd;
    private $db;
    private $char='utf8';
    private $link;

    protected $sql;
    protected $where = ' where 1 ';
    protected $groub;
    protected $order;
    protected $limit;
    
    public static $instans = null;


    private function __construct()
    {
        include (__DIR__.'/'.'db.config.php');
        $this->host = $db['host'];
        $this->user = $db['user'];
        $this->passwd = $db['passwd'];
        $this->db = $db['db'];
        $this->char = isset($db['char']) ? $db['char'] : $this->char;
        $this->connect();
        $this->setChar();
        $this->selectdb();
    }

    private function __clone(){
    }

    public static function instans(){
        if (is_null(self::$instans)){
            self::$instans = new mysql;
        }
        return self::$instans;
    }

    protected function query(){
        $this->resurlt = mysql_query($this->sql,$this->link);
    }

    protected function connect(){
        $this->link = mysql_connect($this->host,$this->user,$this->passwd) or die('connect mysql fail!!');
    }

    public  function setChar($char=''){
        if(empty($char)){
            $this->sql = 'use names '.$this->char;
        } else {
            $this->sql = 'use names '.$char;
        }
        $this->query();
    }

    public  function selectdb($db=''){
        if(empty($db)){
            $this->sql = 'use '.$this->db;
        } else {
            $this->sql = 'use  '.$db;
        }
        $this->query();
    }

    public function insert($table,$data){
        if(empty($table) || empty($data)){
            return false;
        }
        if (is_array($data)) {
            $this->sql = ' INSERT INTO '.$table. ' (`'.implode('`,`',array_keys($data)).'`) VALUES(`'.implode('`,`',$data).'`)';
            $this->query();
            return $this->getIsertId();
        } else {
            return false;
        }
    }

    public function getIsertId(){
        return mysql_insert_id();
    }

    public function update($table,$data,$condition){
        if (empty($table) || empty($data)) {
            return false;
        }
        if (is_array($data)) {
            $this->sql = ' UPDATE '.$table.' set ';
            foreach ($data as $key => $val) {
                $this->sql .= "`".$key."`='".$val."',";
            }
            $this->sql = substr($this->sql,0,-1);
        }

        $this->condition($condition);
        $this->sql .=$this->where;
        $this->query();
    }

    public function condition($data){
        if (is_string($data)) {
            $this->where .= " AND ".$data;
        } else if (is_array($data)){
            foreach ($data as $key => $val){
                $this->where .= " AND `".$key."`='".$val."' ";
            }
        }
        return $this;
    }

    public function select($table,$data='*',$where='',$group='',$order='',$limit=''){
    
    }
}
?>
