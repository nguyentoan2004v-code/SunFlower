<?php
$directory = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.(php)$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    'App\Models\NguyenLieu' => 'App\Models\NguyenLieu',
    'App\Models\LichSuKho' => 'App\Models\LichSuKho',
    'App\Models\ChiTietDonHangNguyenLieu' => 'App\Models\ChiTietDonHangNguyenLieu',
    'NguyenLieu::' => 'NguyenLieu::',
    'LichSuKho::' => 'LichSuKho::',
    'OrderItemNguyenLieu::' => 'ChiTietDonHangNguyenLieu::',
    '->nguyenLieus' => '->nguyenLieus',
    '->nguyenLieus()' => '->nguyenLieus()',
    '->lichSuKhos' => '->lichSuKhos',
    '->lichSuKhos()' => '->lichSuKhos()',
    '->chiTietDonHangNguyenLieus' => '->chiTietDonHangNguyenLieus',
    '->chiTietDonHangNguyenLieus()' => '->chiTietDonHangNguyenLieus()',
    '->tonkho_thucte' => '->tonkho_thucte',
    '->tonkho_datruoc' => '->tonkho_datruoc',
    '->tonkho_toithieu' => '->tonkho_toithieu',
    '->gia_von' => '->gia_von',
    'id_nguyen_lieu' => 'id_nguyen_lieu',
    'id_chitiet_donhang' => 'id_chitiet_donhang',
    'id_nhacungcap' => 'id_nhacungcap',
    'tongtien' => 'tongtien',
    'id_phieu_nhap' => 'id_phieu_nhap',
    'chi_tiet_id_phieu_nhap' => 'id_chitiet_phieu_nhap',
    'soluong_bandau' => 'soluong_bandau',
    'soluong_hientai' => 'soluong_hientai',
    'dongia' => 'dongia',
    'hsd' => 'hsd',
    'malo' => 'malo',
    'available_stock' => 'available_stock', // Keep available_stock for now, or change to tonkho_khadung
];

foreach ($regex as $file) {
    $filepath = $file[0];
    
    // Skip vendor and storage and bootstrap and node_modules
    if (strpos($filepath, 'vendor') !== false || strpos($filepath, 'storage') !== false || strpos($filepath, 'bootstrap') !== false || strpos($filepath, 'node_modules') !== false) {
        continue;
    }

    // Skip the migration file we just created so we don't mess up its string literals
    if (strpos($filepath, 'rename_bom_tables_and_columns_to_vietnamese.php') !== false) {
        continue;
    }

    $content = file_get_contents($filepath);
    $originalContent = $content;

    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($filepath, $content);
        echo "Updated: $filepath\n";
    }
}
echo "Done replacing safe strings.\n";
