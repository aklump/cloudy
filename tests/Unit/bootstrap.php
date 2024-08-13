<?php

/**
 * @file Boostrap for unit tests.
 */

define('CLOUDY_CORE_DIR', realpath(__DIR__ . '/../../dist/'));
require_once CLOUDY_CORE_DIR . '/../vendor/autoload.php';

const CLOUDY_PACKAGE_ID = 'cloudy_unit_tests';
const CLOUDY_BASEPATH = __DIR__;
