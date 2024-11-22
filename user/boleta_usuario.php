<?php
session_start();
require '../libs/fpdf186/fpdf.php';
require 'includes/db_user.php';

if (!isset($_SESSION['cliente_id']) || !isset($_GET['id'])) {
    header("Location: index_user.php");
    exit;
}

$id_orden = $_GET['id'];

// Obtener los detalles de la orden
$query = $pdo->prepare("
    SELECT do.id_producto, p.nombre AS producto, p.precio AS precio_unitario, do.cantidad, do.subtotal, 
           o.fecha, o.total, 
           COALESCE(c.nombre, 'Cliente no especificado') AS cliente
    FROM detalle_ordenes do
    JOIN productos p ON do.id_producto = p.id
    JOIN ordenes o ON do.id_orden = o.id
    LEFT JOIN clientes c ON o.id_cliente = c.id
    WHERE do.id_orden = :id_orden
");
$query->bindParam(':id_orden', $id_orden, PDO::PARAM_INT);
$query->execute();
$detalles = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($detalles)) {
    die("No se encontraron detalles para la orden #$id_orden.");
}

// Configurar la clase PDF
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'El Rincon de Melo - Boleta de Venta', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Información general
$pdf->Cell(0, 10, 'Cliente: ' . utf8_decode($detalles[0]['cliente']), 0, 1);
$pdf->Cell(0, 10, 'Fecha de Compra: ' . $detalles[0]['fecha'], 0, 1);
$pdf->Ln(10);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Producto', 1);
$pdf->Cell(40, 10, 'Precio Unitario (S/)', 1);
$pdf->Cell(20, 10, 'Cantidad', 1);
$pdf->Cell(40, 10, 'Subtotal (S/)', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
foreach ($detalles as $detalle) {
    $pdf->Cell(80, 10, utf8_decode($detalle['producto']), 1);
    $pdf->Cell(40, 10, number_format($detalle['precio_unitario'], 2), 1);
    $pdf->Cell(20, 10, $detalle['cantidad'], 1);
    $pdf->Cell(40, 10, number_format($detalle['subtotal'], 2), 1);
    $pdf->Ln();
}

// Total
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total: S/' . number_format($detalles[0]['total'], 2), 0, 1, 'R');

// Mostrar PDF
$pdf->Output('I', 'boleta_usuario_' . $id_orden . '.pdf');
?>