<?php

namespace AKlump\Cloudy\Tests\Integration\TestingTraits;

trait TestWithMultiplePhpVersionsTrait {

  /**
   * @return string
   *   A valid path to any version of PHP 8.
   */
  private function getPathToPhp8(): string {
    $php8 = glob('/Applications/MAMP/bin/php/php8*')[0] ?? '';
    $php8 .= '/bin/php';
    $this->assertNotEmpty($php8);
    $this->assertFileExists($php8);

    return $php8;
  }

}
