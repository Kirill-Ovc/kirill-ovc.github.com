<?
	$admin_user='admin';
	$admin_pass='dZ21mRH1f';
	
	session_start();
	$admin_hash='370ed67d999aee8cfc0534b82a5162d3';
	$user_hash='7c77b570544a19b82713d68e7e232a51';
	
	if(isset($_GET['login'])&&isset($_GET['password']) && $_GET['login']==$admin_user && $_GET['password']==$admin_pass )
	{
		$_SESSION['userid']=$admin_hash;
		header("Location: index.php");
	}
	
	if(isset($_GET['login'])&&isset($_GET['password']) && $_GET['login']=='user'&& $_GET['password']=='654321')
	{
		$_SESSION['userid']=$user_hash;
		header("Location: index.php");
	}

	if($_GET['close_session']==1)
	{
		unset($_SESSION['userid']);
		header("Location: index.php");
	}
	
	
	include "php/config.php";
	
	switch($_GET['operation'])
	{
		case 'add_phone':
			$symbols=array('(',')',' ','-','_');
			if($_GET['alltime']=='on' || ($_GET['activity_start']=='' || $_GET['activity_start']==''))
			{
				mysql_query("INSERT INTO phones(phone) VALUES('".str_replace($symbols,'',$_GET['phone'])."')");
			}
			else
			{
				mysql_query("INSERT INTO phones(phone,activity_start,activity_finish) VALUES('".str_replace($symbols,'',$_GET['phone'])."','".$_GET['activity_start']."','".$_GET['activity_finish']."')");
			}		
		//	mysql_query("INSERT INTO phones(phone) VALUES('".$_GET['phone']."')");
			header("Location: index.php?page=settings&setting=sms");
			break;
			
		case 'edit_phone':
			if($_GET['alltime']=='on' || ($_GET['activity_start']=='' || $_GET['activity_start']==''))				
				mysql_query("UPDATE phones SET phone='".$_GET['phone']."' WHERE id='".$_GET['id']."'");
			else
				mysql_query("UPDATE phones SET phone='".$_GET['phone']."', activity_start='".$_GET['activity_start']."', activity_finish='".$_GET['activity_finish']."' WHERE id='".$_GET['id']."'");
				
			header("Location: index.php?page=settings&setting=sms");
			break;
			
		case 'delete_phone':
			mysql_query("DELETE FROM phones WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=settings&setting=sms");
			break;
			
		case 'add_mail':
			mysql_query("INSERT INTO emails(email) VALUES('".$_GET['mail']."')");
			header("Location: index.php?page=settings&setting=email");
			break;
			
		case 'edit_mail':
			mysql_query("UPDATE emails SET email='".$_GET['mail']."' WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=settings&setting=email");
			break;
			
		case 'delete_mail':
			mysql_query("DELETE FROM emails WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=settings&setting=email");
			break;

		case 'add_status':
			$max=mysql_query("SELECT MAX(sort) FROM statuses LIMIT 0,1");
			$max=mysql_fetch_array($max);
			mysql_query("INSERT INTO statuses(title,font_color,back_color,sort) VALUES('".$_GET['title']."','".$_GET['font_color']."','".$_GET['back_color']."','".($max[0]+1)."')");
			header("Location: index.php?page=settings&setting=status");
			break;
			
		case 'delete_status':
			mysql_query("DELETE FROM statuses WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=settings&setting=status");
			break;

		case 'edit_status':
			mysql_query("UPDATE statuses SET title='".$_GET['title']."', back_color='".$_GET['back_color']."', font_color='".$_GET['font_color']."' WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=settings&setting=status");
			break;
			
		case 'delete_request':
			mysql_query("DELETE FROM requests WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=request");
			break;
			
		case 'change_status':
			mysql_query("UPDATE requests SET status='".$_GET['status_id']."' WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=request");
			break;
			
		case 'edit_comment':
			mysql_query("UPDATE requests SET comment='".$_GET['comment']."' WHERE id='".$_GET['id']."'");
			header("Location: index.php?page=request");
			break;
	}
	echo mysql_error();
	if(isset($_POST['operation']))
	{
		if($_POST['operation']=='ticket')
		{
			$uploaddir = 'user_files/';
			$uploadfile = $uploaddir.md5(time()).basename($_FILES['userfile']['name']);

			if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {}
			else {$uploadfile='';	}
			mysql_query("INSERT INTO tickets(text,img) VALUES('".$_POST['text']."','".$uploadfile."')");
			echo mysql_error();

			$to  = "roongshn-ne@bk.ru" ; 
			$headers  = "Content-type: text/html; charset=utf8 \r\n"; 
			$headers .= "From: LeadCraft CRM <autoinformator@leadcraft.ru>\r\n";
			$subject = "Обращение в техподдержку"; 
			$message = " 
			<html> 
				<head> 
					<title>Обращение в техподдержку</title> 
				</head> 
				<body> 
					<p>Компания: LeadCraft</p> 
					<p>Текст: ".$_GET['mail']."</p> 
					<p><a href='learcraft.ru/".$uploadfile."' >Файл</a></p> 
				</body> 
			</html>"; 	
			mail($to, $subject, $message, $headers);
			$event_be=1;			
			header("Location: index.php?page=ticked_apply");
		}
	}
	
?>
<!DOCTYPE html>
<html>
  <head>
    <title>LeadCraft CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/colorPicker.css" rel="stylesheet" media="screen">

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="js/jquery.colorPicker.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
		$(document).ready(function(){

				$("#edit_phone form input[name='activity_start'],#edit_phone form input[name='activity_finish'] ").click(function(){
						$("#edit_phone form input[name='alltime']").prop('checked', false);
					});
				$("#add_phone form input[name='activity_start'],#add_phone form input[name='activity_finish'] ").click(function(){
						$("#add_phone form input[name='alltime']").prop('checked', false);
					});

				
				$('.settings_tables table tr td a.edit_phone').click(function(){
						
						$("#edit_phone form input[name='phone']").val($(this).parent().parent().parent().find('.phone').text());
						if($(this).parent().parent().parent().find('.act_start').text()=='' && $(this).parent().parent().parent().find('.act_finish').text()=='')
						{
							$("#edit_phone form input[name='alltime']").prop('checked', true);

							$("#edit_phone form input[name='activity_start']").val('');
							$("#edit_phone form input[name='activity_finish']").val('');
						}
						else
						{
							$("#edit_phone form input[name='activity_start']").val($(this).parent().parent().parent().find('.act_start').text());
							$("#edit_phone form input[name='activity_finish']").val($(this).parent().parent().parent().find('.act_finish').text());

							$("#edit_phone form input[name='alltime']").prop('checked', false);
						}
						$("#edit_phone form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
						
					});

				$('.settings_tables table tr td a.delete_phone').click(function(){
						$("#delete form input[name='operation']").val('delete_phone');
						$("#delete form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
					});
					
				$('.settings_tables table tr td a.edit_mail').click(function(){
						$("#edit_mail form input[name='mail']").val($(this).parent().parent().parent().find('.mail').text());
						$("#edit_mail form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
					});

				$('.settings_tables table tr td a.delete_mail').click(function(){
						$("#delete form input[name='operation']").val('delete_mail');
						$("#delete form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
					});
					
				$('.settings_tables table tr td button.delete_status').click(function(){
						$("#delete form input[name='operation']").val('delete_status');
						$("#delete form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
					});

				$('.settings_tables table tr td button.edit_status').click(function(){
						$("#edit_status form input[name='title']").val($(this).parent().parent().parent().find('.title').text());
						$("#edit_status form input[name='font_color']").val($(this).parent().parent().parent().find('.title').css('color'));
						$("#edit_status form input[name='back_color']").val($(this).parent().parent().parent().find('.title').css('background-color'));
						$("#edit_status form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
						$(".color1, .color2").change();
					});
					
				
				$('.requests_table table tr td button.delete_request').click(function(){
						$("#delete form input[name='operation']").val('delete_request');
						$("#delete form input[name='id']").val($(this).parent().parent().parent().find('.id').text());
					});
					
				$('.requests_table table tr td.comment').click(function(){
						$("#edit_comment form textarea").empty().html($(this)./*parent().*/parent().find('.comment').text());
						$("#edit_comment input[name='id']").val($(this)/*.parent()*/.parent().find('.id').text());
					});

/*
				$('.requests_table table tr td.status select').click(function(event){
						event.stopPropagation();
					});
		*/		
				$('.requests_table table tr td.status').click(function(event){
						event.stopPropagation();
						$('.requests_table table tr td').children('span').css({'display':'inline'});
						$('.requests_table table tr td').children('select').css({'display':'none'});
		
		
						$(this).children('span').css({'display':'none'});
						$(this).children('select').css({'display':'inline'});
						$(this).children('select').change(function(event){
								console.log($(this).val());
								window.location.replace("?page=request&operation=change_status&id="+$(this).parent().parent().find('.id').text()+"&status_id="+$(this).val());
							});
						$('body').click(function(){
								//alert();
								$('.requests_table table tr td').children('span').css({'display':'inline'});
								$('.requests_table table tr td').children('select').css({'display':'none'});
							});
						
					});
			
				$('.settings_tables table tr td button.arrow_up').click(function(){
					elem=$(this).parent().parent().parent();

					first_elem=$(elem).find('.id').text();
					second_elem=$(elem).prev().find('.id').text();					
				
					$.get("php/sort_statuses.php",{id_1:first_elem, id_2:second_elem});

					$(elem).prev().before(elem);

					$('.settings_tables table tbody tr td button.arrow_up, .settings_tables table tbody tr td button.arrow_down').prop('disabled',false);
					$('.settings_tables table tbody tr:eq(1) td button.arrow_up').prop('disabled',true);
					$('.settings_tables table tbody tr:last td button.arrow_down').prop('disabled',true);
				});
				
				$('.settings_tables table tr td button.arrow_down').click(function(){
					elem=$(this).parent().parent().parent();

					first_elem=$(elem).find('.id').text();
					second_elem=$(elem).next().find('.id').text();

					$.get("php/sort_statuses.php",{id_1:first_elem, id_2:second_elem});

					$(elem).next().after(elem);

					$('.settings_tables table tbody tr td button.arrow_up, .settings_tables table tbody tr td button.arrow_down').prop('disabled',false);
					$('.settings_tables table tbody tr:eq(1) td button.arrow_up').prop('disabled',true);
					$('.settings_tables table tbody tr:last td button.arrow_down').prop('disabled',true);				
				});

				$('#color1, .color1, #color2, .color2').colorPicker({showHexField: false});
			});
    </script>
    
  </head>
  <body>
	
	<?	
		
	if(!isset($_SESSION['userid']))
	{
	?>
	<div class='container'>
		<div id='login_container' class='span4 offset4'>
			<form id='login_form'>
				<h3>Авторизация</h3>
				<label>Логин</label>
				<input name='login' type='text'/>
				<label>Пароль</label>
				<input name='password' type='password'/><br>
				<button class='btn'>Войти</button>
			</form>
		</div>
	</div>
	<?	
	}
	else
	if(isset($_SESSION['userid']) && ( $_SESSION['userid']==$admin_hash  || $_SESSION['userid']==$user_hash))
	{
	?>
	
	<div id='add_phone' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Добавить номер телефона</h3>
		</div>
		<form>
			<div class="modal-body">				
				<label>Номер телефона</label>
				<input type='text' name='phone'/>
				<label>Время активности</label>
				с <input type='time'  name='activity_start'  style='width:70px;'/> до <input  type='time' style='width:70px;' name='activity_finish' /><br><input type='checkbox' name='alltime' checked /> Круглосуточно<br>
				<input type='hidden' name='operation' value='add_phone' />				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button href="#" class="btn btn-primary">Добавить</button>
			</div>
		</form>
	</div>
	
	<div id='edit_phone' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Изменить номер телефона</h3>
		</div>
		<form>
			<div class="modal-body">				
				<label>Номер телефона</label>
				<input type='text' name='phone'/>
				<label>Время активности</label>
				с <input type='time'  name='activity_start'  style='width:70px;'/> до <input  type='time' style='width:70px;' name='activity_finish' /><br><input type='checkbox' name='alltime' checked /> Круглосуточно<br>
				<input type='hidden' name='operation' value='edit_phone' />				
				<input type='hidden' name='id' value='' />				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button href="#" class="btn btn-primary">Сохранить</button>
			</div>
		</form>
	</div>

	
	
	<div id='add_mail' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Добавить email</h3>
		</div>
		<form>
			<div class="modal-body">
				<label>Адрес электронной почты</label>
				<input type='text' name='mail'/>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Добавить</button>
				<input type='hidden' name='operation' value='add_mail' />	
			</div>
		</form>
	</div>
	
	<div id='edit_mail' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Изменить email</h3>
		</div>
		<form>
			<div class="modal-body">
				<label>Адрес электронной почты</label>
				<input type='text' name='mail'/>			
				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Добавить</button>
				<input type='hidden' name='operation' value='edit_mail' />	
				<input type='hidden' name='id' value='' />		
			</div>
		</form>
	</div>
	
	<div id='add_status' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Добавить статус заявки</h3>
		</div>
		<form>
			<div class="modal-body">
				<div class='span6'>
					<label>Название статуса</label>
					<input type='text' name='title'/>
				</div>		
				<div class='span2'>
					<label for="color1">Цвет текста</label>
					<input id="color1" name="font_color" type="text" value="#ffffff" />
				</div>
				<div class='span2'>
					<label for="color2">Цвет фона</label>
					<input id="color2" name="back_color" type="text" value="#000000" />
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Добавить</button>
				<input type='hidden' name='operation' value='add_status' />	
			</div>
		</form>
	</div>
	
	<div id='edit_status' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Редактировать статус заявки</h3>
		</div>
		<form>
			<div class="modal-body">
				<div class='span6'>
					<label>Название статуса</label>
					<input type='text' name='title'/>
				</div>		
				<div class='span2'>
					<label for="color1">Цвет текста</label>
					<input id="color1" class='color1' name="font_color" type="text" value="#ffffff" />
				</div>
				<div class='span2'>
					<label for="color2">Цвет фона</label>
					<input id="color2" class='color2' name="back_color" type="text" value="#000000" />
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Сохранить</button>
				<input type='hidden' name='operation' value='edit_status' />
				<input type='hidden' name='id' value='' />					
			</div>
		</form>
	</div>	

	
	<div id='delete' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Удаление </h3>
		</div>
		<form>
			<div class="modal-body">
				<p>Вы уверены?</p>				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Удалить</button>
				<input type='hidden' name='operation' value='' />	
				<input type='hidden' name='id' value='' />		
			</div>
		</form>
	</div>

	<div id='edit_comment' class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>Комментарий к заявке</h3>
		</div>
		<form>
			<div class="modal-body">				
				<label>Текст комментария</label>
				<textarea name='comment' style='width:97%'></textarea>	
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Отменить</a>
				<button class="btn btn-primary">Сохранить</button>
				<input type='hidden' name='operation' value='edit_comment' />
				<input type='hidden' name='id' value='' />					
			</div>
		</form>
	</div>	
	
	
	  
	<div class='container'>
		<div class="navbar">
		  <div class="navbar-inner">
			<a class="brand" href="?page=request">LeadCraft CRM</a>
			<ul class="nav">
			  <li <?if($_GET['page']=='request' || !isset($_GET['page'])){echo "class='active'";}?> ><a href="?page=request">Мои заявки</a></li>

			<?
			if($_SESSION['userid']==$admin_hash)
			{
			?>
			  <li <?if($_GET['page']=='settings'){echo "class='active'";}?>><a href="?page=settings&setting=sms">Настройки системы</a></li>
			<?
			}
			?>
			  <li <?if($_GET['page']=='ticket'){echo "class='active'";}?>><a href="?page=ticket">Написать в поддержку</a></li>
			</ul>
			<div style='float:right;'>
				<div style='float:left; padding-top:8px; padding-right:10px;'>
				<?
				if($_SESSION['userid']==$admin_hash)
					echo 'Администратор';
				if($_SESSION['userid']==$user_hash)
					echo 'Менеджер';
				?>
				</div>
				<a href='?close_session=1' class='btn'>Выйти</a>
			</div>
		  </div>
		 
		</div>

	<?
		if($_GET['page']=='settings')
		{
			?>
			<div class='span3'>
				<h1>Настройки</h1>
				<ul class="nav nav-pills nav-stacked">
					<li <?if($_GET['setting']=='sms'){echo "class='active'";}?>><a href='?page=settings&setting=sms'>SMS уведомления</a></li>
					<li <?if($_GET['setting']=='email'){echo "class='active'";}?>><a href='?page=settings&setting=email'>E-mail уведомления</a></li>
					<li <?if($_GET['setting']=='status'){echo "class='active'";}?>><a href='?page=settings&setting=status'>Статусы заявок</a></li>
				</ul>
			</div>
			<div class='span8 settings_tables'>

				<?if($_GET['setting']=='sms')
				{?>
				<h1>SMS - уведомления</h1>
				Присылать уведомления на следующие номера:<br><br>
				<a  href="#add_phone" role="button" class="btn" data-toggle="modal"><i class='icon-plus'></i> Добавить</a><br><br>
				<table class="table">
					<tr>
						<th>#</th>
						<th>Телефон</th>
						<th>Время активности</th>
						<th>Управление</th>
					</tr>
					<?
					$res=mysql_query("SELECT * FROM phones");
					while( $row=mysql_fetch_array($res) )
					{
					echo "
					<tr>
						<td class='id'>".$row['id']."</td>
						<td class='phone'>".$row['phone']."</td>";

						if($row['activity_start']=='' || $row['activity_finish']=='')
							echo "<td>Круглосуточно</td>";
						else
							echo  "
						<td>C <span class='act_start'>".$row['activity_start']."</span> до <span class='act_finish'>".$row['activity_finish']."</span></td>";

						echo "
						<td>
							<div class='btn-group'>
								<a href='#edit_phone' role='button' class='btn edit_phone' data-toggle='modal'><i class='icon-edit'></i> Редактировать</a>
								<a href='#delete' role='button' class='btn delete_phone' data-toggle='modal'><i class='icon-trash'></i> Удалить</a>
							</div>
						</td>
					</tr> ";
					} 
					?>
				</table>				
			
				<?}
				if($_GET['setting']=='email')
				{?>
				<h1>Email - уведомления</h1>
				Присылать уведомления на следующие адреса:<br><br>
				<a href="#add_mail" role="button" class="btn" data-toggle="modal"><i class='icon-plus'></i> Добавить</a><br><br>
				<table class="table">
					<tr>
						<th>#</th>
						<th>Адрес</th>
						<th>Управление</th>
					</tr>
					<?
					$res=mysql_query("SELECT * FROM emails");
					while( $row=mysql_fetch_array($res) )
					{
					echo "
					<tr>
						<td class='id'>".$row['id']."</td>
						<td class='mail'>".$row['email']."</td>
						<td>
							<div class='btn-group'>
								<a href='#edit_mail' role='button' class='btn edit_mail' data-toggle='modal'><i class='icon-edit'></i> Редактировать</a>
								<a href='#delete' role='button' class='btn delete_mail' data-toggle='modal'><i class='icon-trash'></i> Удалить</a>
							</div>
						</td>
							
					</tr> ";
					} 
					?>
					
				</table>			
				<?}
				if($_GET['setting']=='status')
				{?>
				<h1>Статусы заявок</h1>
				Здесь вы можете редактировать статусы, которые могут принимать ваши заявки<br><br>
				<a href="#add_status" role="button" class="btn" data-toggle="modal"><i class='icon-plus'></i> Добавить</a>	<br><br>
				<table class="table">
					<tr>
						<th>#</th>
						<th>Статус</th>
						<th>Управление</th>
					</tr>
					<?
					$res=mysql_query("SELECT * FROM statuses ORDER BY sort");
					$num=mysql_num_rows($res);
					$i=0;
					while( $row=mysql_fetch_array($res) )
					{
						$i++;
					 echo "
					<tr>
						<td class='id'>".$row['id']."</td>
						<td class='title' style='color:".$row['font_color']."; background:".$row['back_color'].";'>".$row['title']."</td>
						<td>
							<div class='btn-group'>
								<button class='btn edit_status' href='#edit_status' role='button' data-toggle='modal'><i class='icon-edit'></i> Редактировать</button>
								
								<button class='btn delete_status' ";
								$res2=mysql_query("SELECT COUNT(*) FROM requests WHERE status='".$row['id']."'");
								$row2=mysql_fetch_array($res2);
								if($row2[0]>0)
								{
									echo " disabled=true ";
								}
								echo " href='#delete' role='button' data-toggle='modal'><i class='icon-trash'></i> Удалить</button>							
								<button ";
								if($i==1)
									echo " disabled=true ";
								echo " class='btn arrow_up'><i class='icon-arrow-up'></i></button>
								<button ";
								if($i==$num)
									echo " disabled=true ";
								echo " class='btn arrow_down'><i class='icon-arrow-down'></i></button>";
								"
							</div>
						</td>
					</tr>
			
					";
					} ?>
				</table>
				<?}

			
		}
		if($_GET['page']=='request' || !isset($_GET['page']))
		{
			?>
			<div class='span12 requests_table'>				
				<h1>Заявки</h1>
				Показывать: 
				<?
					$res=mysql_query("SELECT title,id FROM statuses");
					while( $row=mysql_fetch_array($res) )
					{	
						echo " <a href='?page=request&filter=".$row['id']."'>".$row['title']."</a> | ";
					}	
							
				?>
				<a href='?page=request'>Все</a>
				
				<table class="table">
					<tr>
						<th>#</th>
						<th>Дата поступления</th>
						<th>Статус</th>
						<th>Данные лида</th>
						<th>Контакты</th>
						<th>Примечание</th>
						<th></th>
					</tr>
					<?
					if(!isset($_GET['filter']))
						$res=mysql_query("SELECT * FROM requests ORDER BY id DESC");
					else
						$res=mysql_query("SELECT * FROM requests WHERE status='".$_GET['filter']."' ORDER BY id DESC");
						
					while( $row=mysql_fetch_array($res) )
					{	
					$res2=mysql_query("SELECT title,font_color,back_color FROM statuses WHERE id='".$row['status']."'");
					$row2=mysql_fetch_array($res2);				
					 echo "
					<tr style='color:".$row2['font_color']."; background:".$row2['back_color']."'>
						<td class='id'>".$row['id']."</td>
						
						<td class='date'>";
					//	echo $row['date'];
						$date=strtotime($row['date'])+60*60*2;
						echo date("H:i",$date)."<br>";
						echo date("d-m-Y ",$date);

						echo "</td>
						<td style='text-decoration:underline' class='status'><span>";
						
						echo $row2['title']; ?></span>
						<select style='display:none'>
							<?
							$res3=mysql_query("SELECT title,id FROM statuses ORDER BY sort");
							while( $row3=mysql_fetch_array($res3) )
							{	
								echo " <option ";
								if($row3['title']==$row2['title'])
									echo ' selected ';
								echo " value='".$row3['id']."'>".$row3['title']."</option> ";
							}	
										
							?>
						</select>
						<?
						echo						
						"</td>
						
						<td class='name'>".$row['name']."</td>
						<td class='contacts'>".$row['phone']."<br><a href='mailto:".$row['email']."'>".$row['email']."</a></td>
						<td href='#edit_comment' role='button' data-toggle='modal' class='comment'>".$row['comment']."</td>
						<td>
							<div class='btn-group'>
								<button class='btn delete_request' href='#delete' role='button' data-toggle='modal'><i class='icon-trash'></i></button>
							</div>
						</td>
					</tr>
			
					";
					} ?>
				</table>
			</div>
			<?
		}
		if($_GET['page']=='ticket')
		{
			?>
			<form method="POST" enctype="multipart/form-data">
				<div class='span11'>
				<h1>Обращение в техподдержку</h1>
				Опишите вашу проблему как можно подробнее	<br>	<br>		
					<textarea name='text' style='width:98%; height:200px;'></textarea><br>
					Вы можете приложить файл(например скриншот ошибки)<br>
					<input type='file' name='userfile'><br><br>
				</div>
				<div style='text-align:right' class='span11'>
					<button class='btn btn-primary btn-large'>Отправить</button>
					<input type='hidden' name='operation' value='ticket'/>	
				</div>
			</form>
			<?
		}
		
		if($_GET['page']=='ticked_apply')
		{
			?>
			<h1>Ваше обращение принято</h1>
			<h3>Мы с вяжемся с вами в ближайшее время</h3>
			<?
		}
	?>
		</div>
	</div>
   
	<?
	}
	?>
  </body>
</html>
