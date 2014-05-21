<?
$db_name='omni_crm';
$db_user='omni_crm';
$db_pass='AaVDNuu3Br';


$mysql_connect=mysql_connect('localhost',$db_user,$db_pass);
mysql_select_db($db_name);
mysql_query('SET NAMES UTF8');
echo mysql_error();


$ticket_email='';
$from_title='LeadCraft';
$from_email='autoinformator@leadcraft.ru';

$time_to_server_shift=60*60*2*(-1); //сдвиг часового пояса до сервера(-2 часа)
$time_to_mainsms_shift=60*60*1*(1); //сдвиг от времени СЕРВЕРА до майнсмса (+1)

?>
