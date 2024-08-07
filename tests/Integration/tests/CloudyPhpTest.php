<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithMultiplePhpVersionsTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversNothing
 */
class CloudyPhpTest extends TestCase {

  use TestWithCloudyTrait;
  use TestWithMultiplePhpVersionsTrait;

  public function testCanReadPhpPathFromLocalYamlFile() {
    $php8 = $this->getPathToPhp8();
    $local_config_path = __DIR__ . '/../t/CloudyPHP/config.local.yml';
    $local_config_data = ['shell_commands' => ['php' => $php8]];
    file_put_contents($local_config_path, Yaml::dump($local_config_data));
    $this->bootCloudy(__DIR__ . '/../t/CloudyPHP/base.yml', 'test_runner.cloudy_php.sh');
    $this->execCloudy('echo $CLOUDY_PHP');
    $this->assertSame($php8, $this->getCloudyOutput());
  }

  public function testCloudySetsCloudyPhpWithTheSystemDefaultAutomatically() {
    $this->bootCloudy(__DIR__ . '/../t/ConfigTest/base.yml');
    $result = $this->execCloudy('echo $CLOUDY_PHP');
    $this->assertNotEmpty($result, 'Assert value for $CLOUDY_PHP');
    exec("$result -v", $output);
    $output = implode(PHP_EOL, $output);
    $this->assertMatchesRegularExpression('#PHP [\d.]+#', $output);
  }

}
