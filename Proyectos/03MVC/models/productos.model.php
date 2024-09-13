<?php
// TODO: Clase de Producto
require_once('../config/config.php');
// Clase Producto refactorizada para recibir la conexión como parámetro

class Producto
{
    private $conexion;

    // Constructor para inicializar la conexión
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function todos() // select * from productos
    {
        $cadena = "SELECT p.idProductos, 
                        p.Codigo_Barras, 
                        p.Nombre_Producto, 
                        p.Graba_IVA, 
                        u.Detalle as Unidad_Medida, 
                        i.Detalle as IVA_Detalle, 
                        k.Cantidad, 
                        k.Fecha_Transaccion, 
                        k.Valor_Compra, 
                        k.Valor_Venta, 
                        k.Tipo_Transaccion
                   FROM `Productos` p
                   INNER JOIN `Unidad_Medida` u ON p.idProductos = u.idUnidad_Medida
                   INNER JOIN `IVA` i ON p.Graba_IVA = i.idIVA
                   INNER JOIN `Kardex` k ON p.idProductos = k.Productos_idProductos
                   WHERE k.`Estado` = 1";
        $datos = mysqli_query($this->conexion, $cadena);
        return $datos;
    }

    public function uno($idProductos) // select * from productos where id = $idProductos
    {
        $cadena = "SELECT p.*, u.Detalle as Unidad_Medida, i.Detalle as IVA_Detalle 
                   FROM `Productos` p 
                   INNER JOIN Unidad_Medida u ON p.idProductos = u.idUnidad_Medida 
                   INNER JOIN IVA i ON p.Graba_IVA = i.idIVA 
                   WHERE p.idProductos = $idProductos";
        $datos = mysqli_query($this->conexion, $cadena);
        return $datos;
    }

    public function insertar($Codigo_Barras, $Nombre_Producto, $Graba_IVA, $Unidad_Medida_idUnidad_Medida, $IVA_idIVA, $Cantidad, $Valor_Compra, $Valor_Venta, $Proveedores_idProveedores)
    {
        try {
            // Insertar el producto
            $cadenaProducto = "INSERT INTO `Productos`(`Codigo_Barras`, `Nombre_Producto`, `Graba_IVA`) 
                               VALUES ('$Codigo_Barras', '$Nombre_Producto', '$Graba_IVA')";

            if (mysqli_query($this->conexion, $cadenaProducto)) {
                $productoId = $this->conexion->insert_id; // Obtener el ID del producto recién creado

                // Insertar el Kardex asociado al producto
                $cadenaKardex = "INSERT INTO `Kardex`(`Estado`, `Fecha_Transaccion`, `Cantidad`, `Valor_Compra`, `Valor_Venta`, `Unidad_Medida_idUnidad_Medida`, `Unidad_Medida_idUnidad_Medida1`, `Unidad_Medida_idUnidad_Medida2`, `IVA`, `IVA_idIVA`, `Proveedores_idProveedores`, `Productos_idProductos`, `Tipo_Transaccion`)
                                 VALUES (1, NOW(), '$Cantidad', '$Valor_Compra', '$Valor_Venta', '$Unidad_Medida_idUnidad_Medida', '$Unidad_Medida_idUnidad_Medida', '$Unidad_Medida_idUnidad_Medida', '$IVA_idIVA', '$IVA_idIVA', '$Proveedores_idProveedores', '$productoId', 1)";

                if (mysqli_query($this->conexion, $cadenaKardex)) {
                    return $productoId; // Éxito, devolver el ID del producto
                } else {
                    return $this->conexion->error; // Error en el Kardex
                }
            } else {
                return $this->conexion->error; // Error en el producto
            }
        } catch (Exception $th) {
            return $th->getMessage();
        }
    }

    public function actualizar($idProductos, $Codigo_Barras, $Nombre_Producto, $Graba_IVA) // update productos set ... where id = $idProductos
    {
        try {
            $cadena = "UPDATE `Productos` SET 
                       `Codigo_Barras`='$Codigo_Barras',
                       `Nombre_Producto`='$Nombre_Producto',
                       `Graba_IVA`='$Graba_IVA'
                       WHERE `idProductos` = $idProductos";
            if (mysqli_query($this->conexion, $cadena)) {
                return $idProductos; // Éxito, devolver el ID actualizado
            } else {
                return $this->conexion->error;
            }
        } catch (Exception $th) {
            return $th->getMessage();
        }
    }

    public function eliminar($idProductos) // delete from productos where id = $idProductos
    {
        try {
            $cadena = "UPDATE `kardex` SET `Estado`=0 WHERE `Productos_idProductos`=$idProductos";
            if (mysqli_query($this->conexion, $cadena)) {
                return 1; // Éxito
            } else {
                return $this->conexion->error;
            }
        } catch (Exception $th) {
            return $th->getMessage();
        }
    }
}
