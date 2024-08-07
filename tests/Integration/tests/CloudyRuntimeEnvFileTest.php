<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class CloudyRuntimeEnvFileTest extends TestCase {

  use TestWithCloudyTrait;

  public function testRuntimeEnvFileIsDeletedOnExitWithFailure() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $path = $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/fn.cloudy_putenv.php";echo $CLOUDY_RUNTIME_ENV;exit_with_failure');
    $path = explode(PHP_EOL, $path, 2)[0];
    $this->assertMatchesRegularExpression('#\.sh$#', $path);
    $this->assertFileDoesNotExist($path);
  }

  public function testRuntimeEnvFileIsDeletedOnExitWithSuccess() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $path = $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/fn.cloudy_putenv.php";echo $CLOUDY_RUNTIME_ENV;exit_with_success');
    $path = explode(PHP_EOL, $path, 2)[0];
    $this->assertMatchesRegularExpression('#\.sh$#', $path);
    $this->assertFileDoesNotExist($path);
  }
}
