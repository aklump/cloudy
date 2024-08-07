<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ConfigPathBaseTest extends TestCase {

  use TestWithCloudyTrait;

  public function testCanReadAdditionalConfigInCloudyPackage() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $output = $this->execCloudy('eval $(get_config_as "master_dir" "master_dir"); echo $master_dir');
    $this->assertSame('private', $output);
  }

  public function testCanReadAdditionalConfigInPMInstalled() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypePM/opt/aklump/package/config.yml');
    $output = $this->execCloudy('eval $(get_config_as "master_dir" "master_dir"); echo $master_dir');
    $this->assertSame('private', $output);
  }

  public function testCanReadAdditionalConfigInPMInstalledNoConfigPathBase() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypePM.NoConfigPathBase/opt/aklump/package/config.yml');
    $output = $this->execCloudy('eval $(get_config_as "master_dir" "master_dir"); echo $master_dir');
    $this->assertSame('private', $output);
  }

}
