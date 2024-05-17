<?php
date_default_timezone_set('Europe/Madrid');

// Load the configuration file
$config = parse_ini_file('config.ini');

// Get the current date
$date = date('Y-m-d');

// Replace {date} in the URL with the current date
$url = str_replace('{date}', $date, $config['url']);

// Get values from the configuration file
$directory = $config['directory'];
$xls = $config['xls'];
$filepath = "{$directory}/{$xls}";

// Create the directory if it doesn't exist
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

// Remove previous files in the directory
array_map('unlink', glob("{$directory}/*"));

// Download and save the content to the specified file
file_put_contents($filepath, file_get_contents($url));
?>
