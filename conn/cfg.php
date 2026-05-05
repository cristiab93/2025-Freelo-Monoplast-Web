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
    define("DBSERVERNAME", "167.250.5.72");
	define("DBUSERNAME", "nkkmijbj_cris");
	define("DBPASSWORD", "0112358Cris");
	define("DBNAME", "nkkmijbj_monoplast_v2");
	define("DEPURAR", 0);
	define("BASEURL", "https://monoplast.com.ar/");
}