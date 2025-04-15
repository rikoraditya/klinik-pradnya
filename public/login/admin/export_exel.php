<?php
// Memasukkan autoload Composer
require '../../../vendor/autoload.php'; // Sesuaikan path jika perlu

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Impor Alignment
use PhpOffice\PhpSpreadsheet\Style\Font; // Impor Font
use PhpOffice\PhpSpreadsheet\Style\Fill; // Impor Fill
use PhpOffice\PhpSpreadsheet\Style\Border; // Impor Border

// Ambil data dari database
require '../../php/functions.php';

$query = "SELECT * FROM pasien"; // Ubah sesuai kebutuhan
$result = mysqli_query($conn, $query);

// Membuat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan teks di bagian atas tabel
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'Rekapitulasi Kunjungan Pasien');

// Styling untuk teks "Rekapitulasi Kunjungan Pasien"
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14); // Ukuran font lebih kecil
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Menambahkan header kolom
$sheet->setCellValue('A2', 'No');
$sheet->setCellValue('B2', 'No Antrian');
$sheet->setCellValue('C2', 'Nama');
$sheet->setCellValue('D2', 'NIK');
$sheet->setCellValue('E2', 'Jenis Kelamin');
$sheet->setCellValue('F2', 'No HP');
$sheet->setCellValue('G2', 'Keluhan');
$sheet->setCellValue('H2', 'Poli Tujuan');
$sheet->setCellValue('I2', 'Tanggal Kunjungan');
$sheet->setCellValue('J2', 'Status Antrian');

// Styling untuk header
$sheet->getStyle('A2:J2')->getFont()->setBold(true)->setSize(10); // Ukuran font header lebih kecil
$sheet->getStyle('A2:J2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4F81BD');
$sheet->getStyle('A2:J2')->getFont()->getColor()->setARGB('FFFFFF');
$sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Menambahkan data pasien ke Excel
$rowNumber = 3; // Mulai di baris ketiga
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $rowNumber - 2);
    $sheet->setCellValueExplicit('B' . $rowNumber, $row['no_antrian'], DataType::TYPE_STRING);
    $sheet->setCellValue('C' . $rowNumber, $row['nama']);
    $sheet->setCellValueExplicit('D' . $rowNumber, $row['nik'], DataType::TYPE_STRING);
    $sheet->setCellValue('E' . $rowNumber, $row['jenis_kelamin']);
    $sheet->setCellValueExplicit('F' . $rowNumber, $row['no_hp'], DataType::TYPE_STRING); // <-- Perbaikan disini
    $sheet->setCellValue('G' . $rowNumber, $row['keluhan']);
    $sheet->setCellValue('H' . $rowNumber, $row['poli_tujuan']);
    $sheet->setCellValue('I' . $rowNumber, $row['tanggal_kunjungan']);
    $sheet->setCellValue('J' . $rowNumber, $row['status_antrian']);
    $rowNumber++;
}

// Styling untuk data
$sheet->getStyle('A3:J' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3:J' . ($rowNumber - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Menyimpan file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_kunjungan_pasien.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
?>