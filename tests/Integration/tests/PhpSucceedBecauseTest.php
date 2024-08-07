<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers succeed_because()
 */
class PhpSucceedBecauseTest extends TestCase {

  use TestWithCloudyTrait;

  public function testPhpSuccessBecauseEchosMessage() {
    $output = $this->getCloudyOutput();
    $this->assertMatchesRegularExpression('#d is for diamond#', $output);
    $this->assertMatchesRegularExpression('#e is for elephant#', $output);
  }

  public function testPhpSuccessBecauseExitsWithZero() {
    $this->assertSame(0, $this->getCloudyExitStatus());
  }

  protected function setUp(): void {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" $CLOUDY_BASEPATH/tests/fn.succeed_because.php;has_failed || exit_with_success');
  }
}
