<?php
require_once '../config/auth.php';
require_once '../vendor/autoload.php';

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

try {    // Define PDF constants if not defined
    if (!defined('PDF_PAGE_ORIENTATION')) {
        define('PDF_PAGE_ORIENTATION', 'P');
    }
    if (!defined('PDF_UNIT')) {
        define('PDF_UNIT', 'mm');
    }
    if (!defined('PDF_PAGE_FORMAT')) {
        define('PDF_PAGE_FORMAT', 'A4');
    }
    
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('SIM-KIP System');
    $pdf->SetAuthor('Admin SIM-KIP');
    $pdf->SetTitle('Laporan Data Mahasiswa KIP');

    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Add page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 11);

    // Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Data Mahasiswa KIP', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 10, 'Tanggal: ' . date('d-m-Y'), 0, 1, 'C');
    $pdf->Ln(10);

    // Get statistics
    $totalStudents = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
    $activeKIP = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE `No KIP` IS NOT NULL")->fetchColumn();

    // Add statistics
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Statistik Umum:', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 10, 'Total Mahasiswa: ' . number_format($totalStudents), 0, 1);
    $pdf->Cell(0, 10, 'Mahasiswa KIP Aktif: ' . number_format($activeKIP), 0, 1);
    $pdf->Cell(0, 10, 'Persentase KIP Aktif: ' . number_format(($activeKIP / $totalStudents) * 100, 1) . '%', 0, 1);
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(30, 7, 'NIM', 1);
    $pdf->Cell(50, 7, 'Nama', 1);
    $pdf->Cell(25, 7, 'Gender', 1);
    $pdf->Cell(40, 7, 'No KIP', 1);
    $pdf->Cell(40, 7, 'Status', 1);
    $pdf->Ln();

    // Table data
    $pdf->SetFont('helvetica', '', 10);
    $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY `Nomor Induk Mahasiswa (NIM)`");
    while ($row = $stmt->fetch()) {
        $pdf->Cell(30, 6, $row['Nomor Induk Mahasiswa (NIM)'], 1);
        $pdf->Cell(50, 6, $row['Nama Siswa'], 1);
        $pdf->Cell(25, 6, $row['Jenis Kelamin'], 1);
        $pdf->Cell(40, 6, $row['No KIP'] ?: '-', 1);
        $pdf->Cell(40, 6, $row['No KIP'] ? 'Aktif' : 'Tidak Aktif', 1);
        $pdf->Ln();
    }

    // Output PDF
    $pdf->Output('Laporan_Mahasiswa_KIP_' . date('Y-m-d_H-i-s') . '.pdf', 'D');

} catch (Exception $e) {
    die("Error creating PDF: " . $e->getMessage());
}
?>
