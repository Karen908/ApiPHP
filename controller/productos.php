<?php
header('Content-Type: application/json');

require_once("../config/conexion.php");
require_once("../models/productos.php");
$productos = new Productos();

//Metodo GET
//TRAE TODOS LOS REGISTROS 
$body= json_decode(file_get_contents("php://input"), true);
switch ($_GET["op"]) {
    case "GetAll":
        $datos = $productos->get_Productos();
        echo json_encode($datos);
        break;
//TRAE POR ID
        case "GetId":
        $datos = $productos->get_Productos_x_id($body["id_productos"]);
        echo json_encode($datos);
        break;
//TRAE POR CODIGO
        case "GetCodigo":
        $datos = $productos->get_Productos_Codigo($body["codigo"]);
        echo json_encode($datos);
        break;
//TRAE POR NOMBRE
        case "GetNombre":
        $datos = $productos->get_Productos_Nombre($body["nombre"]);
        echo json_encode($datos);
        break;

//METODO POST
        case "Post":
        if (
            isset($body["codigo"], $body["nombre"], $body["precio"], $body["cantidad"], $body["fecha_vencimiento"], $body["cantidad_en_stock"])
            && $body["codigo"] !== null
            && $body["nombre"] !== null
            && $body["precio"] !== null
            && $body["cantidad"] !== null
            && $body["fecha_vencimiento"] !== null
            && $body["cantidad_en_stock"] !== null
        ) {
        // Lllamamos al metodo post_productos_stock
        $result = $productos->post_productos_stock(
            $body["codigo"],
            $body["nombre"],
            $body["precio"],
            $body["cantidad"],
            $body["fecha_vencimiento"],
            $body["cantidad_en_stock"]
        );
         echo json_encode($result);
        } else {
        // Informar sobre campos faltantes o nulos
        echo json_encode(["status" => "error", "message" => "Campos requeridos no presentes o nulos"]);
        }
         break;

                
//METODO UPDATE 
        case "Update":
              $datos=$productos->update_Productos (
              $body["id_productos"],
              $body["codigo"],
              $body["nombre"],
              $body["precio"],
              $body["cantidad"],
              $body["fecha_vencimiento"]);
              echo json_encode("update correcto");
              break;
                
//METODO DELETE 

        case "Delete":
               $datos = $productos->Delete_Productos($body["id_productos"]);
                echo json_encode($datos);
                break;
}
?>