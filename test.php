<?php
require_once("db.php");
require_once("config.php");
echo '111'; 
echo 222;
echo "hello";exit;
testservice();
// insert_with_transaction();
function select()
{
	$db = new db();
	$db->query("SELECT * FROM test");
	$value = $db->get_rows();
	print_r($value);
}

function insert()
{
	$db = new db();
	$db->query("INSERT INTO test (member_id, gender, create_time) VALUES (:member_id, :gender, :create_time)");
	$db->bind(':member_id', '2');
	$db->bind(':gender', 'f');
	$db->bind(':create_time', date("Y-m-d H:i:s"));
	$db->execute();
	// $value = $db->get_rows();
	print_r($value);
}
function insert_with_transaction()
{
	$db = new db();
	$db->beginTransaction();
	$db->query("INSERT INTO test (member_id, gender, create_time) VALUES (:member_id, :gender, :create_time)");
	$db->bind(':member_id', '2');
	$db->bind(':gender', 'f');
	$db->bind(':create_time', date("Y-m-d H:i:s"));
	 // $db->endTransaction();
	$db->execute();
	$db->cancelTransaction();
	 // $db->execute();
}

function testservice()
{
	$db = new mysql_driver();
	$array = array('ee' => 'ee','dd' =>'dd');
	$aaa = $db->query('select * from test',$array);
}
// $db->get_row();
?>