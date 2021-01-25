<?php

$databaseURL = getenv('DATABASE_URL');

//$db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
$db = pg_connect($databaseURL);
