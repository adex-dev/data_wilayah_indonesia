<?php
include("./config.php");
$mysqli = $conn;
// Check koneksi
if ($mysqli->connect_errno) {
    die('Koneksi ke database gagal: ' . $mysqli->connect_error);
}

$url = "./propinsi.json";  // Ganti dengan URL API yang sesuai
$response = file_get_contents($url);

if ($response === false) {
    die('Gagal mengambil data dari API.');
}
$data = json_decode($response, true);


// Persiapkan statement SQL
$stmt = $mysqli->prepare("INSERT INTO provinsi (id_provinsi, nama) VALUES (?, ?)");
$stmt->bind_param("is", $id_provinsi, $nama);

$stk = $mysqli->prepare("INSERT INTO region (id, id_provinsi, name_region) VALUES (?, ?, ?)");
$stk->bind_param("iis", $idkab, $id_provinsi, $namakab);

$stkc = $mysqli->prepare("INSERT INTO sub_region (id, id_region, id_provinsi, name_subregion) VALUES (?, ?, ?, ?)");
$stkc->bind_param("iiis", $idkec, $idkab, $id_provinsi, $namasub);

$stkl = $mysqli->prepare("INSERT INTO village (id, id_subregion, id_region, id_provinsi, name_village) VALUES (?, ?, ?, ?, ?)");
$stkl->bind_param("iiiis", $idkel, $idkec, $idkab, $id_provinsi, $namakel);


foreach ($data as $value) {
    $id_provinsi = $value['id'];
    $nama = $value['nama'];
    $kabupaten = "./kabupaten/$id_provinsi.json";
    foreach (json_decode(file_get_contents($kabupaten), true) as  $vk) {
        $idkab = $vk['id'];
        $namakab = $vk['nama'];
        $stk->execute();
        $kecamatan = "./kecamatan/$idkab.json";
        foreach (json_decode(file_get_contents($kecamatan), true) as  $vc) {
            $idkec = $vc['id'];
            $namasub = $vc['nama'];
            $stkc->execute();
            $kelurahan = "./kelurahan/$idkec.json";
            foreach (json_decode(file_get_contents($kelurahan), true) as  $vl) {
                $idkel = $vl['id'];
                $namakel = $vl['nama'];
                $stkl->execute();
            }
        }
    }

    // Eksekusi statement untuk setiap baris data
    $stmt->execute();
}
// Tutup statement dan koneksi
$stmt->close();
$stk->close();
$stkc->close();
$stkl->close();
$mysqli->close();
echo "Sukses";
