<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class CloudyPackageControllerTest extends TestCase {

  use TestWithCloudyTrait;

  public function testLogfileExitsWithFailure() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml', 'test_runner.logfile.sh');
    $output = $this->execCloudy('');
    $this->assertMatchesRegularExpression('#LOGFILE was changed#', $output);
    $this->assertMatchesRegularExpression('#replace with CLOUDY_LOG#', $output);
  }

  public function testConfigExitsWithFailure() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml', 'test_runner.config.sh');
    $output = $this->execCloudy('');
    $this->assertMatchesRegularExpression('#CONFIG was changed#', $output);
    $this->assertMatchesRegularExpression('#replace with CLOUDY_PACKAGE_CONFIG#', $output);
  }

  public function testAppRootExitsWithFailure() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml', 'test_runner.app_root.sh');
    $output = $this->execCloudy('');
    $this->assertMatchesRegularExpression('#APP_ROOT was changed#', $output);
    $this->assertMatchesRegularExpression('#replace with CLOUDY_BASEPATH#', $output);
  }

  public function testRelativeCloudyBasepathResolvesExitsWithFailure() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml', 'test_runner.cloudy_basepath.relative.sh');
    $output = $this->execCloudy('');
    $this->assertMatchesRegularExpression('#CLOUDY_BASEPATH must be absolute when explicitly set#', $output);
  }

  public function testAbsoluteCloudyBasepathCanBeSet() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeComposer/vendor/aklump/package/config.yml', 'test_runner.cloudy_basepath.absolute.sh');
    $output = $this->execCloudy('echo $CLOUDY_BASEPATH');
    $expected = realpath(__DIR__ . '/../t/InstallTypeComposer/vendor/aklump/package/');
    $this->assertSame($expected, $output);
  }

}
