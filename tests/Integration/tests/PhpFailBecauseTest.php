<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers fail_because()
 */
class PhpFailBecauseTest extends TestCase {

  use TestWithCloudyTrait;

  public function testFailBecauseSetsNonZeroExitCode() {
    $this->assertSame(1, $this->getCloudyExitStatus());
  }

  public function testFailBecauseEchosMessages() {
    $output = $this->getCloudyOutput();
    $this->assertMatchesRegularExpression('#a is for apple#', $output);
    $this->assertMatchesRegularExpression('#b is for banana#', $output);
    $this->assertMatchesRegularExpression('#c is for chocolate#', $output);
  }

  protected function setUp(): void {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" $CLOUDY_BASEPATH/tests/fn.fail_because.php;has_failed && exit_with_failure');
  }
}
