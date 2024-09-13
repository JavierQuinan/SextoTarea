<?php
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', dirname(__FILE__) . '/font/');
}

require('fpdf/fpdf.php');
require_once("../models/productos.model.php");
require_once("../config/config.php");

// Definir rutas
$logo_local = '../public/images/image.png';  // Ruta local para el logo
$logo_url = 'https://www.uniandes.edu.ec/wp-content/uploads/2024/07/2-headerweb-home-2.png';  // URL externa

// Crear conexión a la base de datos
$con = new ClaseConectar();
$conexion = $con->ProcedimientoParaConectar();

// Crear instancia de la clase Producto
$productos = new Producto($conexion);

// Obtener los productos desde la base de datos
$listaproductos = $productos->todos();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Encabezado de la empresa
$pdf->Image($logo_local, 10, 6, 30);  // Logo de la empresa local
$pdf->Cell(80); // Moverse a la derecha
$pdf->Cell(30, 10, 'Empresa Compu Solutions', 0, 1, 'C');  // Nombre de la empresa
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80);
$pdf->Cell(30, 5, 'RUC: 0302263884001', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Direccion: Calle Simòn Bolivar, Azogues, Ecuador', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Telefono: +593 983912632', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Email: compu@gmail.com', 0, 1, 'C');
$pdf->Ln(10); // Salto de línea

// Datos del cliente (esto debe venir de la base de datos)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Nombre: Francisco Quinteros', 0, 1, 'L');  // Cambiar dinámicamente
$pdf->Cell(0, 6, 'Cedula/RUC: 0302263884', 0, 1, 'L');  // Cambiar dinámicamente
$pdf->Cell(0, 6, 'Direccion: Calle Vigilio Saquicela T, Azogues, Ecuador', 0, 1, 'L');  // Cambiar dinámicamente
$pdf->Cell(0, 6, 'Telefono: +593 983912622', 0, 1, 'L');  // Cambiar dinámicamente
$pdf->Ln(10); // Salto de línea

// Título de la tabla de productos
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Lista de Productos', 0, 1, 'L');

// Crear tabla de productos
$header = array('#', 'Codigo de Barras', 'Nombre', 'Cantidad', 'Precio Unitario', 'Subtotal');
$widths = array(10, 40, 60, 20, 30, 30);  // Anchuras proporcionales

$pdf->SetFont('Arial', 'B', 9);

// Crear encabezados de la tabla
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
$index = 1;
$total_factura = 0;  // Para calcular el total de la factura

// Insertar filas de productos
while ($prod = mysqli_fetch_assoc($listaproductos)) {
    $cantidad = $prod['Cantidad'];  // Obtener de la base de datos
    $precio_unitario = $prod['Valor_Venta'];  // Obtener de la base de datos
    $subtotal = $cantidad * $precio_unitario;
    $total_factura += $subtotal;

    $pdf->Cell($widths[0], 6, $index, 1);
    $pdf->Cell($widths[1], 6, $prod["Codigo_Barras"], 1);
    $pdf->Cell($widths[2], 6, $prod["Nombre_Producto"], 1);
    $pdf->Cell($widths[3], 6, number_format($cantidad, 0), 1, 0, 'R');
    $pdf->Cell($widths[4], 6, number_format($precio_unitario, 2), 1, 0, 'R');
    $pdf->Cell($widths[5], 6, number_format($subtotal, 2), 1, 0, 'R');
    $pdf->Ln();
    $index++;
}

// Subtotales y Totales
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 6, 'Subtotal', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_factura, 2), 0, 1, 'R');

$iva = $total_factura * 0.12;  // IVA del 12%
$pdf->Cell(130, 6, 'IVA (12%)', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$total_pagar = $total_factura + $iva;
$pdf->Cell(130, 6, 'Total a Pagar', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_pagar, 2), 0, 1, 'R');

// Pie de página
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Forma de Pago: Transferencia Bancaria', 0, 1, 'L');
$pdf->Cell(0, 6, 'Cuenta Bancaria: Banco Pichincha, Cta: 123456789', 0, 1, 'L');
$pdf->Cell(0, 6, 'Nota: Gracias por su compra.', 0, 1, 'L');

// Mostrar el PDF
$pdf->Output();
?>
