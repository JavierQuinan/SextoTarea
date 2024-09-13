<?php
// Mostrar errores para depuración (solo en desarrollo, quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegurarse de que no se está enviando ninguna salida antes de las cabeceras
ob_start();

if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', dirname(__FILE__) . '/font/');
}

// Cargar FPDF
require('fpdf/fpdf.php');
require_once("../models/productos.model.php");
require_once("../config/config.php");

// Crear conexión a la base de datos
$con = new ClaseConectar();
$conexion = $con->ProcedimientoParaConectar();
if (!$conexion) {
    die('Error de conexión a la base de datos');
}

// Crear instancia de la clase Producto
$productos = new Producto($conexion);
$listaproductos = $productos->todos();

// Verifica si hay productos antes de proceder
if (!$listaproductos || mysqli_num_rows($listaproductos) === 0) {
    die('No se encontraron productos para generar el reporte');
}

// Configurar cabeceras para la descarga del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="productos_reporte.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Iniciar FPDF y cabecera
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', 'B', 12);

// Encabezado de la empresa
$pdf->Cell(80);
$pdf->Cell(30, 10, 'Empresa Compu Solutions', 0, 1, 'C');
$pdf->Ln(10);

// Datos del cliente (dummy para prueba)
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(0, 6, 'Nombre: Francisco Quinteros', 0, 1, 'L');
$pdf->Cell(0, 6, 'Cedula/RUC: 0302263884', 0, 1, 'L');
$pdf->Cell(0, 6, 'Direccion: Calle Vigilio Saquicela T, Azogues, Ecuador', 0, 1, 'L');
$pdf->Ln(10);

// Título de la tabla de productos
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Lista de Productos', 0, 1, 'L');

// Crear tabla de productos
$header = array('#', 'Codigo de Barras', 'Nombre', 'Cantidad', 'Precio Unitario', 'Subtotal');
$widths = array(10, 40, 60, 20, 30, 30);

$pdf->SetFont('Helvetica', 'B', 9);
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 9);
$index = 1;
$total_factura = 0;

// Generar filas de productos
while ($prod = mysqli_fetch_assoc($listaproductos)) {
    $cantidad = $prod['Cantidad'];
    $precio_unitario = $prod['Valor_Venta'];
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

// Subtotales y totales
$pdf->Ln(10);
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(130, 6, 'Subtotal', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_factura, 2), 0, 1, 'R');

$iva = $total_factura * 0.12;
$pdf->Cell(130, 6, 'IVA (12%)', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$total_pagar = $total_factura + $iva;
$pdf->Cell(130, 6, 'Total a Pagar', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_pagar, 2), 0, 1, 'R');

// Enviar salida del PDF
ob_end_clean(); // Limpiar el buffer de salida antes de enviar el PDF
$pdf->Output('I', 'productos_reporte.pdf');

// Otra manera de generar el FPDF

/*// Mostrar errores para depuración (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegurarse de que no se está enviando ninguna salida antes de las cabeceras
ob_start();

// Cargar FPDF
require('fpdf/fpdf.php');

// Configurar cabeceras para la descarga del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="productos_reporte.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Iniciar FPDF y cabecera
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12); // Usar Arial en lugar de helvetica para evitar problemas

// Encabezado de la empresa
$pdf->Cell(80);
$pdf->Cell(30, 10, 'Empresa Compu Solutions', 0, 1, 'C');
$pdf->Ln(10);

// Datos del cliente (datos ingresados manualmente)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Nombre: Francisco Quinteros', 0, 1, 'L');
$pdf->Cell(0, 6, 'Cedula/RUC: 0302263884', 0, 1, 'L');
$pdf->Cell(0, 6, 'Direccion: Calle Vigilio Saquicela T, Azogues, Ecuador', 0, 1, 'L');
$pdf->Ln(10);

// Título de la tabla de productos
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Lista de Productos', 0, 1, 'L');

// Crear tabla de productos
$header = array('#', 'Codigo de Barras', 'Nombre', 'Cantidad', 'Precio Unitario', 'Subtotal');
$widths = array(10, 40, 60, 20, 30, 30);

$pdf->SetFont('Arial', 'B', 9);
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
$index = 1;
$total_factura = 0;

$productos = [
    ['Codigo_Barras' => '1234567890', 'Nombre_Producto' => 'Producto 1', 'Cantidad' => 2, 'Valor_Venta' => 1000],
    ['Codigo_Barras' => '9876543210', 'Nombre_Producto' => 'Producto 2', 'Cantidad' => 1, 'Valor_Venta' => 500],
    ['Codigo_Barras' => '4567890123', 'Nombre_Producto' => 'Producto 3', 'Cantidad' => 3, 'Valor_Venta' => 1500],
];

foreach ($productos as $prod) {
    $cantidad = $prod['Cantidad'];
    $precio_unitario = $prod['Valor_Venta'];
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

// Subtotales y totales
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 6, 'Subtotal', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_factura, 2), 0, 1, 'R');

$iva = $total_factura * 0.12;
$pdf->Cell(130, 6, 'IVA (15%)', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$total_pagar = $total_factura + $iva;
$pdf->Cell(130, 6, 'Total a Pagar', 0, 0, 'R');
$pdf->Cell(30, 6, '$' . number_format($total_pagar, 2), 0, 1, 'R');

// Limpiar el buffer de salida antes de enviar el PDF
ob_end_clean();
$pdf->Output('I', 'productos_reporte.pdf');
*/
?>




