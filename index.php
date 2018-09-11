<?php
mb_internal_encoding( 'utf-8' );
$filest = 'off';
if (file_exists($filest)) {die('<center><img src="images/kpoff.png" align="center" alt="OFF"><br><b>Почему сайт не работает?</b> <br>Ответ прост, для выпуска Happy Wheels - Kuplinov Play набралось слишком много уровней, поэтому новые уровни временно не принимаются. <br>Как только Дмитрий объявит новый набор, уровни вновь возможно будет предложить!</center>');}
session_start();
$stop = array("semchenkodragonlore","adamsondragonlore","vk.com","youtube","xyu","hui","pizd","soset","pidor","hueplet","putin","mudak","Cyka","Syka","Zhopa","blua","govno","zalupa","penis","davalka","blyadina","kuplinov-loh","Suck","fukcer","Cacau","DICK","kuplinov bes","timof","P_i_d_o_r","ࡢƔ","Happy Wheels","Sosi","naxui","proidi","4len","suka","molodec","fuck","sasai","Azaza","lololo","siska","vagina","anus","xyit","SASAY","eban","pedik","debil","mamk","ebal","AKBAR","kakah","pidr","JloX","pipis");

$err = ''; //ошибка
$lvl = ''; //уровень
$aut = ''; //автор

function GetHappyLevels($author, $levelname)
{
	//author должен иметь длину >=4
	$author = substr($author, 0, 12);//максимальная длина 12, так что обрезаем
	$levelname = 'ln="'.substr($levelname, 0, 12);//добавляем к префиксу ln=", чтобы искать только самое начало названия (само название является значением ln в xml ответе сервера)
	
	$matches = array();
	$req = POST('http://www.totaljerkface.com/get_level.hw', "sortby=newest&action=search%5Fby%5Fuser&page=1&sterm=$author&uploaded=week");
	//делаем запрос на сервер happy wheels, получая список уровней по автору
	while (stripos($req, $levelname) !== false)//пока есть хотя бы один уровень с таким префиксом в названии
	{
		$req = substr($req, stripos($req, $levelname) - 20);//20 - число, вычитая которое мы перемещаемся примерно в начало строки-описания уровня
		
		$id = substrHelper($req, 'id="', true);
		$id = substrHelper($id, '"', false);
		
		$ln = substrHelper($req, 'ln="', true);
		$ln = substrHelper($ln, '"', false);
		
		$un = substrHelper($req, 'un="', true);
		$un = substrHelper($un, '"', false);//получение id, названия и ника
		
		array_push($matches, array("id"=>$id, "ln"=>$ln, "un"=>$un));//запись в массив
		
		$req = substr($req, stripos($req, $levelname) + 4);//затираем название уровня, чтобы while перешёл к следующему вхождению
	}
	
	return json_encode($matches);
}

function substrHelper($string, $tofind, $after)
{
	if ($after)
		return substr($string, stripos($string, $tofind) + strlen($tofind));
	else
		return substr($string, 0, stripos($string, $tofind));
}

function POST($url, $data)//POST запрос, используя curl
{
	$curl = @curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 4);
    curl_setopt($curl, CURLOPT_REFERER, "https://www.youtube.com/playlist?list=PLejGw9J2xE9Uu9HdaMJ3LSDbA9cDv2r47");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $output = @curl_exec($curl);
    curl_close($curl);
	return $output;
}

function setRandomSessid() {
	$_SESSION['sessid'] = md5(date('d.m.Y H:i:s').rand(1, 1000000));
}
//setRandomSessid();

if (!function_exists('checkddos')) {
	function checkddos($sec) {
		$ban=0; $file="protect.dat"; $time=time(); $ip=$_SERVER[REMOTE_ADDR];
		$whitelist=array("127.0.0.1","91.42.*.*");
		$x=explode(".",$ip); foreach($whitelist as $ip1) if(preg_match("/^$x[0]\.($x[1]|\*)\.($x[2]|\*)\.($x[3]|\*)$/",$ip1)) return 0;
		$f=@fopen($file,"r"); if($f) {clearstatcache(); flock($f,LOCK_SH); $r=@fread($f,filesize($file)); fclose($f);}
		$a=unserialize($r);
		if($a[$ip]+$sec>=$time) $ban=1; $a[$ip]=$time;
		foreach($a as $k=>$v) if($v+$sec+1<$time) unset($a[$k]);
		file_put_contents($file,serialize($a),LOCK_EX);
		return $ban;
	}
}

if (!function_exists('string_exists')) {

    function string_exists($subject, $params) {

        $pattern = "";

        if (is_array($params)) {
            $pattern = mb_strtolower(implode('|', $params));
        } else if (is_string($params)) {
            $pattern = mb_strtolower($params);
        }

        return (preg_match("/($pattern)/", mb_strtolower($subject)));
    }

}

// если форма отправлена
if ( isset($_POST['submit']) && $_POST['level']!=="" && $_POST['sessid'] == $_SESSION['sessid'] ) 

{ 

	setRandomSessid();
	
	$rptsearch = trim($_POST['level']).' ↔ '.trim($_POST['author']);

	if( $_SESSION['codepic']!==strtolower($_POST['codepic']) )
		{
		$err = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Ошибка!</strong> Не верно введён защитный код.</div>';
		}
		
	// если уже есть такой лвл и автор - шлём нах
	elseif(trim($_POST['author'])!=="" && stristr(@file_get_contents('hw.dat'), $rptsearch) != false) 
		{
		$err = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Ошибка!</strong> Такой уровень и автор уже есть в списке.</div>';
		}

	// если стоп-слово - шлём нах
	elseif( string_exists(trim($_POST['level']), $stop) || string_exists(trim($_POST['author']), $stop) || preg_match("/[а-яё]/iu", trim($_POST['level'])) || preg_match("/[а-яё]/iu", trim($_POST['author'])) || mb_strlen(trim($_POST['level'])) < 4 || mb_strlen(trim($_POST['author'])) < 4)
		{
		$err = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Ошибка!</strong> В предложенном уровне или имени автора недопустимые символы.</div>';
		}
		
	// если уже добавлялось - шлём нах
	elseif(checkddos(604000) || $_COOKIE["level"]) 
		{ 
		$err = '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Друг!</strong> Ранее от тебя уже был принят уровень. В целях предотвращения большой очереди и спама, в неделю принимается только 1 уровень.</div>';
		} 
	
	// если отсутствует в базе HW - шлём нах
	elseif( count(json_decode(GetHappyLevels( trim($_POST['author']), trim($_POST['level']) ))) < 1 ) 	
		{
		$err = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Ошибка!</strong> Предложенный уровень или автор не существуют в игре.</div>';
		}

	
	// иначе записываем данные в файл
	else 
	{
		$brr = array("↔", "✔", "\r\n", "\n", "\r", "\t", "\0", "\x0B", "\\", "/");
		
		$lvl = str_replace($brr, '', $_POST['level']);
		$lvl = substr(trim($lvl), 0, 25);

		$aut = str_replace($brr, '', $_POST['author']);
		$aut = substr(trim($aut), 0, 15);
		if($aut=="") $aut = "неизвестен";
		
		// проверим и удалим пустые строки в файле
		$base = @file_get_contents("hw.dat");
		$base = trim(preg_replace("/[\r\n]+/m","\r\n", $base));
		$openFile = @fopen("hw.dat", 'w+');
		if (flock($openFile, LOCK_EX)) { 	
		@fwrite($openFile, $base);                     
		@fclose($openFile); }

		@file_put_contents("hw.dat", "\r\n$lvl ↔ $aut ↔ ".date("d.m.Y")."\r\n", FILE_APPEND | LOCK_EX);
		@file_put_contents("ip.txt", "$lvl ↔ $aut ↔ ".date("d.m.Y")." ↔ ".$_SERVER['REMOTE_ADDR']."\r\n", FILE_APPEND | LOCK_EX);
		setcookie("level", $lvl, time()+603000);
		$err = '<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Спасибо!</strong> Предложенный уровень успешно добавлен в список.</div>';
		
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kuplinov ► Play: HW Levels</title>

    <meta name="description" content="Предложение уровней от зрителей Kuplinov ► Play в игре Happy Wheels.">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	<link href="css/theme.default.min.css" rel="stylesheet">

  </head>
  <body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="text-center">

				<a href="/"><h1>Kuplinov ► Play</h1></a>      
				<p>База уровней для Happy Wheels</p>
				<img src="images/hw.jpg" width="150px" border="0" align="middle">

			</div>
		</div>
	</div>

	
	<div class="row">
		<div class="col-md-12">
			<div class="text-center">

	<fieldset>
	  <legend></legend>

		<?php echo $err; ?>
		<form id="kpForm" class="form-inline" role="form" action="./" method="POST">
		<input type="hidden" name="sessid" value="<?=$_SESSION['sessid']?>">
		  <div class="form-group form-inline">
				<div class="form-group">
				  <label class="control-label" for="lvl">Название уровня</label>
				  <div>
					<input placeholder="—= Rap Battle 3 =—" name="level" id="lvl" class="input-lg form-control" type="text" maxlength="35" required="">
				  </div>
				</div>
			
				<div class="form-group">
				  <label class="control-label" for="aut">Автор уровня</label>
				  <div>
					<input placeholder="sachamun" class="input-lg form-control" id="aut" name="author" type="text" maxlength="35" required="">
				  </div>
				</div>

				<div class="form-group">
				  <label class="control-label" for="aut">Защитный код</label>
				  <div>
					<input placeholder="123" class="input-lg form-control" style="width:100px;" id="cod" name="codepic" type="text" maxlength="3" required="">
				  </div>
				</div>
					<div class="form-group">
					 <label class="control-label" for="cod"> </label>
						<div>
						<img src="image.php" alt="secure" />
						</div> 
					</div> 

					<div class="form-group">
					 <label class="control-label" for="ok"> </label>
						<div>
					<button type="submit" name="submit" class="btn btn-success btn-lg">
						Предложить уровень
					</button>
						</div> 
					</div> 
			</div>
		</form>
			<br>
	</fieldset>

			</div>
		</div>
	</div>
		
		

	<div class="row">
		<div class="col-md-12">
			<div class="text-center">
			

				<br>
				<button type="button" class="clrs btn btn-default"><span class="glyphicon glyphicon-trash"></span> сбросить сортировку</button><br>
				<br>
				
			   
<?php
$arr = @file("hw.dat");
// Общее количество строк
$count = count($arr);
// Если строк больше 0
if($count>0)
  {
   // Если страница НЕ определена - вывести с первой. Если определена - использовать определённую
   if(!isset($_GET["page"])?$_GET["page"]=1:$_GET["page"]=(int)$_GET["page"]);

   // По сколько строк выводить на страницу
   $list = 50;
   // Количество строк (вместе с текущей)
   $j=($count-1)-(($_GET["page"]-1)*$list);
   // Количество оставшихся строк (без текущей)
   $i=$j-$list;
   // Количество страниц
   $all=ceil($count/$list);
   
         if($_GET["page"]=="" || $_GET["page"] < 0 || $_GET["page"] > $all) {echo '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Нет такой страницы!</strong> Маленький шалун ;-)</div>';} else {
   ?>
			<div class="table-responsive">
   			   <table id="klevels">
				   <thead>
					   <tr>
					   <th title="сортировать по уровню">название уровня</th><th title="сортировать по автору">имя автора</th><th title="сортировать по дате">дата</th>
					   </tr>
				   </thead>
			   <tbody>
	<?
   // Вывести строки для выбранной страницы
   for(;$i<$j&&$j>=0;$j--)
      {
       // Рабить построчно
	   $elem=explode("↔",$arr[$j]);

$ellevel=''; $elauthor=''; $eldate='';
$ellevel=$elem[0]; $elauthor=$elem[1]; $eldate=$elem[2];
if(count($elem) == 2) {$ellevel=$elem[0]; $elauthor='неизвестен'; $eldate=$elem[1];}

       // Вывести
       echo "<tr><td>".trim(htmlspecialchars($ellevel))."</td><td>".trim(htmlspecialchars($elauthor))."</td><td>".trim(htmlspecialchars($eldate))."</td></tr>\n";
      }
	  ?> 
				  </tbody>
				</table>
				</div>
				
			<nav>
			<ul class="pagination">

<?php
   // Количество страниц
   $all=ceil($count/$list);
   // Вывести навигацию
	$active='';
	$numbpage=1; if($_GET["page"]) $numbpage=(int)$_GET["page"];
	$curtpage=1; if($numbpage<=1) $curtpage=1; else $curtpage=$numbpage;
	$prevpage=$numbpage-1; if( $numbpage <=1 ) $prevpage=1;
	$nextpage=$numbpage+1; if( $numbpage <=1 || $numbpage >= $all ) $nextpage=2;
	if($numbpage >= $all) $active=' class="disabled"';

    echo '<li><a href="?page='.$prevpage.'">&laquo; Назад</a></li>';
    echo '<li><a href="?page='.$all.'">'.$curtpage.' / '.$all.'</a></li>';
    echo '<li'.$active.'><a href="?page='.$nextpage.'"'.$active.'>Вперёд &raquo;</a></li>';


	?> </ul></nav><?
  }
  }
?>				
				
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="text-center">

			<ul class="breadcrumb">

				<li class="active"> <?php echo "Всего уровней в базе: <span class=\"badge\">$count</span>";?></li>
				<li><a href="https://www.youtube.com/playlist?list=PLejGw9J2xE9Uu9HdaMJ3LSDbA9cDv2r47">Youtube</a></li>
				<li><a href="https://vk.com/kuplinovplay">Вконтакте</a></li>
				<li><a href="https://vk.com/dmitry.kuplinov">Дмитрий Куплинов</a></li><br>
				<li><small>Настоящий ресурс может содержать материалы 16+<br><a href="thanks.txt" target="_blank" class="bg-success">Благодарность</a> | <a href="images/c1.jpg" target="_blank" class="bg-info">Обратная связь</a></small></li>

			</ul>

			</div>
		</div>
	</div>

</div>
    <script src="js/jquery.min.js"></script>
	<script src="js/jquery.tablesorter.min.js"></script>
	<script src="js/jquery.tablesorter.widgets.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/responsive-paginate.js"></script>
    <script src="js/scripts.js"></script>
<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter34540695 = new Ya.Metrika({ id:34540695, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, ut:"noindex" }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/34540695?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
</body>
</html>