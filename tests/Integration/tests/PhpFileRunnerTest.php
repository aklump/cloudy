<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class PhpFileRunnerTest extends TestCase {

  use TestWithCloudyTrait;

  public function testKnowledgeExampleCodeSimplestForm() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/php_file_runner.php" simplest_form');
    $this->assertSame('', $this->getCloudyOutput());
    $this->assertSame(0, $this->getCloudyExitStatus());
  }

  public function testKnowledgeExampleCodeCapturingOutput() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/php_file_runner.php" output');
    $this->assertSame('Green leaves', $this->getCloudyOutput());
    $this->assertSame(0, $this->getCloudyExitStatus());
  }

  public function testKnowledgeExampleCodeExitStatus() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/php_file_runner.php" exit_status');
    $this->assertSame('', $this->getCloudyOutput());
    $this->assertSame(64, $this->getCloudyExitStatus());
  }

  public function testExitCodeWhenNotHasFailed() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('succeed_because foo;echo $CLOUDY_EXIT_STATUS;. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/php_file_runner.php" exit_status; echo $?; echo $CLOUDY_EXIT_STATUS');
    $this->assertSame("0\n64\n64", $this->getCloudyOutput());
  }
  public function testExitCodeWhenAlreadyHasFailed() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('fail_because foo bar --status=32;echo $CLOUDY_EXIT_STATUS;. "$PHP_FILE_RUNNER" "$CLOUDY_BASEPATH/tests/php_file_runner.php" exit_status; echo $?; echo $CLOUDY_EXIT_STATUS');
    $this->assertSame("32\n64\n64", $this->getCloudyOutput());
  }

  public function testPhpFileWithOutputBeforeOpenTagThrows() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $include = __DIR__ . '/../t/InstallTypeCore/tests/bad_format.php';
    $this->execCloudy('. "$PHP_FILE_RUNNER" "' . $include . '"');
    $this->assertSame(124, $this->getCloudyExitStatus());
  }

  public function testNoPhpPathReturns126() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER"');
    $this->assertSame(126, $this->getCloudyExitStatus());
  }

  public function testEmptyPhpPathReturns126() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $this->execCloudy('. "$PHP_FILE_RUNNER" ""');
    $this->assertSame(126, $this->getCloudyExitStatus());
  }

  public function testNonFilePhpPathReturns125() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $include = __DIR__ . '/../t/InstallTypeCore/php/_bogus_not_found.php';
    $this->execCloudy('. "$PHP_FILE_RUNNER" "' . $include . '"');
    $this->assertSame(125, $this->getCloudyExitStatus());
  }
}
