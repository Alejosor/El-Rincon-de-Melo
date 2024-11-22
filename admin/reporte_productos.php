<?php
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
        $this->Cell(0, 10, utf8_decode('El Rincón de Melo'), 0, 1, 'C'); // Título principal
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, utf8_decode('Reporte de Productos'), 0, 1, 'C'); // Subtítulo
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 8, utf8_decode('Generado por: ') . utf8_decode($_SESSION['admin_username']), 0, 1, 'L');
        $this->Cell(0, 8, 'Fecha: ' . date('Y-m-d H:i:s'), 0, 1, 'L');
        $this->Ln(5); // Salto de línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Ajustar ancho de columna
    function ImprovedTable($header, $data)
    {
        // Anchos de las columnas
        $w = [10, 70, 80, 20, 20, 30]; // Aumenté el ancho de "Nombre" y ajusté otros
        // Encabezados
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 10, utf8_decode($header[$i]), 1, 0, 'C');
        }
        $this->Ln();

        // Filas
        foreach ($data as $row) {
            $this->Cell($w[0], 8, $row['id'], 1);
            $this->Cell($w[1], 8, utf8_decode($row['nombre']), 1);
            $this->Cell($w[2], 8, utf8_decode($row['descripcion']), 1);
            $this->Cell($w[3], 8, number_format($row['precio'], 2), 1, 0, 'R');
            $this->Cell($w[4], 8, $row['stock'], 1, 0, 'R');
            $this->Cell($w[5], 8, utf8_decode($row['estado']), 1, 0, 'C');
            $this->Ln();
        }
    }
}

// Crear el PDF con orientación horizontal (Landscape)
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Obtener datos de productos
$query = $pdo->query("
    SELECT id, nombre, descripcion, precio, stock, 
           CASE activo WHEN 1 THEN 'Activo' ELSE 'Deshabilitado' END AS estado 
    FROM productos
    ORDER BY estado DESC, nombre ASC
");
$productos = $query->fetchAll(PDO::FETCH_ASSOC);

// Encabezados
$header = ['ID', 'Nombre', 'Descripción', 'Precio', 'Stock', 'Estado'];

// Verificar si hay productos
if (!empty($productos)) {
    $pdf->ImprovedTable($header, $productos);
} else {
    $pdf->Cell(0, 10, utf8_decode('No hay productos disponibles.'), 1, 1, 'C');
}

// Salida del archivo PDF
$pdf->Output('I', 'reporte_productos.pdf');
?>