<?php
session_start();

function myConexion() {
    require_once "conexion.php";
    $conexion = new mysqli($servername, $username, $passworddb, $dbname);
    if ($conexion->connect_error) {
        die("Error de conexion" . $conexion->connect_error);
    }
    return $conexion;
}


function consultarIdUsu($conectada){
    $stmt = $conectada->prepare("SELECT id_usu FROM usuario WHERE usuario =?");
    $stmt->bind_param("s", $_SESSION["usuario"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    if ($result->num_rows > 0) {
        var_dump($row);
        return $row["id_usu"];
       
    }
    return null;
   
}

function insertarControlGlucosa($conectada, $id_usu){
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST["fecha"])){
            $fecha = $_POST["fecha"];
            $deporte = 0;
            $lenta = 0; 
            
            try{
                $stmt = $conectada->prepare("INSERT IGNORE INTO control_glucosa (fecha,deporte,lenta, id_usu) VALUES (?,?,?,?)");
                $stmt->bind_param("siii",$fecha, $deporte, $lenta, $id_usu);
                $stmt->execute();
                echo "Registro insertado correctamente: control_glucosa";
               
            }catch(mysqli_sql_exception $e){
                echo "Error al añadir datos: ". $e->getMessage();
            }
        }
    }

}

function insertarComida($conectada, $id_usu) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        var_dump($_POST);  // Para ver qué datos llegan desde el formulario

        if (!empty($_POST["fecha"]) && !empty($_POST["comida"])) {
            $fecha = $_POST["fecha"];
            $comida = $_POST["comida"];

            // Convertir valores numéricos y manejar vacíos correctamente
            $gl1h = isset($_POST["gl1h"]) && $_POST["gl1h"] !== '' ? (int)$_POST["gl1h"] : null;
            $rac = isset($_POST["rac"]) && $_POST["rac"] !== '' ? (int)$_POST["rac"] : null;
            $insulina = isset($_POST["insulina"]) && $_POST["insulina"] !== '' ? (int)$_POST["insulina"] : null;
            $gl2h = isset($_POST["gl2h"]) && $_POST["gl2h"] !== '' ? (int)$_POST["gl2h"] : null;

            var_dump($fecha, $comida, $gl1h, $gl2h, $rac, $insulina, $id_usu); // Verificar variables

            if (!$conectada) {
                die("❌ Error en la conexión a la base de datos: " . mysqli_connect_error());
            }

            try {
                // Ajustamos la consulta para no ignorar errores y permitir NULL en valores vacíos
                $stmt = $conectada->prepare("INSERT IGNORE INTO comida (tipo_comida, gl_1h, gl_2h, raciones, insulina, fecha, id_usu) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("siiiisi", $comida, $gl1h, $gl2h, $rac, $insulina, $fecha, $id_usu);
                
                if (!$stmt->execute()) {
                    echo "❌ Error en la ejecución: " . $stmt->error;
                } else {
                    //echo "✅ Registro insertado correctamente.";
                    header("Location: consulta.php");
                } 
            } catch (mysqli_sql_exception $e) {
                echo "❌ Error al añadir datos: " . $e->getMessage();
            } 
        } else {
            echo "⚠️ Faltan datos en el formulario.";
        }
    }
}

function insertaHiperGlucemia($conectada, $id_usu){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        var_dump($_POST);
        if(!empty($_POST["fecha"])&&!empty($_POST["comida"]) && !empty($_POST["correccion"]) && !empty($_POST["glucosa2"]) &&
            !empty($_POST["hora2"])    
        ){
            $correccion = $_POST["correccion"]; 
            $glucosa = $_POST["glucosa2"];
            $hora = $_POST["hora2"];
            $comida = $_POST["comida"];
            $fecha = $_POST["fecha"];
            if (!$conectada) {
                die("❌ Error en la conexión a la base de datos: " . mysqli_connect_error());
            }
            try{
                $sqlInsert = $conectada->prepare("INSERT INTO hiperglucemia (glucosa, hora, correccion, tipo_comida, fecha, id_usu) 
                VALUES (?, ?, ?, ?, ?, ?)");
                $sqlInsert->bind_param("isissi", $glucosa, $hora, $correccion, $comida, $fecha, $id_usu);
                if(!$sqlInsert->execute()){
                    echo "❌ Error en la ejecución: " . $sqlInsert->error;
                } else {
                    echo "✅ Registro insertado correctamente.";
                
                }
            }catch(mysqli_sql_exception $e) {
                echo "❌ Error al añadir datos: " . $e->getMessage();
            } 

        }else{
            echo "⚠️ Faltan datos en el formulario.";
        }
    }  
};

function insertaHipoGlucemia($conectada, $id_usu){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        var_dump($_POST);
        if(!empty($_POST["fecha"])&&!empty($_POST["comida"]) && !empty($_POST["glucosa1"]) &&
            !empty($_POST["hora1"])    
        ){
           
            $glucosa = $_POST["glucosa1"];
            $hora = $_POST["hora1"];
            $comida = $_POST["comida"];
            $fecha = $_POST["fecha"];
            if (!$conectada) {
                die("❌ Error en la conexión a la base de datos: " . mysqli_connect_error());
            }
            try{
                $sqlInsert = $conectada->prepare("INSERT INTO hipoglucemia (glucosa, hora, tipo_comida, fecha, id_usu) 
                VALUES (?, ?, ?, ?, ?)");
                $sqlInsert->bind_param("isssi", $glucosa, $hora, $comida, $fecha, $id_usu);
                if(!$sqlInsert->execute()){
                    echo "❌ Error en la ejecución: " . $sqlInsert->error;
                } else {
                    echo "✅ Registro insertado correctamente.";
                
                }
            }catch(mysqli_sql_exception $e) {
                echo "❌ Error al añadir datos: " . $e->getMessage();
            } 

        }else{
            echo "⚠️ Faltan datos en el formulario.";
        }
    }  
}

$conectada = myConexion();
$id_usu = consultarIdUsu($conectada);
    insertarControlGlucosa($conectada, $id_usu); 
    insertarComida($conectada, $id_usu);
    insertaHiperGlucemia($conectada, $id_usu);
    insertaHipoGlucemia($conectada, $id_usu);
   

?>