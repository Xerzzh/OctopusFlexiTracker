<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set('Europe/Madrid');

// Load the configuration file
$config = parse_ini_file('config.ini');

$directory = $config['directory'];
$filename_xls = $config['xls'];
$filename_json = $config['json'];
$filepath_xls = "{$directory}/{$filename_xls}";
$filepath_json = "{$directory}/{$filename_json}";

$reader = IOFactory::createReader('Xls');
$spreadsheet = $reader->load($filepath_xls);
$sheet = $spreadsheet->getSheetByName('Tabla de Datos PCB');

$fields = json_decode($config['fields'], true);

$results = [];
foreach ($fields as $field => $range) {
    $values = $sheet->rangeToArray($range);
    foreach ($values as $key => $value) {
        $hour = intval($value[0]) - 1;
        $hour = strval($hour);
        $results[$key][$field] = $hour;
        if ($field !== 'Hour') {
            $results[$key][$field] = $value[0];
        }
    }
}

// Get current hour
$currentHour = intval(date('H'));

// Find corresponding node
$selectedNode = null;
foreach ($results as $result) {
    if (intval($result['Hour']) === $currentHour) {
        $selectedNode = $result;
        break;
    }
}

if ($selectedNode !== null) {
    $PRedp = $config['PRedp'];
    $CG = $config['CG'] * 1000; // €/MWh
    $M = $config['M'];

    $total_MWh = array_sum([$selectedNode['OMIEp'], $selectedNode['CMp'], $selectedNode['RCp'], $PRedp, $CG, $M, $selectedNode['ATR']]);
    $selectedNode['MWh'] = number_format($total_MWh, 4);
    $selectedNode['KWh'] = number_format($total_MWh / 1000, 4);
    $selectedNode['Total'] = number_format($selectedNode['KWh'] * (1 + 0.038) * (1 + 0.21), 4);

    $json_results = json_encode([$selectedNode], JSON_PRETTY_PRINT);
    file_put_contents($filepath_json, $json_results);
} else {
    // If no node found for current hour, create an empty JSON file
    file_put_contents($filepath_json, json_encode([]));
}
?>
