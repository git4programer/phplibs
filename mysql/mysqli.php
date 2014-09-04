<?php

class mysqli{
	public $link;
	private $charset;
	static public $instance=null;
	private $host;
	private $user;
	private $passwd;
	private $db;
	private $limit = '';
	private $sql = '';
	private $order = ' ';
	private $result;
	private $where =' where 1 ';
    private $port;

	private function __construct(){
		require_once(__DIR__.DIRECTORY_SEPARATOR."db.config.php");
		$this->host = $db['host'];
		$this->user = $db['user'];
		$this->passwd = $db['passwd'];
		$this->db = $db['database'];
		$this->charset = isset($db['charset']) ? $db['charset']:'utf8';
        $this->port = isset($db['port']) ? $db['port']:'3306';
		$this->connentMysql();
		$this->setCharSet();
	}
    public function __destruct(){
        mysqli_close($this->link);
    }
	/**
	 * prevernt clone destroy this Singleton
	 */
	private function __clone(){
	}
	public static function Instance(){
		if(is_null(self::$instance)){
			self::$instance = new mysql;
		}
		return self::$instance;
	}

	private function connentMysql(){
		$this->link = mysqli_connect($this->host,$this->user,$this->passwd,$this->db,$this->port) or die('mysql connent fail');
	}
	private function selectDb(){
		$sql = "use ".$this->db;
		$this->result = mysqli_query($this->link,$sql);
	}
	private function setCharSet(){
        $this->changCharSet($this->charset);
	}

	public function changCharSet($char){
		$sql ="set names ".$char;
		$this->query($sql);
	}

	public function changDb($db){
		$this->db = $db ;
		$this->selectDb();
	}

	public function getInsertId(){
		return mysqli_insert_id($this->link);
	}

	public function query($sql){
        $this->sql = $sql;
		$this->result = mysqli_query($this->link,$this->sql);
        $this->limit = '';
        $this->order = ' ';
        $this->where = ' where 1 ';
	}

	public function insertDate($tablename,$data){
		if (empty($data)){
			return false;
		}
		$this->sql = '';
		$this->sql .= "INSERT INTO `".$tablename."` (`";
		$this->sql .= implode("`,`", array_keys($data));
		$this->sql .="`) VALUES('";
		$this->sql .= implode("','",$data);
		$this->sql .="')";
		$this->query($this->sql);
		return $this->getInsertId();
	}

    public function insertDataArr($tablename,$data){
        if (!is_array($data) || empty($data)){
            return false;
        }
        $tmp_arr = array_pop($data);
        $key = array_keys($tmp_arr);
        array_push($data,$tmp_arr);
        $this->sql = '';
        $this->sql .= "INSERT INTO `".$tablename."` (`";
        $this->sql .= implode("`,`", $key);
        $this->sql .="`) VALUES";
        foreach ($data as $v){
            $this->sql .="('";
            $this->sql .=implode("','",$v);
            $this->sql .="'),";
        }
        $this->sql = substr($this->sql,0,-1);
        $this->query($this->sql);
    }
	public function updateDate($tablename,$data,$where=''){
		 
		$this->sql = "UPDATE `".$tablename."` set ";
		foreach ($data as $k=>$v){
			$this->sql .="`".$k."`='".$v."',";
		}
		$this->sql = substr($this->sql,0,-1);
		$this->where($where);
		$this->sql .=$this->where;
		$this->query($this->sql);
	}

	public function select($tablename,$data='*',$where='',$order='',$limit=''){
		$this->sql  = 'SELECT ';
		if(is_array($data) && !empty($data)){
			$this->sql .= implode(',',$data);
		} else if (empty($data)){
			return false;
		}else{
			$this->sql .= $data;
		}
		$this->sql .= ' from '.$tablename;
	
		$this->where($where);
		$this->order($order);
		$this->limit($limit);
		$this->buildSql();
		$this->query($this->sql);
		return $this->mysqlGetRows();
	}

	public function buildSql(){
		$this->sql = $this->sql.$this->where.$this->order.$this->limit;
	}

	public function where($data){
		if(is_array($data) && !empty($data)){
			foreach ($data as $k=>$v){
				$this->where .= ' AND `'.$k."`='".$v."'";
			}
		} else if(is_string($data) && !empty($data)){
			$this->where .= ' AND '.$data;
		}
		return $this;
	}
	public function order($data){
		if(is_array($data) && !empty($data)){
			$this->order = ' order by ';
			foreach($data as $k=>$v){
				$this->order .= $k .' '.$v.',';
			}
			$this->order = substr($this->order, 0,-1);
		}else if(is_string($data) && !empty($data)){
			$this->order = ' order by '.$data;
		}
		$this->order .=' ';
		return $this;
	}
	public function limit($limit){
		if(!empty($limit)){
			$this->limit = ' LIMIT '.$limit;
		}
		return $this;
	}
	public function mysqlGetRows(){
	    $data = array();
        if (mysqli_num_rows($this->result) == 0){
            return false;
        }
	    while ($re = mysqli_fetch_assoc($this->result)) {
	        $data[]= $re;
	    }
	    return $data;
	}
	public function getMysqlOneRows(){
	    $data = $this->mysqlGetRows();
	    return $data[0];
	}
	public function getSql(){
		return $this->sql;
	}
    public function queryBySql($sql){
        $this->query($sql);
        return $this->mysqlGetRows();
    }
    public function getOneRowsBySql($sql){
        $this->query($sql);
        return $this->getMysqlOneRows();
    }
}

?>
