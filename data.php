<?php
if(!class_exists("DataBase")){
class DataBase
{
	var $dblocation; 
	var $dbname; 
  	var $dbuser; 
  	var $dbpassword;
	var $dbcnx;
	var $error;
	
	function Connect()
	{
		  $this->dbcnx = mysql_connect($this->dblocation, $this->dbuser, $this->dbpassword); 
		  $this->error = "";
		  if (!$this->dbcnx) 
		  { 
			$this->error = "<p>mySQL is not evaible now!</p>";
			return false; 
		  } 
		  if (!@mysql_select_db($this->dbname,$this->dbcnx) ) 
		  { 
			$this->error = "<p>There is not access to data base.</p>";
			return false; 
		  } 
		  $ver = mysql_query("SELECT VERSION()"); 
		  if(!$ver) 
		  { 
			$this->error = "<p>Query error!</p>"; 
			return false;
		  } 
	}
	function Disconnect()
	{
		mysql_close($this->dbcnx);
	}
}}

if(!class_exists("DataSource")){

class DataSource
{
var $dataBase;

var $sqlQuery;
var $sqlTable;
var $records;
var $orderBy;
var $limit;		
var $condition;

var $queryRes;
var $numRows;
var $numCols;
var $tableLength;
var $error;

function dataSource()
{
	$this->queryRes="";
	$this->sqlTable = "";
	$this->selectedRecords = "*";
	$this->orderBy = "";
	$this->limit = "";
	$this->condition = "";	
	$this->error="";
	$this->condition="";
	$this->tableLength = 0;
}
function setTable($tab)
{
	$this->sqlTable = $tab;
}
function setFields($rec)
{
	$this->records = $rec;
}
function setOrder($ord,$by)
{
	$this->orderBy = "ORDER BY $by $ord";
}
function setLimit($offset,$quantity)
{
	$this->limit = "LIMIT $offset , $quantity";
}
function setWhere($where)
{
	$this->condition = "WHERE ".$where;
}
function select()
{
	if(func_num_args()==1)
	{
		$this->records = func_get_arg(0);
	}
	else $this->records = "*";
	$this->sqlQuery = "SELECT ".$this->records." FROM ".$this->sqlTable." ".$this->condition." ".$this->orderBy." ".$this->limit.";";
	$this->dataBind();
	return $this->queryRes;
}
function dataBind()
{
	if(!isset($this->dataBase))
	{
		$this->error="dataBase behaviour is not declarate!";
		return false;
	}
	$this->dataBase->Connect();
	$this->queryRes = mysql_query($this->sqlQuery);
	if($this->tableLength==0)
	{
		$query = mysql_query("SELECT COUNT(*) FROM ".$this->sqlTable.";");
		$this->tableLength = mysql_result($query,0);
	}
	$this->dataBase->Disconnect();
	$this->numRows = mysql_num_rows($this->queryRes);
	$this->numCols = mysql_num_fields($this->queryRes);
}
function nextRow()
{
	return mysql_fetch_array($this->queryRes);
}
function getRow($index)
{
	if(@mysql_data_seek($this->queryRes, $index)) {return $this->NextRow();}
	else 
	{ 
	$this->error = "Not valid Index";
	return false;
	}
}
function getColl($identificator)
{
	if(!$this->numRows==0)
	{
		@mysql_data_seek($this->queryRes, 0);
		while(@$res = mysql_fetch_array($this->queryRes))
		{
			$arr[] = $res[$identificator];
		}
	return $arr;	
	}
	else 
	{
		return false;
	}
}
function getTableLength(){// return the SQLTable length
if($this->tableLength==0)
	{
		$query = mysql_query("SELECT COUNT(*) FROM ".$this->sqlTable.";");
		$this->tableLength = mysql_result($query,0);
	}
return $this->tableLength;
}
function getLastRecord($fields){// return the SQLTable length
	$this->setFields($fields);
	$tmpLim = $this->limit;
	$tmpOrd = $this->orderBy;
	$this->setLimit(0,1);
	$this->setOrder("DESC","id");
	$this->select($fields);
	$this->limit = $tmpLim;
	$this->orderBy = $tmpOrd;
	$tempRow = $this->nextRow();	
	return  $tempRow;
}
}}
?>