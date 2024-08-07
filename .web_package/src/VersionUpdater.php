<?php

namespace AKlump\WebPackage\User;

class VersionUpdater {

  const STATUS_SAME = 0;

  const STATUS_UPDATED = 1;

  const STATUS_FAILED = 2;

  /**
   * @var string
   */
  private $contents;

  /**
   * @var string
   */
  private $version;

  public function __construct(string $version) {
    $this->version = $version;
  }

  public function __invoke(string &$contents): int {
    $this->contents = &$contents;
    $current = $this->getCurrent();
    if (empty($current)) {
      $find = sprintf('/^(#!.+?%s+)/', PHP_EOL);
      $replace = sprintf('\0# Cloudy version %s%s', $this->version, PHP_EOL . PHP_EOL);
      $this->contents = preg_replace($find, $replace, $this->contents);
    }
    elseif ($current === $this->version) {
      return self::STATUS_SAME;
    }
    else {
      $find = '#Cloudy version [\d.]+#i';
      $replace = sprintf('Cloudy version %s', $this->version);
      $this->contents = preg_replace($find, $replace, $this->contents);
    }

    // Now read the version after replacement to determine if the replacement
    // worked as expected.
    if ($this->getCurrent() === $this->version) {
      return self::STATUS_UPDATED;
    }

    return self::STATUS_FAILED;
  }

  private function getCurrent(): string {
    $regex = '/^# Cloudy version ([\d.]+)/m';
    preg_match($regex, $this->contents, $matches);

    return $matches[1] ?? '';
  }

}



//$stamped_version = get_version($original);
//if ($new_version === $stamped_version) {
//  return;
//}
//

//if ($updated === $original) {

//}
//if ($updated === $original) {
//
//
//  file_put_contents($path, $updated);
//
