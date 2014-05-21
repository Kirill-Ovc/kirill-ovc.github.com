<?
	include "config.php";
	
	$sort_1=mysql_query("SELECT sort FROM statuses WHERE id='".$_GET['id_1']."'");
	$sort_1=mysql_fetch_assoc($sort_1);
	
	$sort_2=mysql_query("SELECT sort FROM statuses WHERE id='".$_GET['id_2']."'");
	$sort_2=mysql_fetch_assoc($sort_2);

	echo mysql_error();
	print_r($sort_1);
	
	print_r($sort_2);
	
	mysql_query("UPDATE statuses SET sort='".$sort_2['sort']."' WHERE id='".$_GET['id_1']."'");
	mysql_query("UPDATE statuses SET sort='".$sort_1['sort']."' WHERE id='".$_GET['id_2']."'");
?>
