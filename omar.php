
// Estos son parámetros Base para una conexión de BD con
//MYSQL en nuestro hosting.
<?php
echo "omar ";
include "config.php";
$ds = new DataSource;
$ds->dataBase = $db;
$ds->setTable("db");
$ds->setFields("host");
$q = $ds->select();
echo mysql_num_rows($q);
$w = $ds->getRow(0);
echo $w[1];
?>