<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../config/auth.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if user is logged in and is admin
if (!$auth->isAdmin()) {
    header("Location: ../login.php");
    exit;
}

try {
    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    
    // Set document properties
    $spreadsheet->getProperties()
        ->setCreator('SIM-KIP System')
        ->setLastModifiedBy('SIM-KIP System')
        ->setTitle('Data Mahasiswa KIP')
        ->setSubject('Data Mahasiswa KIP Export')
        ->setDescription('Export data mahasiswa dari sistem SIM-KIP')
        ->setKeywords('mahasiswa kip export')
        ->setCategory('Student Data');

    // Add header data
    $sheet = $spreadsheet->getActiveSheet();
    $headers = [
        'NIM', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
        'Agama', 'No KIP', 'Alamat', 'Provinsi', 'No Handphone', 'Email'
    ];
    
    foreach ($headers as $idx => $header) {
        $sheet->setCellValueByColumnAndRow($idx + 1, 1, $header);
    }

    // Fetch and add student data
    $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY `Nomor Induk Mahasiswa (NIM)`");
    $row = 2;
    while ($mahasiswa = $stmt->fetch()) {
        $col = 1;
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Nomor Induk Mahasiswa (NIM)']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Nama Siswa']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Jenis Kelamin']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Tempat Lahir']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Tanggal Lahir']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Agama']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['No KIP']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Alamat']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Provinsi']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['No Handphone']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $mahasiswa['Email']);
        $row++;
    }

    // Auto-size columns
    foreach (range('A', $sheet->getHighestColumn()) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Data_Mahasiswa_KIP_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');

    // Save file to browser
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    die("Error creating export file: " . $e->getMessage());
}
?>
