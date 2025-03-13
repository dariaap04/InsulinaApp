<?php
    session_start(); 
    function myConexion(){
        require_once "conexion.php"; 
        $conexion = new mysqli ($servername, $username, $passworddb, $dbname);
        if($conexion->connect_error){
            die("Error de conexion".$conexion->connect_error);
        }
        return $conexion;
    }

    function registro($conectada){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if (!empty($_POST["fecha_nacimiento"]) && 
            !empty($_POST["nombre"]) && 
            !empty($_POST["apellidos"]) &&
            !empty($_POST["usuario"]) && 
            !empty($_POST["contra"])) {
                
                $nacimiento = ($_POST["fecha_nacimiento"]);
                $nombre = ($_POST["nombre"]);
                $apellido =($_POST["apellidos"]);
                $usuario =($_POST["usuario"]);
                $contrasenia = password_hash($_POST["contra"], PASSWORD_ARGON2ID);

                    $query = "SELECT * FROM usuario WHERE usuario = '$usuario'";
                    $result = $conectada->query($query);
                    if($result->num_rows > 0){
                        echo "el usuario ya existe";
                    }else{
                        $sql = "INSERT INTO usuario (fecha_nacimiento, nombre, apellidos, usuario, contra) 
                                VALUES ('$nacimiento', '$nombre', '$apellido', '$usuario', '$contrasenia')";
                        if($conectada->query($sql) === TRUE){
                            echo "El usuario se ha registrado correctamente";
                            header("Location: index.html");exit(); 
                        }else{
                            echo "Error: ". $sql. "<br>". $conectada->error;
                        }
                    }

            }else{
                $_SESSION["error"] = 1;
                header("Location: registro.php");
                exit();
            }
        } 
    }


    $conectada = myConexion();
     registro($conectada);
     
?>