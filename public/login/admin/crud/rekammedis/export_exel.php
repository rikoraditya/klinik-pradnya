<?php
require '../../../../../vendor/autoload.php';
require '../../../../php/functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Ambil data gabungan dari tabel-tabel terkait
$query = "
SELECT 
    rekam_medis.no_rm,
    pasien.nama,
    pasien.nik,
    pasien.jenis_kelamin,
    pasien.no_hp,
    pasien.tempat_lahir,
    pasien.tanggal_lahir,
    pasien.alamat,
    kunjungan.tanggal_kunjungan,
    kunjungan.keluhan,
    kunjungan.poli_tujuan,
    kunjungan.jenis_pasien,
    dokter.nama AS nama_dokter,
    kunjungan.nik_bpjs,
    kunjungan.denyut_nadi,
    kunjungan.laju_pernapasan,
    GROUP_CONCAT(CONCAT(obat.nama_obat, ' (', kunjungan_obat.jumlah, ') (', kunjungan_obat.dosis, ')') SEPARATOR ', ') AS obat,
    kunjungan.diagnosa
FROM rekam_medis
JOIN pasien ON rekam_medis.nik = pasien.nik
JOIN kunjungan ON rekam_medis.no_rm = kunjungan.no_rm
LEFT JOIN kunjungan_obat ON kunjungan.id = kunjungan_obat.id_kunjungan
LEFT JOIN obat ON kunjungan_obat.kode_obat = obat.kode_obat
LEFT JOIN dokter ON kunjungan.dokter_id = dokter.id
GROUP BY kunjungan.id
ORDER BY kunjungan.tanggal_kunjungan DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn) . "\nQuery: " . $query);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->mergeCells('A1:S1');
$sheet->setCellValue('A1', 'Rekapitulasi Rekam Medis Pasien');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
}

$sheet->getStyle('A2:S2')->getFont()->setBold(true);
$sheet->getStyle('A2:S2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('4F81BD');
$sheet->getStyle('A2:S2')->getFont()->getColor()->setARGB('FFFFFF');
$sheet->getStyle('A2:S2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
    $sheet->setCellValue('N' . $rowNumber, $row['nama_dokter']);
    $sheet->setCellValueExplicit('O' . $rowNumber, $row['nik_bpjs'], DataType::TYPE_STRING);
    $sheet->setCellValue('P' . $rowNumber, $row['denyut_nadi']);
    $sheet->setCellValue('Q' . $rowNumber, $row['laju_pernapasan']);
    $sheet->setCellValue('R' . $rowNumber, $row['obat']);
    $sheet->setCellValue('S' . $rowNumber, $row['diagnosa']);
    $rowNumber++;
}

$sheet->getStyle('A3:S' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3:S' . ($rowNumber - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="rekap_rekam_medis.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
