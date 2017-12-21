<?php

// add by wenyuhai
include_once("include/util/DbConditionUtil.php");  // 数据库条件解析工具类
// add End

class PacaTable 
{
	static function tabname()
	{
		return "[*]" . substr(static::$class, 1);
	}

    public static $err = 0;
    public static $errmsg = '';
	static $fieldsdef = array();
	/*
	   "adminid" =>"INT",
	   "content" =>"TXT",
	   "name" =>"CHR6*"
	 */
	var $fields = null;
    
	static function sqlstr($_str)
	{
		$_str = str_replace("'","''",$_str);
		$_str = str_replace("\\","\\\\",$_str);
		return $_str;
	}

	function __construct()
	{   
		$this->fields = array();
		$this->fields["ID"] = 0;
		foreach(array_keys($this::$fieldsdef) as $key)
		{
			switch(substr(static::GetFieldType(static::$fieldsdef[$key]),0,3))
			{
				case "INT":
					$this->fields[$key]=0;
					break;
				case "CHR":
				case "TXT":
				case "LXT":
					$this->fields[$key]="";
					break;
			}
		}
	}

    /**
    * 设置错误编码和信息
    * ---
    * @param int $err 错误编码
    * @param string $errmsg 错误信息
    * @return void
    */
    public static function setErr($err, $errmsg)
    {   
        self::$err = $err;
        self::$errmsg = $errmsg;
    }

	function FromArr($arr)
	{
		foreach(array_keys($this->fields) as $key)
		{
			$this->fields[$key] = $arr[$key];
		}
		return 0;
	}
	static function GetFieldTypeIndex($key)
	{
		$value = static::GetFieldType($key);
		if(substr($value, strlen($value) -1, strlen($value)) == "*")
		{
				return true;
		}
		return false;
	}
	static function GetFieldTypeStr($key)
	{
			$type = static::GetFieldType($key);
			$count =0;

			if(substr($type, 0,3) == "CHR")
			{
				if(substr($type, strlen($type) -1, strlen($type)) == "*")
				{
					$count = substr($type, 3, strlen($type) -4);
				} else {
					$count = substr($type, 3, strlen($type)-3 );
				}
			}

			switch(substr($type,0,3))
			{
					case "INT":
							return "Int";
							break;
					case "CHR":
							return "Char x " . $count;
							break;
					case "TXT":
							return "Text";
							break;
					case "LXT":
							return "LongText";
							break;
			}

	}
	static function GetFieldType($_key)
	{
		$arr = explode("@", $_key);
		if(count($arr) > 1)
			return trim($arr[0]);
		else 
			return trim($_key);
	}
	static function GetFieldComment($_key)
	{
		$arr = explode("@", $_key);
		if(count($arr) > 1)
			return trim($arr[1]);
		else 
			return "";
	}
	static function CreateByID($id)
	{
		$one = new static();
		$sql_str = "SELECT * FROM `" . static::tabname() . "` WHERE `ID`=" . $id . " LIMIT 0, 1";

		$arr = PacaDB::QueryArray($sql_str);
		if(count($arr) != 1)
			return null;

		$one->FromArr($arr[0]);

		return $one;
	}
	
	static function DeleteByID($_id)
	{
		$sql_str = sprintf("DELETE FROM `%s` WHERE `ID` = %d", 
				static::tabname(), 
				$_id);

		PacaDB::Execute($sql_str);

		return 0;
	}

	static function DelFields($key,$value)
	{
		//$sql_str = "DELETE  FROM " . static::tabname() . " WHERE {$key} = '{$value}'";
        if(!strstr($value, ",")){
		    $sql_str = "DELETE  FROM `" . static::tabname() . "` WHERE {$key} = '{$value}'";
        }else{
		    $sql_str = "DELETE  FROM `" . static::tabname() . "` WHERE {$key} IN ({$value})";
        }
		PacaDB::Execute($sql_str);

		return 0;
	}

	function Update()
	{
		return $this->core_Update();
	}
	function core_Update()
	{
		
		if(intval($this->fields["ID"]) == 0)
		{
			
			$sql_str = $this->GetInsertSQL();

			PacaDB::Query($sql_str);
			$this->fields["ID"] =  PacaDB::InsertID();
		} else {
			$sql_str = $this->GetUpdateSQL( ); 	

			PacaDB::Query($sql_str);
		}
		
		return 0;
	}

	static function Install()
	{
		self::core_Install();
	}
	static function core_Install()
	{
		$strHFile = static::GetCreateSQL();
		//var_dump($strHFile);
		//die;
		PacaDB::Execute($strHFile);
	}
	
	static function SetCreateAll()
	{
		$sql_str = "SELECT * FROM `" . static::tabname() . "` ";
		return static::SetCreateBySQL($sql_str);
	}

	/**
	 * 字段按IN查找
	 * add By wenyuhai
	 * ---
	 * @param string $field 字段名称
	 * @param string|array $value 字段值
	 */
	public static function SetCreateByInWhere($field, $value)
	{
		if (is_array($value)) {
			$value = implode(',', $value);
		}
		$sql_str = "SELECT * FROM `" . static::tabname() . "` WHERE `{$field}` IN ({$value})";
		return static::SetCreateBySQL($sql_str);
	}

	/**
	 * 根据条件查找当前表内容
	 * add by wenyuhai
	 * ---
	 * @param string|array $field 字段
	 * @param string|array $conditions 条件
	 * @param string $order 排序
	 * @param string $group 分组
	 * @param int $offset 偏移量
	 * @param int $limit 显示条数
	 * @return object
	 */
	 public static function SetCreateByConditions($fields = '*', $conditions = '', $order = '', $group = '', $offset = 0, $limit = 0)
	 {
		$dbConditionUtil = new DbConditionUtil();
		// 解析条件
		$dbConditionUtil->whereArray($conditions);
		$conditions = $dbConditionUtil->getConditions();

		// 组合查询sql
		$sql_str = "SELECT {$fields} FROM `" . static::tabname() . "`";
		if ($conditions) {
			$sql_str .= " where {$conditions}";
		}

		// 排序
		if ($order) {
			$sql_str .= " order by {$order}";
		}

		// 分组
		if ($group) {
			$sql_str .= " group by {$group}";
		}

		// 分页
		if ($offset && $limit) {
			$sql_str .= " limit {$offset}, {$limit}";
		} else if ($limit) {
			$sql_str .= " limit {$limit}";
		}

		// 判断是否有字段信息
		if ($fields != '*') {
			return PacaDB::QueryArray($sql_str);
		}

		return static::SetCreateBySQL($sql_str);
	 }

     /**
	 * 根据条件删除当前表内容
	 * add by wenyuhai
	 * ---
	 * @param string|array $conditions 条件
	 * @return object
	 */
	 public static function DeleteByConditions( $conditions = '')
	 {
		$dbConditionUtil = new DbConditionUtil();
		// 解析条件
		$dbConditionUtil->whereArray($conditions);
		$conditions = $dbConditionUtil->getConditions();

		// 组合查询sql
		$sql_str ="DELETE  FROM `" . static::tabname() . "`";
		if ($conditions) {
			$sql_str .= " where {$conditions}";
		}

		return PacaDB::Query($sql_str);
	 }

     /**
      * 根据一个或多个id删除信息
      * shihaijun
      * -----
      * @param array 条件
      * @return 
      */
     static function DeleteIds($value)
     {
        if(!strstr($value, ",")){
		    $sql_str = "DELETE  FROM `" . static::tabname() . "` WHERE `ID` = {$value}";
        }else{
		    $sql_str = "DELETE  FROM `" . static::tabname() . "` WHERE `ID` IN ({$value})";
        }
		return PacaDB::Query($sql_str);
	 }
	 /**
	  * 根据条件查找分页信息
	  * ---
	  * @param array|string $conditions 条件
	  * @return object
	  */
	static function SetCreatePageByConditions($_pagenum, $_pagesize, $order = '', $conditions = '')
	{
		$offset = $_pagenum * $_pagesize;
		$limit = $_pagesize;
		$result = self::SetCreateByConditions('*', $conditions, $order, '', $offset, $limit);

		return $result;
	}

	static function SetCreatePage($_pagenum, $_pagesize)
	{

		$set = array();

		$sql_str = "SELECT * FROM `" . static::tabname() . "` ORDER BY `ID` DESC LIMIT " . ($_pagenum * $_pagesize) . " , " . $_pagesize;
		
		$result = PacaDB::QueryArray($sql_str);

		for ($i=0; $i < count($result); $i++)
		{
			$set[$i] = new static();
			$set[$i]->FromArr($result[$i]);

		}

		return $set;
	}
	static function SetCreatePageKeyLike($_pagenum, $_pagesize, $keyval)
	{
		$field = explode(",",$keyval);
		$set = array();

		$sql_str = "SELECT * FROM `".static::tabname()."` WHERE  `".$field[0]."` LIKE ".$field[1]." ORDER BY `ID` LIMIT " . ($_pagenum * $_pagesize) . " , " . $_pagesize;
		//echo $sql_str;
		$result = PacaDB::QueryArray($sql_str);

		for ($i=0; $i < count($result); $i++)
		{
			$set[$i] = new static();
			$set[$i]->FromArr($result[$i]);

		}

		return $set;
	}
	static function SetCreatePageKeyVal($_pagenum, $_pagesize,$keyval)
	{
		$field = explode(",",$keyval);
		$set = array();

		$sql_str = "SELECT * FROM `".static::tabname()."` WHERE  `".$field[0]."` = ".$field[1]." ORDER BY `ID` DESC LIMIT " . ($_pagenum * $_pagesize) . " , " . $_pagesize;

		$result = PacaDB::QueryArray($sql_str);

		for ($i=0; $i < count($result); $i++)
		{
			$set[$i] = new static();
			$set[$i]->FromArr($result[$i]);

		}

		return $set;
	}
	static function GetTotalCountKeyLike($keyval)
	{
		
		$field = explode(",",$keyval);
		$set = array();
		
		$sql_str = "SELECT COUNT(`ID`) AS `CC` FROM `" . static::tabname() ."` WHERE `".$field[0]."` LIKE " . $field[1] . "";
		//var_dump($sql_str);
		$result = PacaDB::QueryArray($sql_str);
		if ( count($result) == 1)
		{
			return $result[0]["CC"];
		}
		return 0;
	}
	static function GetTotalCountKeyVal($keyval)
	{
		
		$field = explode(",",$keyval);
		$set = array();
		
		$sql_str = "SELECT COUNT(`ID`) AS `CC` FROM `" . static::tabname() ."` WHERE `".$field[0]."`=".$field[1];
		//var_dump($sql_str);
		$result = PacaDB::QueryArray($sql_str);
		if ( count($result) == 1)
		{
			return $result[0]["CC"];
		}
		return 0;
	}
	static function GetTotalCount($conditions = array())
	{
		$dbConditionUtil = new DbConditionUtil();
		// 解析条件
		$dbConditionUtil->whereArray($conditions);
		$conditions = $dbConditionUtil->getConditions();

		$sql_str = "SELECT COUNT(`ID`) AS `CC` FROM `" . static::tabname() ."` ";
		if ($conditions) {
			$sql_str .= " where {$conditions}";
		}
		//var_dump($sql_str);
		$result = PacaDB::QueryArray($sql_str);
		if ( count($result) == 1)
		{
			return $result[0]["CC"];
		}
		return 0;

	}

	static function GetMaxID()
	{
		$sql_str = "SELECT MAX(`ID`) AS `CC` FROM `" . static::tabname() . "` ";
		$result = PacaDB::QueryArray($sql_str);
		if ( count($result) == 1)
		{
			return $result[0]["CC"];
		}
		return 0;

	}
	static function isExist()
	{
		$sql_str = sprintf("SHOW TABLES LIKE '%s'", static::tabname() );
		
		$result = PacaDB::QueryArray($sql_str);

		if ( count($result) > 0)
		{
			return true;
		}
		return false;
	}
	static function Drop()
	{
		$sql_str = sprintf('DROP TABLE IF EXISTS `%s`', static::tabname() );
		$result = PacaDB::Execute($sql_str);
		return 0;
	}
	static function Clean()
	{
		$sql_str = sprintf("DELETE FROM `%s` ", 
				static::tabname());

		PacaDB::Execute($sql_str);

		return 0;
	}
	static function GetPageCount($_pagenum, $conditions = array())
	{
		$count = static::GetTotalCount($conditions);
        $_pagenum = $_pagenum ? $_pagenum : 1;
		$pagecount = floor(($count -1)/$_pagenum) + 1;
		return $pagecount;
	}
	static function GetPageCountKeyVal($_pagenum,$Keyval)
	{
	
		$count = static::GetTotalCountKeyVal($Keyval);
		$pagecount = floor(($count -1)/$_pagenum) + 1;
		return $pagecount;
	}

	static function GetPageCountKeyLike($_pagenum,$Keyval)
	{
	
		$count = static::GetTotalCountKeyLike($Keyval);
		$pagecount = floor(($count -1)/$_pagenum) + 1;
		return $pagecount;
	}
	function Delete()
	{
		static::DeleteByID($this->fields["ID"]);
	}
	
	function GetInsertSQL()
	{
		$sqlstr = " INSERT INTO `" . static::tabname() . '` (';
		$i=0;
		foreach ( array_keys($this->fields) as $key)
		{
			if($key != "ID")
			{
				if($i != 0) $sqlstr .= " , " ; $i ++;
				$sqlstr .= (" `" . $key . "` ");
			}
		}
		$sqlstr .= " ) VALUES (" ;
		$i=0;
		foreach ( array_keys($this->fields) as $key)
		{
			if($key != "ID")
			{
				if($i != 0) $sqlstr .= " , " ; $i ++;
				$sqlstr .= (" '" . PacaTable::sqlstr($this->fields[$key])  . "' ");
			}
		}
		$sqlstr .= " );" ;
		return $sqlstr; 
	}
	static function SetFieldsByID($_id, $_fields)
	{
		$sql_str = static::GetUpdateFieldSQL($_id, $_fields ); 
		PacaDB::Query($sql_str);
		return 0;

	}
	static function GetUpdateFieldSQL($_id, $_fields)
	{
		$sqlstr = " UPDATE `" . static::tabname() . "` SET ";
		$i = 0; 
		foreach (array_keys ($_fields) as $key)
		{
			if($key != "ID")
			{
				if($i != 0) $sqlstr .= ","; $i ++;
				$sqlstr .= ("`" . $key ."` = '" . $_fields[$key] . "'");
			}
		}
		$sqlstr .=  "WHERE `ID`=" . $_id;
		return $sqlstr; 
	}

	function GetUpdateSQL()
	{
		$sqlstr = " UPDATE `" . static::tabname() . "` SET ";
		$i = 0; 
		foreach (array_keys ($this->fields) as $key)
		{
			if($key != "ID")
			{
				if($i != 0) $sqlstr .= ","; $i ++;
				$sqlstr .= ("`" . $key ."` = '" . PacaTable::sqlstr($this->fields[$key]) . "'");
			}
		}
		$sqlstr .= ( "WHERE `ID`=" . $this->fields["ID"]);
		return $sqlstr; 
	}

	static function GetCreateSQL()
	{
		$sqlstr = "CREATE TABLE IF NOT EXISTS `". static::tabname() . "` (";
		
		$arr = array();
		$arr[] = "`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		
		foreach(array_keys(static::$fieldsdef) as $key)
		{
			$count =0;
			$value = static::GetFieldType(static::$fieldsdef[$key]);
			if(substr($value, 0,3) == "CHR")
			{
				if(substr($value, strlen($value) -1, strlen($value)) == "*")
				{
					$count = substr($value, 3, strlen($value) -4);
				} else {
					$count = substr($value, 3, strlen($value)-3 );
				}
			}
			switch(substr($value, 0,3)) 
			{
				case "CHR":
					$arr[] = ("`" . $key . "` VARCHAR( ". $count . " ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  ");
					break;
				case "TXT":
					$arr[] = " `" . $key . "` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
					break;
				case "LXT":
					$arr[] = " `" . $key . "` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ";
					break;
				case "INT":
					$arr[] =  " `". $key . "` INT NOT NULL ";
					break;
			}
		}
		
		$str = " INDEX ( ";
		$c = 0;
		foreach(array_keys(static::$fieldsdef) as $key)
		{
			$value = static::GetFieldType(static::$fieldsdef[$key]);
			if(substr($value, strlen($value) -1, strlen($value)) == "*")
			{
				$str .= ($c==0)?"":"," ; $c++;
				$str .= ("`" . $key . "` ");
			}
		}
		$str .= " ) ";
		if($c != 0) $arr[] = $str;
		$sqlstr .= implode($arr, ", ");
		$sqlstr .= " ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
		return $sqlstr;
	}
	static function Set2Array($_set)
	{
		$arr = array();
		for($i = 0; $i < count($_set); $i++)
		{
			$arr[$i] = $_set[$i]->fields;
		}
		return $arr;
	}
	static function SetCreateBySQL($_sql)
 	{
 		$set = array();
 
 		$result = PacaDB::QueryArray($_sql);
 
 		for ($i=0; $i < count($result); $i++)
 		{
 			$set[$i] = new static();
 			$set[$i]->FromArr($result[$i]);
 		}
 
 		return $set;		
 	}
 	/*1.3下载csv函数*/
 	function GetCsv($_sql)
	{
		$arr=array();
		//首先取出所有字段的字段名字
		$arr=array_keys($this->fields);
        	//以逗号分隔形成字符串
        	$str=implode(',', $arr)."\n";
		//根据sql语句取出所有的数据
		$set=array();
		$set = static::SetCreateBySQL($_sql);
		for ($z= 0; $z < count($set); $z++)
        	{      
                  for ($j=0; $j <count($arr); $j++) 
             	  {    //z条数据中的$arr[$j]属性 然后加上, 
                   $str1=str_replace("\"","\"\"",$set[$z]->fields[$arr[$j]]); 
             	   //z条数据中的$arr[$j]属性 然后加上, 
             	    $str.="\"".$str1."\"";
	                $str.=',';
                  }
             //$str=substr($str,0,strlen($str)-1);
                  $str.="\n";
               }
		//返回txt文件？
		return $str; 
	}
	/*1.3上传csv函数*/
	 function input_csv($filename)
	{ 
    	$out = array (); 
    	$n = 0; 
    	$handle = fopen($filename, 'r'); 
    	$arr=array_keys($this->fields);
    	while ($data = fgetcsv($handle, 1000000))
    	{    
        	for ($i = 0; $i <count($arr); $i++)
        	{    
            	if (!empty($data[$i])) {
                 	$out[$n][$i] = $data[$i]; 	
            	}else{

            		$out[$n][$i] = ''; 	
            	}
        	}	 
        	$n++; 
    	} 
    	$outt=array();
    	for ($j=0; $j <count($out); $j++) { 
            for ($k=0; $k <count($arr) ; $k++) { 
            	$outt[$j][$arr[$k]]=$out[$j][$k];
            }
    	}
		return $outt;
	} 
	/*1.3上传csv功能*/
	function SetCsv($_csv)
	{
		$filename=$_csv;
		$arr=array_keys($this->fields);
		if (!empty($_csv)) 
		{
		  $result=$this->input_csv($filename);

		  for($i=1;$i<count($result);$i++)
		  {	 
			$one = static::CreateByID($result[$i]["ID"]);
			if (empty($one)) 
			{   
				$result[$i]["ID"]="0";
				$one=new static();
			    for ($k=0; $k <count($arr); $k++) 
				{ 
				  $one->fields[$arr[$k]] = iconv("GBK", "UTF-8", $result[$i][$arr[$k]]);
				}
				$one->Update();
			}else{
					for ($k=0; $k <count($arr); $k++) 
					{ 
						$one->fields[$arr[$k]] =iconv("GBK", "UTF-8", $result[$i][$arr[$k]]);
					}
					$one->Update();
			}
			
		  }
		}
	}	
}
//$csvtxt = Drole::GetCSV("select * from drole order by ID");
//Drole::SetCsv($csvtxt);
?>
