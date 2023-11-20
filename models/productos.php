<?php
class Productos extends Conectar {
    //TRAE TODOS LOS DATOS
    public function get_Productos() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT p.*, s.cantidad_en_stock AS cantidad_stock FROM productos p
        LEFT JOIN stock s ON p.id_productos = s.fk_producto";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    }
    //TRAE DATOS POR ID
    public function get_Productos_x_id($id_productos) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT p.*, s.cantidad_en_stock AS cantidad_stock FROM productos p INNER JOIN stock s ON p.id_productos= s.fk_producto WHERE p.id_productos = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $id_productos);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    } 
     //TRAE DATOS POR CODIGO
     public function get_Productos_Codigo($codigo) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT p.*, s.cantidad_en_stock AS cantidad_stock FROM productos p INNER JOIN stock s ON p.id_productos= s.fk_producto WHERE p.codigo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codigo);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    }  
    //TRAE DATOS POR NOMBRE
    public function get_Productos_Nombre($nombre) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT p.*, s.cantidad_en_stock AS cantidad_stock FROM productos p INNER JOIN stock s ON p.id_productos= s.fk_producto WHERE p.nombre = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $nombre);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    } 
    // METODO POST
    public function post_productos_stock($codigo, $nombre, $precio, $cantidad, $fecha_vencimiento, $cantidad_en_stock)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        try {
            // Iniciar transacción
            $conectar->beginTransaction();

            // Insertar en la tabla "productos"
            $sql_productos = "INSERT INTO productos (codigo, nombre, precio, cantidad, fecha_vencimiento) VALUES (?, ?, ?, ?, ?)";
            $stmt_productos = $conectar->prepare($sql_productos);
            $stmt_productos->bindValue(1, $codigo);
            $stmt_productos->bindValue(2, $nombre);
            $stmt_productos->bindValue(3, $precio);
            $stmt_productos->bindValue(4, $cantidad);
            $stmt_productos->bindValue(5, $fecha_vencimiento);
            $stmt_productos->execute();

            // Obtener el ID del producto recién insertado
            $id_producto = $conectar->lastInsertId();

            // Verificar si el producto ya existe
            $sql_verificar_producto = "SELECT id_productos FROM productos WHERE id_productos = ?";
            $stmt_verificar_producto = $conectar->prepare($sql_verificar_producto);
            $stmt_verificar_producto->bindValue(1, $id_producto);
            $stmt_verificar_producto->execute();

            if ($stmt_verificar_producto->rowCount() > 0) {
                // El producto existe, puedes continuar con la inserción en la tabla "stock"
                $sql_stock = "INSERT INTO stock (fk_producto, cantidad_en_stock) VALUES (?, ?)";
                $stmt_stock = $conectar->prepare($sql_stock);
                $stmt_stock->bindValue(1, $id_producto);
                $stmt_stock->bindValue(2, $cantidad_en_stock);
                $stmt_stock->execute();
            } else {
                // El producto no existe, puedes manejar este caso según tus necesidades
                $conectar->rollBack();
                return ["status" => "error", "message" => "El producto no existe"];
            }

            // Confirmar transacción
            $conectar->commit();

            return ["status" => "success", "message" => "Registro insertado correctamente"];
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conectar->rollBack();

            return ["status" => "error", "message" => "Error al insertar el registro: " . $e->getMessage()];
        }
    }

    //METODO UPDATE PRODUCTO
    public function update_Productos($id_productos,$codigo, $nombre, $precio, $cantidad, $fecha_vencimiento) {
        $conectar = parent::Conexion();
        parent::set_names();
        
        $sql = "UPDATE productos SET
                codigo = ?,
                nombre = ?,
                precio = ?,
                cantidad = ?,
                fecha_vencimiento = ?
                WHERE id_productos = ?";
        
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $codigo);
        $sql->bindValue(2, $nombre);
        $sql->bindValue(3, $precio);
        $sql->bindValue(4, $cantidad);
        $sql->bindValue(5, $fecha_vencimiento);
        $sql->bindValue(6, $id_productos);  
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    } 
    // METODO DELETE PRODUCTO
    public function Delete_Productos($id_productos) {
        $conectar = parent::Conexion();
        parent::set_names();
    
        try {
            // Iniciar transacción
            $conectar->beginTransaction();
    
            // Eliminar filas relacionadas en la tabla "stock"
            $sql_stock = "DELETE FROM stock WHERE fk_producto = ?";
            $stmt_stock = $conectar->prepare($sql_stock);
            $stmt_stock->bindValue(1, $id_productos);
            $stmt_stock->execute();
    
            // Verificar si la operación fue exitosa
            if ($stmt_stock->rowCount() > 0) {

                // Eliminar la fila en la tabla "productos"
                $sql_productos = "DELETE FROM productos WHERE id_productos = ?";
                $stmt_productos = $conectar->prepare($sql_productos);
                $stmt_productos->bindValue(1, $id_productos);
                $stmt_productos->execute();
    
                // Verificar si la operación fue exitosa
                if ($stmt_productos->rowCount() > 0) {

                    // Confirmar transacción
                    $conectar->commit();
                    return ["status" => "success", "message" => "Delete correcto"];
                } else {
                    throw new Exception("No se pudo eliminar el producto");
                }
            } else {
                throw new Exception("No se pudo eliminar el stock del producto");
            }
        } catch (Exception $e) {
            
            // Revertir transacción en caso de error
            $conectar->rollBack();
    
            return ["status" => "error", "message" => "Error al intentar eliminar: " . $e->getMessage()];
        }
    }
    
}  
?>