<?php
$err='';
$filedb = '../hw.dat';
$filest = '../off';
$status = 1; $disable=' disabled';
if (file_exists($filest)) {$status = 0; $disable='';}

// функция получения уровней из файла
function getlvl($ct) {
	
	if(!$ct || $ct <=0) $ct=20;
	$found=0;
	$new="";
	$file='../hw.dat';
	$a=file($file);
	for($i=0;$i<count($a);$i++) {

		$x=explode("|",$a[$i]);
		if(!substr_count($a[$i],"✔") ) {
			$new.=$a[$i]; $found++;
			$a[$i]=str_replace(array("\r","\n"),"",$a[$i]); 
			$a[$i].=" ✔\r\n";
		}
		if($found==$ct) break;
	}
	file_put_contents($file,implode("",$a));
	file_put_contents('txt/'.date("d.m.Y").'_'.$ct.'L.txt',$new);
	
}

// если форма отправлена
if ( isset($_POST['submit']) && $_POST['file']!=="" )  {
	
	if ( file_put_contents($filedb, stripslashes($_POST['file'])) )
	{
		$err='<div class="alert alert-success"><strong>ОК.</strong> Файл базы успешно сохранён.</div>';
	} else 
	{
		$err='<div class="alert alert-danger"><strong>Ошибка!</strong> Не удалось сохранить файл базын. Срочно свяжитесь с владельцем сайта!</div>';
	}
}

// если запрос на вкл откл сайта
if ( isset($_GET['site']) && $_GET['site'] == 1 )  {
	
	@file_put_contents($filest,'1');
	$status = 0; $disable='';
	$err='<div class="alert alert-warning"><strong>ВЫКЛЮЧЕН!</strong> Сайт отключен на техническое обслуживание. Не забываем включить после завершения работ!</div>';
} elseif ( isset($_GET['site']) && $_GET['site'] == 0 ) {
	 
	if (file_exists($filest)) @unlink($filest);
	$status = 1; $disable=' disabled';
	$err='<div class="alert alert-success"><strong>ВКЛЮЧЕН!</strong> Сайт вновь активирован. Не забываем проверить, что он работает!</div>';
	}

// если запрос на получение уровней
if ( isset($_GET['gets']) && $_GET['gets'] == 2 )  { // 20 уровней
	
	getlvl('20');
	$err='<div class="alert alert-success"><strong>Готово!</strong> 20 уровней было выбрано, отмечено и сохранено - <a href="gets.php?lvl=20" target="_blank">скачать файл</a>.</div>';
	} elseif ( isset($_GET['gets']) && $_GET['gets'] == 1 ) { // 100 уровней
	
	getlvl('100');
	$err='<div class="alert alert-success"><strong>Готово!</strong> 100 уровней было выбрано, отмечено и сохранено - <a href="gets.php?lvl=100" target="_blank">скачать файл</a>.</div>';
	} 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Управление</title>


    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

  </head>
  <body>

    <div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="btn-group btn-group-lg">
			
				<a href="./" class="btn btn-primary" type="button">
					Главная
				</a> 				 
				<button class="btn btn-default disabled" type="button">
					Получить новые уровни:
				</button>
				<a href="?gets=2" class="btn btn-default">
					20 шт
				</a>
				<a href="?gets=1" class="btn btn-default">
					100 шт
				</a>
				<a href="#" data-toggle="modal" onclick="refreshIframe();" data-target="#myModal" class="btn btn-success">Обзор созданных файлов</a>

				
			</div> 

			<h4>Статус сайта &mdash; <?php if($status == 1) {
			echo '<span class="text-success">активен</span> <a href="?site=1" class="btn btn-danger btn-xs">Отключить</a>';
			} else  { 
			echo '<span class="text-danger">выключен</span> <a href="?site=0" class="btn btn-success btn-xs">Включить</a>'; } ?>
			</h4>
			<blockquote>
<strong><u>Как взять новые уровни для выпуска?</u></strong> <br>
1. Нажать на кнопку выше [20 шт] или [100 шт]<br>
2. После чего немного ниже, в сообщении на зелёном фоне нажать - скачать файл<br>
<br>
			<h5><strong>Помощь по служебным спец.символам:</strong><br>
			<strong> ↔ </strong> (разделяет данные - уровень, автор, дата)<br>
			<strong> ✔ </strong> (помечает уровень, как пройденный)
			</h5>
			</blockquote>
			
			<div class="well">
			<strong>Помни!</strong> Прежде чем редактировать ОБЩИЙ файл базы уровней, нужно <a href="?site=1" class="alert-link">отключить сайт</a>.<br>Так же <u>не забывай</u> оставлять <u>пустую строку</u> в конце файла, во избежании глюков.
			</div>
			<?=$err;?>
			<form role="form" action="./" method="POST">
				<div class="form-group">
				<h2>Общая база всех уровней</h2>	 
				<textarea class="form-control" name="file" rows="20"><?php readfile($filedb); ?></textarea>

				</div> 
			<button type="submit" name="submit" title="Если кнопка не работает - значит сайт включен" class="btn btn-block btn-lg btn-info<?=$disable;?>"<?=$disable;?>>
				Сохранить изменения
			</button>
			</form>
			
			
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Список сгенерированных файлов уровней</h4>
      </div>
      <div class="modal-body" style="height:360px;">
        <iframe id="myIframe" name="Right" src="https://kplevels.ru/e/txt/" width="100%" height="360px" scrolling="yes" frameborder="0"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
<script>
function refreshIframe() {
    var ifr = document.getElementsByName('Right')[0];
    ifr.src = ifr.src;
}
</script>
  </body>
</html>