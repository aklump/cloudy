<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class GlobalsTest extends TestCase {

  use TestWithCloudyTrait;

  public function dataFortestGlobalsProvider() {
    $integration_tests_dir = __DIR__ . '/../';
    $tests = [];
    $tests[] = ['CLOUDY_START_DIR', getcwd()];
    $tests[] = ['CLOUDY_CORE_DIR', $this->getCloudyCoreDir()];
    $tests[] = ['CLOUDY_CACHE_DIR', $this->getCloudyCacheDir()];
    $tests[] = [
      'CLOUDY_PACKAGE_CONFIG',
      $integration_tests_dir . '/t/InstallTypeCore/config.yml',
    ];
    $tests[] = [
      'CLOUDY_BASEPATH',
      $integration_tests_dir . '/t/InstallTypeCore/',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestGlobalsProvider
   */
  public function testBashGlobals(string $var_name, string $expected) {
    $result = $this->execCloudy('echo $' . $var_name);
    $this->assertNotEmpty($result, 'Assert value for ' . $var_name);
    $result = realpath($result);
    $expected = realpath($expected);
    $this->assertSame($expected, $result, 'Assert expected path for $' . $var_name);
  }

  public function testCloudyLog() {
    $result = $this->execCloudy('echo $CLOUDY_LOG');
    $result = realpath($result);
    $expected = $this->getCloudyLog();
    $this->assertSame($expected, $result, 'Assert expected path for $CLOUDY_LOG');
  }

  public function testRoot() {
    $result = $this->execCloudy('echo $ROOT');
    $result = realpath($result);
    $expected = dirname($this->getCloudyPackageController());
    $this->assertSame($expected, $result, 'Assert expected path for $ROOT');
  }

  public function testCloudyPackageController() {
    $result = $this->execCloudy('echo $CLOUDY_PACKAGE_CONTROLLER');
    $result = realpath($result);
    $this->assertSame($this->getCloudyPackageController(), $result, 'Assert expected path for $CLOUDY_PACKAGE_CONTROLLER');
  }

  public function testCloudyRuntimeUuid() {
    $uuid = $this->execCloudy('echo $CLOUDY_RUNTIME_UUID');
    $this->assertMatchesRegularExpression('#^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$#', $uuid);
  }

  public function testSourcePHPProvidesExpectedVariables() {
    $result = $this->execCloudy(sprintf('. "$PHP_FILE_RUNNER" "%s"', __DIR__ . '/../t/InstallTypeCore/tests/variables.php'));
    $this->assertNotEmpty($result);
    $data = json_decode($result, TRUE);
    $this->assertSame($this->getCloudyCoreDir(), $data['CLOUDY_CORE_DIR'], 'Assert $CLOUDY_CORE_DIR in php_file_runner');
    $this->assertSame($this->getCloudyCacheDir(), $data['CLOUDY_CACHE_DIR'], 'Assert $CLOUDY_CACHE_DIR in php_file_runner');
    $this->assertSame($this->getCloudyPackageController(), $data['CLOUDY_PACKAGE_CONTROLLER'], 'Assert $CLOUDY_PACKAGE_CONTROLLER in php_file_runner');
    $this->assertSame($this->getCloudyPackageConfig(), $data['CLOUDY_PACKAGE_CONFIG'], 'Assert $CLOUDY_PACKAGE_CONFIG in php_file_runner');
    $expected_cloudy_basepath = realpath(__DIR__ . '/../t/InstallTypeCore/');
    $this->assertSame($expected_cloudy_basepath, $data['CLOUDY_BASEPATH'], 'Assert $CLOUDY_BASEPATH in php_file_runner');
    $this->assertMatchesRegularExpression('#^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$#', $data['CLOUDY_RUNTIME_UUID']);
    $this->assertMatchesRegularExpression('#\.sh$#', $data['CLOUDY_RUNTIME_ENV'], 'Assert $CLOUDY_RUNTIME_ENV appears to be a shell filepath.');
    $this->assertDirectoryExists($data['CLOUDY_COMPOSER_VENDOR'], 'Assert $CLOUDY_COMPOSER_VENDOR points to an existing directory.');
    $this->assertIsArray($data['CLOUDY_FAILURES'], 'Assert $CLOUDY_FAILURES is set as an array.');
    $this->assertIsArray($data['CLOUDY_SUCCESSES'], 'Assert $CLOUDY_SUCCESSES is set as an array.');
    $this->assertIsInt($data['CLOUDY_EXIT_STATUS'], 'Assert $CLOUDY_EXIT_STATUS is set as an integer.');
    $this->assertMatchesRegularExpression('#\.sh$#', $data['PHP_FILE_RUN_CONTROLLER'], 'Assert $PHP_FILE_RUN_CONTROLLER appears to be a shell filepath.');

    $this->assertJson($data['CLOUDY_CONFIG_JSON']);
    $this->assertSame(getcwd(), $data['CLOUDY_START_DIR']);

    $this->assertFileExists($data['CLOUDY_LOG']);
  }

  public function testCachedJSONContainsExpectedGlobals() {
    $result = $this->execCloudy('echo $CLOUDY_CONFIG_JSON');
    $this->assertNotEmpty($result);
    $data = json_decode($result, TRUE)['__cloudy'];
    $this->assertSame($this->getCloudyCoreDir(), $data['CLOUDY_CORE_DIR'], 'Assert $CLOUDY_CORE_DIR in \$CLOUDY_CONFIG_JSON');
    $this->assertSame($this->getCloudyCacheDir(), $data['CLOUDY_CACHE_DIR'], 'Assert $CLOUDY_CACHE_DIR in \$CLOUDY_CONFIG_JSON');
    $this->assertSame($this->getCloudyPackageConfig(), $data['CLOUDY_PACKAGE_CONFIG'], 'Assert $CLOUDY_PACKAGE_CONFIG in \$CLOUDY_CONFIG_JSON');
    $expected_cloudy_basepath = realpath(__DIR__ . '/../t/InstallTypeCore/');
    $this->assertSame($expected_cloudy_basepath, $data['CLOUDY_BASEPATH'], 'Assert $CLOUDY_BASEPATH in \$CLOUDY_CONFIG_JSON');
  }

  protected function setUp(): void {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
  }

}
