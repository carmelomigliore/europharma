<?php
$db = new PDO("pgsql:dbname=europharma;host=localhost", "myuser", "password"); 
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
