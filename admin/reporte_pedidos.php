<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../libs/fpdf186/fpdf.php';
require 'includes/db_admin.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login_admin.php");
    exit;
}

class PDF extends FPDF
{
    // Cabecera del reporte
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'El Rincón de Melo'), 0, 1, 'C'); // Título principal
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Reporte de Pedidos'), 0, 1, 'C'); // Subtítulo
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Generado por: ') . htmlspecialchars($_SESSION['admin_username']), 0, 1, 'L'); // Usuario
        $this->Cell(0, 10, 'Fecha: ' . date('Y-m-d H:i:s'), 0, 1, 'L'); // Fecha
        $this->Ln(10); // Salto de línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Crear el PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Obtener datos de pedidos
$query = $pdo->query("
    SELECT o.id, o.fecha, o.total, o.estado, 
           COALESCE(c.nombre, 'Cliente no especificado') AS cliente
    FROM ordenes o
    LEFT JOIN clientes c ON o.id_cliente = c.id
    ORDER BY o.fecha DESC
");
$pedidos = $query->fetchAll(PDO::FETCH_ASSOC);

// Verificar si hay pedidos
if (!empty($pedidos)) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(20, 10, 'ID', 1);
    $pdf->Cell(50, 10, 'Cliente', 1);
    $pdf->Cell(40, 10, 'Fecha', 1);
    $pdf->Cell(30, 10, 'Total (S/)', 1);
    $pdf->Cell(50, 10, 'Estado', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    foreach ($pedidos as $pedido) {
        $pdf->Cell(20, 10, $pedido['id'], 1);
        $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $pedido['cliente']), 1);
        $pdf->Cell(40, 10, $pedido['fecha'], 1);
        $pdf->Cell(30, 10, number_format($pedido['total'], 2), 1);
        $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $pedido['estado']), 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'No hay pedidos disponibles.'), 1, 1, 'C');
}

// Salida del archivo PDF
$pdf->Output('I', 'reporte_pedidos.pdf');
?>