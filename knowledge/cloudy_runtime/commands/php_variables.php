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

$vars = get_defined_vars();

// These are variables we want, whose keys do not begin with "CLOUDY_".
$special_keys = ['PHP_FILE_RUN_CONTROLLER'];

$cloudy_vars = array_filter($vars, function ($key) {
  return (bool) preg_match('#cloudy_#i', $key);
}, ARRAY_FILTER_USE_KEY);

$docs_vars = array_intersect_key($vars, $cloudy_vars + array_flip($special_keys));

$output = [];
foreach ($docs_vars as $key => $value) {
  if (is_numeric($value)) {
    $value *= 1;
  }
  $output[] = sprintf('/** @var %s $%s */', gettype($value), $key);
}
sort($output);
print implode(PHP_EOL, $output) . PHP_EOL;
