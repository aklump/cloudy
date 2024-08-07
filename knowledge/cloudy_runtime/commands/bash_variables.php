<?php

/** @var array $CLOUDY_FAILURES */
/** @var array $CLOUDY_SUCCESSES */
/** @var integer $CLOUDY_EXIT_STATUS */
/** @var string $CLOUDY_BASEPATH */
/** @var string $CLOUDY_CACHE_DIR */
/** @var string $CLOUDY_COMPOSER_VENDOR */
/** @var string $CLOUDY_CONFIG_JSON */
/** @var string $CLOUDY_CORE_DIR */
/** @var string $CLOUDY_PACKAGE_CONFIG */
/** @var string $CLOUDY_PACKAGE_CONTROLLER */
/** @var string $CLOUDY_RUNTIME_ENV */
/** @var string $CLOUDY_RUNTIME_UUID */
/** @var string $CLOUDY_START_DIR */
/** @var string $PHP_FILE_RUN_CONTROLLER */

$output = [];
exec('set | grep CLOUDY_', $output);

$vars = [];
foreach ($output as $line) {
  list($name, $value) = explode('=', $line, 2);
  if (is_numeric($value)) {
    $value *= 1;
  }
  $vars[$name] = $value;
}

$hidden_var_names = [
  'BASH_EXECUTION_STRING',
  'CLOUDY_FAILURES__SERIALIZED_ARRAY',
  'CLOUDY_SUCCESSES__SERIALIZED_ARRAY',
];

$vars = array_diff_key($vars, array_flip($hidden_var_names));

$output = [];
foreach ($vars as $key => $value) {
  $output[] = sprintf('# @var %s $%s', gettype($value), $key);
}
sort($output);
print implode(PHP_EOL, $output) . PHP_EOL;
