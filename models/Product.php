<?php
	include_once 'Connection.php';
	class Product {
		var $connect;
		function __construct()
		{
			$connection = new Connection();
			$this->connect = $connection->getConnect();
		}
		function findByCode($code) {
			$sql = "select * from dbo.[book] where code = '" . $code . "'";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$result = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
			return $result;
		}
		function findDetailByCode($code) {
			$sql = "select b.*, a.name as aName, t.name as tName, p.name as pName from dbo.[book] b inner join dbo.[author] a on a.id = b.author_id inner join dbo.[type] t on t.id = b.type_id inner join dbo.[publisher] p on p.id = b.publisher_id where b.code = '" . $code . "'";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$result = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
			return $result;
		}
		function findNew() {
			$sql = "SELECT TOP 3 [code], [name], [price], [image] FROM [QuanLyBanSach].[dbo].[book] order by id";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function findRecommend($typeId) {
			$sql = "select * from dbo.[book] where type_id = " . $typeId;
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function search($query, $page, $sort) {
			$sql = "select b.* from dbo.[book] b";
			foreach ($query as $key =>  $value) {
				if($value["name"] != 'price') {
					$sql .= " inner join dbo.[" . $value["name"] . "] a_" . $key . " on a_" . $key . ".id = b." . $value["name"] . "_id" . " and a_" . $key . ".code = '" . $value["code"] . "'";
				} else {
					$sql .= " where b.price >= " . $value["from"] . "000 and b.price <= " . $value["to"] . "000";
				}
			}
			$sql .= " order by b." . $sort . " OFFSET " . ($page - 1)*6 . " ROWS FETCH NEXT 6 ROWS ONLY";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function count($query) {
			$sql = "select count(1) as total from dbo.[book] b";
			foreach ($query as $key =>  $value) {
				if($value["name"] != 'price') {
					$sql .= " inner join dbo.[" . $value["name"] . "] a_" . $key . " on a_" . $key . ".id = b." . $value["name"] . "_id" . " and a_" . $key . ".code = '" . $value["code"] . "'";
				} else {
					$sql .= " where b.price >= " . $value["from"] . "000 and b.price <= " . $value["to"] . "000";
				}
			}
			$stmt = sqlsrv_query( $this->connect, $sql);
			$total = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
			return $total['total'];
		}
		function getAuthor()
		{
			$sql = "select t.id, t.code, t.name, count(t.id) as total from dbo.[author] t inner join dbo.[book] b on b.author_id = t.id GROUP BY t.id, t.code, t.name";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function getType()
		{
			$sql = "select t.id, t.code, t.name, count(t.id) as total from dbo.[type] t inner join dbo.[book] b on b.type_id = t.id GROUP BY t.id, t.code, t.name";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function getPublisher()
		{
			$sql = "select t.id, t.code, t.name, count(t.id) as total from dbo.[publisher] t inner join dbo.[book] b on b.publisher_id = t.id GROUP BY t.id, t.code, t.name";
			$stmt = sqlsrv_query( $this->connect, $sql);
			$data = array();
			while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		}
		function checkQuantity($code, $quantity) {
			$sql = "select count(1) as total from dbo.[book] b where b.code = ? and b.quantity >= ?";
			$stmt = sqlsrv_query( $this->connect, $sql, array($code, $quantity));
			$total = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
			return $total['total'];
		}
		function updateProduct($code, $quantity) {
			$sql = "update dbo.[book] set quantity = ? where code = ?";
			$stmt = sqlsrv_query( $this->connect, $sql, array($quantity, $code));
			return $stmt;
		}
	}
?>