<?php

//こレのために外部化するの草

function pdo()
{
	try{
		$pdo = new PDO(DSN, DB_USER, DB_PASS);
		$pdo-> exec('set names utf8');
		$pdo-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}catch(PDOException $e){
		echo 'error:'.$e->getMessage();
		exit;
	}
}


 ?>
