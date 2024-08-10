<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use AKlump\Cloudy\Tests\Unit\TestingTraits\TestWithFilesTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class CloudyLogTest extends TestCase {

  use TestWithCloudyTrait;
  use TestWithFilesTrait;

  public function dataFortestControllerLogWorksAsExpectedProvider() {
    $tests = [];
    $tests[] = ['controller.log', '{ROOT}/controller.log'];
    $tests[] = ['./controller.log', '{ROOT}/controller.log'];
    $absolute = $this->getTestFileFilepath('cloudy_log_test.txt');
    $tests[] = [$absolute, $absolute];

    return $tests;
  }

  /**
   * @dataProvider dataFortestControllerLogWorksAsExpectedProvider
   */
  public function testControllerLogPathsResolveAsExpected(string $CLOUDY_LOG, string $expected_absolute_path) {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeComposer/vendor/aklump/package/config.yml', 'test_runner.controller_log.sh');
    $expected_absolute_path = str_replace('{ROOT}', dirname($this->getCloudyPackageController()), $expected_absolute_path);

    $result = $this->execCloudy('', '', $CLOUDY_LOG);
    $this->assertSame($expected_absolute_path, $result);
  }

  public function dataFortestCLIProvidedRelativeLogPathUsesCloudyBasepathNotControllerDirProvider() {
    $tests = [];
    $tests[] = [
      'cloudy_log_test.log',
      getcwd() . "/cloudy_log_test.log",
    ];
    $tests[] = [
      './cloudy_log_test.log',
      getcwd() . "/cloudy_log_test.log",
    ];
    $absolute = $this->getTestFileFilepath('cloudy_log_test.txt');
    $tests[] = [$absolute, $absolute];

    return $tests;
  }

  /**
   * @dataProvider dataFortestCLIProvidedRelativeLogPathUsesCloudyBasepathNotControllerDirProvider
   */
  public function testCloudyLogPathsResolveAsExpected(string $CLOUDY_LOG, string $expected_absolute_path) {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeComposer/vendor/aklump/package/config.yml', 'test_runner.controller_log.sh');
    $result = $this->execCloudy('', $CLOUDY_LOG, '');
    $this->assertSame($expected_absolute_path, $result);
  }

  public function testCloudyLogAbsolutePathIsPrintedInErrorOutput() {
    $CLOUDY_LOG = 'cloudy_log_test.log';
    $expected_absolute_path = getcwd() . "/$CLOUDY_LOG";
    $output = $this->execCloudyTools("export CLOUDY_LOG=$CLOUDY_LOG;cloudy bogus");
    $expected_absolute_path = realpath($expected_absolute_path);
    $this->assertMatchesRegularExpression("#More info: $expected_absolute_path#i", $output, 'Assert cloudy error output contains the absolute log path.');
  }

  public function testCloudyLogFileIsCreated() {
    $CLOUDY_LOG = './cloudy_log_test.log';
    $expected_absolute_path = getcwd() . "/$CLOUDY_LOG";
    if (file_exists($expected_absolute_path)) {
      unlink($expected_absolute_path);
    }
    $this->assertFileDoesNotExist($expected_absolute_path);
    $this->execCloudyTools("export CLOUDY_LOG=$CLOUDY_LOG;cloudy bogus");
    $this->assertFileExists($expected_absolute_path);
    unlink($expected_absolute_path);
  }

}
