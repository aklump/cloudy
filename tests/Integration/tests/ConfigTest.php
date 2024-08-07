<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ConfigTest extends TestCase {

  use TestWithCloudyTrait;

  public function testAdditionalConfigCanUnderstandSingleAndDoubleGlobs() {
    $base_dir = realpath(__DIR__ . '/../t/InstallTypeCore');
    $this->bootCloudy($base_dir . '/config_globs.yml');
    $this->execCloudy('eval $(get_config_path_as -a "paths" "additional_config"); echo ${paths[0]}; echo ${paths[1]}; eval $(get_config_as "food" "food"); echo $food');
    $expected[] = $base_dir . '/config/user_config.yml';
    $expected[] = $base_dir . '/config/glob/subject/glob_add_config.yml';
    $expected[] = 'quiche';
    $expected = implode(PHP_EOL, $expected);
    $this->assertSame($expected, $this->getCloudyOutput());
  }

  public function testCanReadBaseConfig() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $output = $this->execCloudy('eval $(get_config_as "title" "title");echo $title');
    $this->assertSame('Install Type Core', $output);
  }

  public function testCanReadAdditionalConfig() {
    $this->bootCloudy(__DIR__ . '/../t/InstallTypeCore/config.yml');
    $output = $this->execCloudy('eval $(get_config_as "color" "color"); echo $color');
    $this->assertSame('blue', $output);
  }

}
