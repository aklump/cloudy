<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class DetectCloudyBasePathTest extends TestCase {

  use TestWithCloudyTrait;

  public function dataFortestAssertInstallationTypeDetectedCorrectlyProvider() {
    $tests = [];
    $tests[] = ['InstallTypePM', 'InstallTypePM/opt/aklump/package/config.yml'];
    $tests[] = ['InstallTypeCore', 'InstallTypeCore/config.yml'];
    $tests[] = ['InstallTypeComposer', 'InstallTypeComposer/vendor/aklump/package/config.yml'];

    return $tests;
  }

  /**
   * @dataProvider dataFortestAssertInstallationTypeDetectedCorrectlyProvider
   */
  public function testCloudyBasePathDetectedCorrectly($expected, $config) {
    $this->bootCloudy(__DIR__ . "/../t/$config");
    $result = $this->execCloudy('_cloudy_detect_basepath');

    $expected = realpath(__DIR__ . "/../t/$expected");
    $this->assertSame($expected, $result);
  }

}
