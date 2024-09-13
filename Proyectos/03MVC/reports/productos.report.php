<?php
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', dirname(__FILE__) . '/font/');
}

require('fpdf/fpdf.php');
require_once("../models/productos.model.php");

// Definir rutas
$logo_local = '../public/images/image.png';  // Ruta local
$logo_url = 'https://www.uniandes.edu.ec/wp-content/uploads/2024/07/2-headerweb-home-2.png';  // URL externa

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Encabezado de la empresa
$pdf->Image($logo_local, 10, 6, 30);  // Logo de la empresa local
$pdf->Cell(80); // Moverse a la derecha
$pdf->Cell(30, 10, 'Empresa XYZ', 0, 1, 'C');  // Nombre de la empresa
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80);
$pdf->Cell(30, 5, 'RUC: 1234567890', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Direccion: Calle Falsas 123, Quito, Ecuador', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Telefono: +593 999 999 999', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'Email: info@empresa.com', 0, 1, 'C');
$pdf->Ln(10); // Salto de línea

// Datos del cliente
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Nombre: Juan Perez', 0, 1, 'L');
$pdf->Cell(0, 6, 'Cedula/RUC: 1234567890', 0, 1, 'L');
$pdf->Cell(0, 6, 'Direccion: Calle Ejemplo 456, Guayaquil, Ecuador', 0, 1, 'L');
$pdf->Cell(0, 6, 'Telefono: +593 987 654 321', 0, 1, 'L');
$pdf->Ln(10); // Salto de línea

// Título de la tabla de productos
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Lista de Productos', 0, 1, 'L');

// Datos de productos
$productos = new Producto();
$listaproductos = $productos->todos();

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

// Insertar filas de productos
while ($prod = mysqli_fetch_assoc($listaproductos)) {
    $cantidad = 2;  // Ejemplo, puedes obtenerlo de la base de datos
    $precio_unitario = 1000;  // Ejemplo
    $subtotal = $cantidad * $precio_unitario;

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
$pdf->Cell(30, 6, '$6000.00', 0, 1, 'R');

$pdf->Cell(130, 6, 'IVA (12%)', 0, 0, 'R');
$pdf->Cell(30, 6, '$720.00', 0, 1, 'R');

$pdf->Cell(130, 6, 'Total a Pagar', 0, 0, 'R');
$pdf->Cell(30, 6, '$6720.00', 0, 1, 'R');

// Pie de página
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Forma de Pago: Transferencia Bancaria', 0, 1, 'L');
$pdf->Cell(0, 6, 'Cuenta Bancaria: Banco Pichincha, Cta: 123456789', 0, 1, 'L');
$pdf->Cell(0, 6, 'Nota: Gracias por su compra.', 0, 1, 'L');

// Mostrar el PDF
$pdf->Output();
?>
