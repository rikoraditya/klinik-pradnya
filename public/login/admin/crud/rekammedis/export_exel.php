<?php
// Memasukkan autoload Composer
require '../../../../../vendor/autoload.php'; // Sesuaikan path jika perlu

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Impor Alignment
use PhpOffice\PhpSpreadsheet\Style\Font; // Impor Font
use PhpOffice\PhpSpreadsheet\Style\Fill; // Impor Fill
use PhpOffice\PhpSpreadsheet\Style\Border; // Impor Border

// Ambil data dari database
require '../../../../php/functions.php';

$query = "SELECT * FROM rekam_medis";
$result = mysqli_query($conn, $query);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan teks di bagian atas tabel
$sheet->mergeCells('A1:S1'); // Sesuaikan dengan kolom yang tersisa setelah menghapus ID
$sheet->setCellValue('A1', 'Rekapitulasi Rekam Medis Pasien');

// Styling untuk teks "Rekapitulasi Rekam Medis Pasien"
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Header kolom (tanpa ID)
$headers = [
    'A2' => 'No',
    'B2' => 'No RM',
    'C2' => 'Nama',
    'D2' => 'NIK',
    'E2' => 'Jenis Kelamin',
    'F2' => 'No HP',
    'G2' => 'Tempat Lahir',
    'H2' => 'Tanggal Lahir',
    'I2' => 'Alamat',
    'J2' => 'Tanggal Kunjungan',
    'K2' => 'Keluhan',
    'L2' => 'Poli Tujuan',
    'M2' => 'Jenis Pasien',
    'N2' => 'Dokter',
    'O2' => 'NIK BPJS',
    'P2' => 'Denyut Nadi',
    'Q2' => 'Laju Pernapasan',
    'R2' => 'Obat',
    'S2' => 'Diagnosa'
];

// Menambahkan header kolom ke dalam worksheet
foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

// Styling untuk header
$sheet->getStyle('A2:S2')->getFont()->setBold(true);
$sheet->getStyle('A2:S2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4F81BD');
$sheet->getStyle('A2:S2')->getFont()->getColor()->setARGB('FFFFFF');
$sheet->getStyle('A2:S2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Isi data (tanpa kolom ID)
$rowNumber = 3;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $rowNumber - 2);
    $sheet->setCellValueExplicit('B' . $rowNumber, $row['no_rm'], DataType::TYPE_STRING);
    $sheet->setCellValue('C' . $rowNumber, $row['nama']);
    $sheet->setCellValueExplicit('D' . $rowNumber, $row['nik'], DataType::TYPE_STRING);
    $sheet->setCellValue('E' . $rowNumber, $row['jenis_kelamin']);
    $sheet->setCellValueExplicit('F' . $rowNumber, $row['no_hp'], DataType::TYPE_STRING);
    $sheet->setCellValue('G' . $rowNumber, $row['tempat_lahir']);
    $sheet->setCellValue('H' . $rowNumber, $row['tanggal_lahir']);
    $sheet->setCellValue('I' . $rowNumber, $row['alamat']);
    $sheet->setCellValue('J' . $rowNumber, $row['tanggal_kunjungan']);
    $sheet->setCellValue('K' . $rowNumber, $row['keluhan']);
    $sheet->setCellValue('L' . $rowNumber, $row['poli_tujuan']);
    $sheet->setCellValue('M' . $rowNumber, $row['jenis_pasien']);
    $sheet->setCellValue('N' . $rowNumber, $row['dokter']);
    $sheet->setCellValueExplicit('O' . $rowNumber, $row['nik_bpjs'], DataType::TYPE_STRING);
    $sheet->setCellValue('P' . $rowNumber, $row['denyut_nadi']);
    $sheet->setCellValue('Q' . $rowNumber, $row['laju_pernapasan']);
    $sheet->setCellValue('R' . $rowNumber, $row['obat']);
    $sheet->setCellValue('S' . $rowNumber, $row['diagnosa']);
    $rowNumber++;
}

// Styling untuk data
$sheet->getStyle('A3:S' . $rowNumber)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Menambahkan border ke semua sel data
$sheet->getStyle('A3:S' . ($rowNumber - 1))
    ->getBorders()
    ->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

// Output file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rekam_medis.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
?>