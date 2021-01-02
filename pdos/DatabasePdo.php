<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "softsquared.cwcnegneyrah.us-east-2.rds.amazonaws.com";
        $DB_NAME = "everytime";
        $DB_USER = "admin";
        $DB_PW = "98xoxo0408";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}