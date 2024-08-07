<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class CloudyPutEnvTest extends TestCase {

  use TestWithCloudyTrait;

  public function testGetEnvReturnsVariableSetByCloudyPutEnv() {
    $result = $this->execCloudy(sprintf('. "$PHP_FILE_RUNNER" "%s"', __DIR__ . '/../t/InstallTypeCore/tests/getenv.php'));
    $this->assertSame("Evergreen trees are nice!", $result);
  }

  public function testControllerReceivesVariablesSetByCloudyPutEnv() {
    $result = $this->execCloudy('. "$PHP_FILE_RUNNER" $CLOUDY_BASEPATH/tests/fn.cloudy_putenv.php;echo $CLOUDY_FAILURES;echo $putenv_test_value');
    $this->assertSame("Alpha Bravo\nLorem ipsum dolar sit", $result);
  }

  public function testPhpCreateArrayAndBashCanReadIt() {
    $result = $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/fn.cloudy_putenv.php";echo ${NAMES[*]}');
    $this->assertSame('Adam Eve', $result);
  }

  public function testPhpCreateScalarAndBashCanReadIt() {
    $result = $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/fn.cloudy_putenv.php";echo $COLOR');
    $this->assertSame('aqua', $result);
  }

  protected function setUp(): void {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
  }
}
