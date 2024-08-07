<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class PHPTest extends TestCase {

  use TestWithCloudyTrait;


  public function testExceptionMessageIsPassedToFailBecause() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $include = __DIR__ . '/../t/InstallTypeCore/tests/exceptions.php';
    $output = $this->execCloudy('. "$PHP_FILE_RUNNER" "' . $include . '"');
    $this->assertMatchesRegularExpression('#An unknown problem occurred.#', $output);
  }

  public function testExceptionWithNoCodeReturns127() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $include = __DIR__ . '/../t/InstallTypeCore/tests/exceptions.php';
    $this->execCloudy('. "$PHP_FILE_RUNNER" "' . $include . '"');
    $this->assertSame(127, $this->getCloudyExitStatus());
  }

  public function dataFortestExitWithFailureProvider() {
    $tests = [];
    $tests[] = [1];
    $tests[] = [3];
    $tests[] = [99];

    return $tests;
  }

  /**
   * @dataProvider dataFortestExitWithFailureProvider
   */
  public function testExitWithFailure(int $exit_code) {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $invoker = __DIR__ . '/../t/InstallTypeCore/tests/fn.exit_with_failure.php';
    $this->execCloudy(sprintf('. "$PHP_FILE_RUNNER" "%s" %s', $invoker, $exit_code));
    $this->assertSame($exit_code, $this->getCloudyExitStatus());
  }

}
