<?php
$con = mysql_connect("localhost","root","123456");
header("content-type:text/html;charset=utf-8");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("world", $con);

$result = mysql_query("SELECT * FROM sm_dictionary WHERE pid='0'");  //查询pid为0 的头项
$res=array();
while($row = mysql_fetch_array($result)){      //遍历第一次查询结果
	$items=new stdClass();   //存放一条记录和对应的子记录对象
	$items->title=$row['dict_name'];   //name属性
	$items->id=$row["id"];//id属性
	$result_sub=mysql_query("SELECT * FROM sm_dictionary WHERE pid='".$row['id']."'");  //根据查询到的id  作为pid去查询对应的子项
	while($row2 = mysql_fetch_array($result_sub)){    
		$sub=new stdClass();                    //新建子项对象
		$sub->id=$row2["id"];					//放入属性
		$sub->dict_name=$row2["dict_name"];
		
		$sub->dict_value=$row2["dict_value"];
		
		$sub->pid=$row2["pid"];
		
		array_push($items->subitem,$sub);       //数组添加
	}
	array_push($res,$items);
  }
print_r(json_encode($res));
mysql_close($con);
?>