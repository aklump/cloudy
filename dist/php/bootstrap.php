<?php

/**
 * @file
 * Bootstrap for all PHP files.
 */

/** @var $read_only_vars */

require_once $read_only_vars['CLOUDY_COMPOSER_VENDOR'] . '/autoload.php';
require_once __DIR__ . '/cloudy.api.php';
require_once __DIR__ . '/error_handler.php';
require_once __DIR__ . '/cloudy.functions.php';

/**
 * Root directory of the Cloudy instance script.
 */
$_constants = [
  'ROOT',
  'CLOUDY_CORE_DIR',
  'CLOUDY_BASEPATH',
  'CLOUDY_COMPOSER_VENDOR',
];
foreach ($_constants as $_constant) {
  define($_constant, getenv($_constant));
  if (empty($_constant)) {
    throw new RuntimeException(sprintf('Environment var "%s" cannot be empty.'), $_constant);
  }
}

/** @var \Composer\Autoload\ClassLoader $class_loader */
$class_loader = require_once CLOUDY_COMPOSER_VENDOR . '/autoload.php';
