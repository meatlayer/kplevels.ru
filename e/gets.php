<?php
if ( isset($_GET['lvl']) && $_GET['lvl'] == 20 )  { // 20 уровней
@header('Content-type: application/txt');//тут тип
@header('Content-Disposition: attachment; filename="'.date("d.m.Y").'_20L.txt"');//имя
readfile('txt/'.date("d.m.Y").'_20L.txt');
} elseif ( isset($_GET['lvl']) && $_GET['lvl'] == 100 ) { // 100 уровней
@header('Content-type: application/txt');//тут тип
@header('Content-Disposition: attachment; filename="'.date("d.m.Y").'_100L.txt"');//имя
readfile('txt/'.date("d.m.Y").'_100L.txt');
}
?>