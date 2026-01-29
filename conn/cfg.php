<?php

if($_SERVER['HTTP_HOST'] == 'localhost:82' || $_SERVER['HTTP_HOST'] == 'localhost' )
{
    define("DBSERVERNAME", "localhost");
	define("DBUSERNAME", "cristianb");
	define("DBPASSWORD", "511xpWgxUR4icML4");
	define("DBNAME", "monoplast");
	define("DEPURAR", 0);
	define("BASEURL", "http://localhost/freelo_monoplast");
}
else
{
    define("DBSERVERNAME", "sql308.infinityfree.com");
	define("DBUSERNAME", "if0_41017142");
	define("DBPASSWORD", "0112358Cris");
	define("DBNAME", "if0_41017142_testing");
	define("DEPURAR", 0);
	define("BASEURL", "https://testing2026.infinityfree.me/");
}