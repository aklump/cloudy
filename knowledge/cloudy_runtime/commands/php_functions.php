<?php

/** @var array $CLOUDY_FAILURES */
/** @var array $CLOUDY_SUCCESSES */
/** @var integer $CLOUDY_EXIT_STATUS */
/** @var string $CLOUDY_BASEPATH */
/** @var string $CLOUDY_CACHE_DIR */
/** @var string $CLOUDY_COMPOSER_VENDOR */
/** @var string $CLOUDY_CORE_DIR */
/** @var string $CLOUDY_PACKAGE_CONFIG */
/** @var string $CLOUDY_PACKAGE_CONTROLLER */
/** @var string $CLOUDY_RUNTIME_ENV */
/** @var string $CLOUDY_RUNTIME_UUID */
/** @var string $PHP_FILE_RUN_CONTROLLER */

$templates = [
  'php/cloudy.api.php',
  'php/cloudy.functions.php',
];

$functions_by_files = [];
foreach ($templates as $template) {
  $template = file_get_contents("$CLOUDY_CORE_DIR/$template");
  preg_match_all('#function\s*([^(\s]+)\s*\(#', $template, $matches);
  $functions_by_files = array_merge($functions_by_files, $matches[1]);
}

// Remove _cloudy*
$functions_by_files = array_filter($functions_by_files, function ($name) {
  return strpos($name, '_cloudy') !== 0;
});
$functions_by_files = array_unique($functions_by_files);

$regex = implode('|', $functions_by_files);

$functions = get_defined_functions()['user'] ?? [];

$cloudy_functions = array_filter($functions, function ($function) use ($regex) {
  return (bool) preg_match('#^(' . $regex . ')$#', $function);
});
$cloudy_functions = array_values($cloudy_functions);
sort($cloudy_functions);
$cloudy_functions = implode("\n", $cloudy_functions);
echo $cloudy_functions;
