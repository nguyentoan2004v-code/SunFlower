<?php
$directory = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.(php)$/i', RecursiveRegexIterator::GET_MATCH);

// These regexes will replace ->column or ['column'] when they are likely BOM properties.
$replacements = [
    // name -> ten_nl
    '/(nguyenlieu|nguyenlieus|m|nl)->name\b/' => '$1->ten_nl',
    "/(nguyenlieu|nguyenlieus|m|nl)\['name'\]/" => "$1['ten_nl']",
    '/(nguyenlieu|nguyenlieus|m|nl)\["name"\]/' => '$1["ten_nl"]',
    
    // unit -> dvt
    '/(nguyenlieu|nguyenlieus|m|nl)->unit\b/' => '$1->dvt',
    "/(nguyenlieu|nguyenlieus|m|nl)\['unit'\]/" => "$1['dvt']",
    '/(nguyenlieu|nguyenlieus|m|nl)\["unit"\]/' => '$1["dvt"]',

    // quantity -> soluong or dinh_muc or soluong_dung
    // For logs
    '/(log|logs)->quantity\b/' => '$1->soluong',
    // For chi_tiet_phieu_nhap
    '/(ct|chitiet)->quantity\b/' => '$1->soluong',
    // For lo_nguyen_lieu
    '/(lo|lot|lots)->initial_quantity\b/' => '$1->soluong_bandau',
    '/(lo|lot|lots)->current_quantity\b/' => '$1->soluong_hientai',
    // For order_item_materials
    '/(oim)->quantity\b/' => '$1->soluong_dung',
    // For product_material pivot
    '/(pivot)->quantity\b/' => '$1->dinh_muc',

    // note -> ghichu
    '/(log|logs|phieu)->note\b/' => '$1->ghichu',

    // type -> loai_gd
    '/(log|logs)->type\b/' => '$1->loai_gd',

    // status -> trangthai
    '/(phieu|lo|lot)->status\b/' => '$1->trangthai',

    // cost_price -> gia_von
    '/(nguyenlieu|nguyenlieus|m|nl)->cost_price\b/' => '$1->gia_von',
    "/(nguyenlieu|nguyenlieus|m|nl)\['cost_price'\]/" => "$1['gia_von']",
    '/(nguyenlieu|nguyenlieus|m|nl)\["cost_price"\]/' => '$1["gia_von"]',
    
    // lot_number -> malo
    '/(ct|chitiet|lo|lot)->lot_number\b/' => '$1->malo',

    // unit_price -> dongia
    '/(ct|chitiet|lo|lot)->unit_price\b/' => '$1->dongia',

    // expiry_date -> hsd
    '/(ct|chitiet|lo|lot)->expiry_date\b/' => '$1->hsd',
];

foreach ($regex as $file) {
    $filepath = $file[0];
    if (strpos($filepath, 'vendor') !== false || strpos($filepath, 'storage') !== false || strpos($filepath, 'bootstrap') !== false || strpos($filepath, 'node_modules') !== false) {
        continue;
    }
    if (strpos($filepath, 'rename_bom_tables_and_columns_to_vietnamese.php') !== false) {
        continue;
    }

    $content = file_get_contents($filepath);
    $originalContent = $content;

    foreach ($replacements as $pattern => $replace) {
        $content = preg_replace($pattern, $replace, $content);
    }

    if ($content !== $originalContent) {
        file_put_contents($filepath, $content);
        echo "Regex Updated: $filepath\n";
    }
}
echo "Done regex replacing.\n";
