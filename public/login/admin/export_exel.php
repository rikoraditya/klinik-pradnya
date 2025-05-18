<?php
require '../../../vendor/autoload.php';
require '../../php/functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Ambil data kunjungan dari antrian dan pasien
$query = "
SELECT 
    a.no_antrian,
    p.nama,
    p.nik,
    p.jenis_kelamin,
    p.no_hp,
    p.tempat_lahir,
    p.tanggal_lahir,
    p.alamat,
    a.poli_tujuan,
    a.tanggal_antrian,
    a.status_antrian
FROM antrian a
JOIN pasien p ON a.pasien_id = p.id
ORDER BY a.tanggal_antrian DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn) . "\nQuery: " . $query);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', 'Rekapitulasi Kunjungan Pasien');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$headers = [
    'A2' => 'No',
    'B2' => 'No Antrian',
    'C2' => 'Nama',
    'D2' => 'NIK',
    'E2' => 'Jenis Kelamin',
    'F2' => 'No HP',
    'G2' => 'Tempat Lahir',
    'H2' => 'Tanggal Lahir',
    'I2' => 'Alamat',
    'J2' => 'Poli Tujuan',
    'K2' => 'Tanggal Kunjungan'
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

$sheet->getStyle('A2:K2')->getFont()->setBold(true);
$sheet->getStyle('A2:K2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4F81BD');
$sheet->getStyle('A2:K2')->getFont()->getColor()->setARGB('FFFFFF');
$sheet->getStyle('A2:K2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rowNumber = 3;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $rowNumber - 2);
    $sheet->setCellValueExplicit('B' . $rowNumber, $row['no_antrian'], DataType::TYPE_STRING);
    $sheet->setCellValue('C' . $rowNumber, $row['nama']);
    $sheet->setCellValueExplicit('D' . $rowNumber, $row['nik'], DataType::TYPE_STRING);
    $sheet->setCellValue('E' . $rowNumber, $row['jenis_kelamin']);
    $sheet->setCellValueExplicit('F' . $rowNumber, $row['no_hp'], DataType::TYPE_STRING);
    $sheet->setCellValue('G' . $rowNumber, $row['tempat_lahir']);
    $sheet->setCellValue('H' . $rowNumber, $row['tanggal_lahir']);
    $sheet->setCellValue('I' . $rowNumber, $row['alamat']);
    $sheet->setCellValue('J' . $rowNumber, $row['poli_tujuan']);
    $sheet->setCellValue('K' . $rowNumber, $row['tanggal_antrian']);
    $rowNumber++;
}

$sheet->getStyle('A3:K' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3:K' . ($rowNumber - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rekap_kunjungan_pasien.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
