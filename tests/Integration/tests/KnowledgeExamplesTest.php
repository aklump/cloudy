<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class KnowledgeExamplesTest extends TestCase {

  use TestWithCloudyTrait;

  public function testJsonExampleForColorRed() {
    $this->bootCloudy(__DIR__ . '/../t/KnowledgeExamples/config.yml');
    $this->execCloudy('controller_include.sh');
    $this->assertMatchesRegularExpression('#The provided color is: red#', $this->getCloudyOutput());
  }
}
