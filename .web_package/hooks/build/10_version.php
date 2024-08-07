<?php

/**
 * @file
 * Stamps the version into the Cloudy file(s).
 */

namespace AKlump\WebPackage\User;

use RuntimeException;

/** @var $filepaths Filepaths of the BASH files to stamp. */
$filepaths = [
  './dist/cloudy.sh',
];

$new_version = $argv[2];
foreach ($filepaths as $path) {
  $contents = file_get_contents($path);
  $updater = new VersionUpdater($new_version);
  $result = $updater($contents);
  if (VersionUpdater::STATUS_UPDATED === $result) {
    if (!file_put_contents($path, $contents)) {
      throw new RuntimeException(sprintf('Failed to save %s', $path));
    }
    echo sprintf('Version updated to %s in %s', $new_version, $path) . PHP_EOL;
  }
  elseif (VersionUpdater::STATUS_FAILED === $result) {
    throw new RuntimeException(sprintf('Failed to update version in %s', $path));
  }
  else {
    echo sprintf('Version correct in %s', $path) . PHP_EOL;
  }
}
