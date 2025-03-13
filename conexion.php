<?php
    $servername = "localhost"; 
    $username = "root";
    $passworddb=""; 
    $dbname = "insulinadb";

    $conexion = new mysqli ($servername, $username, $passworddb, $dbname);
    if($conexion->connect_error){
        echo "Error al conectar a la base de datos";
    };
?>