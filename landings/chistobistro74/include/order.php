<?
if (isset($_POST['form']))
{
	require_once 'config.php';

    $mysql_connect=mysql_connect('localhost',$db_user,$db_pass);
    mysql_select_db($db_name);
    mysql_query('SET NAMES UTF8');
    echo mysql_error();

    $form = $_POST['form'];
    $form_input_name = $_POST['name'];
    $form_input_phone = $_POST['phone'];
    $form_input_email = $_POST['email'];
    $form_input_comment = $_POST['comment'];

    $tmp=mysql_query("INSERT INTO requests(name,phone,comment,form,status) VALUES( '".$form_input_name."',
																							'".$form_input_phone."',
																							'".$form_input_comment."',
																							'".$form."',
																							(SELECT id FROM statuses WHERE sort=(SELECT MIN(sort) FROM statuses LIMIT 0,1)))");

    echo mysql_error();

    $res=mysql_query("SELECT email FROM emails");

    while($row=mysql_fetch_array($res))
    {
        $emails[]=$row['email'];
    }
    $emails=implode(', ',$emails);
    print_r($emails);
    echo mysql_error();

    $headers="Content-type: text/html; charset=utf8 \r\n";
    $headers .= "From: Omnilogic <autoinformator@omnilogic.ru>\r\n";
    $message = "";
    $subject = "Заявка с сайта ".$site_name;

    if(isset($form))
    {
        $message = "
                    <html>
                        <head>
                            <title>".$form."</title>
                        </head>
                        <body>
                            <p>Форма: Заявка с сайта ". $site_name."</p>
                            <p>Форма: ".$form."</p>
                            <p>Имя клиента: ".$form_input_name."</p>
                            <p>Телефон: ".$form_input_phone."</p>
                            <p>Удобное время мойки: ".$form_input_comment."</p>
                        </body>
                    </html>";
    }

    if ((!empty($form_input_name))||(!empty($form_input_phone))||(!empty($form_input_email)))
        mail($emails, $subject, $message, $headers);

    $sms_message="Новая заявка.\nИмя клиента: ".$form_input_name."\nТелефон: ".$form_input_phone."\nУдобное время мойки: ".$form_input_comment;

    require_once 'mainsms.class.php';
	$api = new MainSMS ( $mainsms_project_id , $mainsms_api_key , false, false );
    $res=mysql_query("SELECT phone,activity_start,activity_finish FROM phones");
    while($row=mysql_fetch_array($res))
    {
        $start=date('d-m-Y',time())." ".$row['activity_start'];
        $start=date_timestamp_get(date_create_from_format('d-m-Y G:i', $start))-(2*60*60);

        $finish=date('d-m-Y',time())." ".$row['activity_finish'];
        $finish=date_timestamp_get(date_create_from_format('d-m-Y G:i', $finish))-(2*60*60);

        if($finish<=$start)
        {
            $finish+=60*60*24;
        }
        if($start<=time() && $finish>=time())
        {
            $api->sendSMS ( $row['phone'] , $sms_message, $mainsms_sender );
        }
        else
        {
            $send_in=date('d.m.Y H:i',$start+(60*60));
            $api->sendSMS ( $row['phone'] , $sms_message, $sender ,$send_in);
        }
    }

}

?>