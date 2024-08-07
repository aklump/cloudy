<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class AdditionalBootstrapTest extends TestCase {

  use TestWithCloudyTrait;

  public function testAdditionalBootstrapExitsWithBogusPath() {
    $this->bootCloudy(__DIR__ . '/../t/AdditionalBootstrap/invalid_base.yml');
    $this->execCloudy('');
    $this->assertMatchesRegularExpression('#Invalid additional_bootstrap#', $this->getCloudyOutput());
    $this->assertSame(1, $this->getCloudyExitStatus());
  }

  public function testAdditionalBootstrapWorksWithRelativePath() {
    $this->bootCloudy(__DIR__ . '/../t/AdditionalBootstrap/subdir/relative_path.yml');
    $output = $this->execCloudy('');
    $this->assertSame('my bootstrap worked', $output);
  }

  public function testAdditionalBootstrapWorksWithAbsolutePath() {
    $this->bootCloudy(__DIR__ . '/../t/AdditionalBootstrap/absolute_path.yml');
    $output = $this->execCloudy('');
    $this->assertSame('my bootstrap worked', $output);
  }

}
