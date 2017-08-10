<?php
abstract class database{

	public function __construct()
	{
		$this->connect();
	}

	abstract public function connect();

	abstract public function query($sql,$values);

	public function quote_identifier($col)
	{
		return $col;
	}
	public function insert($table, array $bind)
	{
		$cols = [];
		$vals = [];
		foreach ($bind as $col => $val) {
			$cols[] = $this->quote_identifier($col);
			$vals[] = '?';
		}
		$sql = "INSERT INTO "
			. $this->quote_identifier($table)
			.'(' . implode(', ', $cols) . ')'
			.'VALUES (' . implode(', ', $vals) . ')';

		return $this->query($sql,$bind); 
	}
}

class mysql_driver extends database{

	private $conn = null;

	public function connect()
	{
		$dsn = "mysql:host=".dbhost.";dbname="."test";
		$option = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		);

		$this->conn = new PDO($dsn, username, password, $option);
	}

	public function quote_identifier($col)
	{
		return '`' . $col . '`';
	} 
	public function query($sql, $values)
	{
		$binds = [];
		$types = str_pad('', count($values), 's');
		$binds[] = &$types;
		foreach ($values as $key => $value) {
			$binds[] = &$values[$key];
		}
		print_r($binds);
		$stmt = $this->conn->prepare($sql);
		call_user_func_array([$stmt, 'bind_param'], $binds);
		print_r($stmt);exit;
		$stmt->execute();
		$stmt->close();
	}
}

class model{

	protected static $db;
	protected $table = 'table';
	protected $data = [];

	public static function set_db(database $db)
	{
		self::$db = $db;
	}
	public function __construct($data)
	{
		$this->data = $data;
	}
	public function save()
	{
		self::$db->insert($this->table,$this->data);
	}
}

class User extends model{

	protected $table = 'test';
}

class db{

	private $db;
	private $stmt;

	public function __construct(){
		$dsn = "mysql:host=".dbhost.";dbname="."test";
		$option = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		);
		try{
			$this->db = new PDO($dsn, username, password, $option);
		}
		catch (PDOException $e)
		{
			print "Error: " . $e->getMessage() . "</br>";
			die();
		}
	}

	// 預備query
	public function query($query){
	    $this->stmt = $this->db->prepare($query);
	}

	// 取代字串
	public function bind($param, $value, $type = null){
	    if (is_null($type)) {
	        switch (true) {
	            case is_int($value):
	                $type = PDO::PARAM_INT;
	                break;
	            case is_bool($value):
	                $type = PDO::PARAM_BOOL;
	                break;
	            case is_null($value):
	                $type = PDO::PARAM_NULL;
	                break;
	            default:
	                $type = PDO::PARAM_STR;
	        }
	    }
	    $this->stmt->bindValue($param, $value, $type);
	}

	// 判斷成功失敗
	public function execute(){
	    return $this->stmt->execute();
	}

	// 回傳陣列
	public function get_rows()
	{
		$this->stmt->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	// 回傳單一列
	public function get_row()
	{
		$this->stmt->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	// 抓受影響的數目
	public function rowCount(){
		return $this->stmt->rowCount();
	}

	// 抓最後一個變動
	public function lastInsertId(){
	    return $this->db->lastInsertId();
	}
	// 交易機制開始
	public function beginTransaction(){
    	return $this->db->beginTransaction();
	}
	// 交易機制送出
	public function endTransaction(){
	    return $this->db->commit();
	}
	// 交易機制回朔
	public function cancelTransaction(){
    	return $this->db->rollBack();
	}
}

?>