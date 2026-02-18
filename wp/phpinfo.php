<?php

/**
 * Exports current PHP configuration as php.ini-style output.
 * Run this on the server (e.g. https://apflota.pl/wp/export-php-ini.php),
 * then copy the output and save as php.ini for use in Docker.
 */

header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="php.ini.export.txt"');

$all = ini_get_all();

// Group by extension/section (PHP uses "extension" as section in ini_get_all)
$sections = [];
foreach ($all as $key => $data) {
  $value = $data['local_value'];
  if (is_bool($value)) {
    $value = $value ? 'On' : 'Off';
  } elseif (is_array($value)) {
    $value = implode(',', $value);
  }
  $sections['PHP'][$key] = $value;
}

$out = "; Exported PHP configuration\n";
$out .= "; Generated: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($sections as $section => $options) {
  $out .= "[$section]\n";
  foreach ($options as $k => $v) {
    $v = str_replace(["\r", "\n"], ['', ' '], (string) $v);
    $out .= "$k = \"$v\"\n";
  }
  $out .= "\n";
}

echo $out;
