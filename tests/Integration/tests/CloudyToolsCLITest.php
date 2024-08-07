<?php

namespace AKlump\Cloudy\Tests\Integration\tests;

use AKlump\Cloudy\Tests\Integration\TestingTraits\TestWithCloudyTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class CloudyToolsCLITest extends TestCase {

  use TestWithCloudyTrait;

  public function testToolsCommandWithInvalidOptionFailsWithExpectedMessage() {
    $this->execCloudyTools('cloudy new bla --yes=foo');
    $pattern = '#\[yes\] Boolean options may not be given a value.#';
    $this->assertMatchesRegularExpression($pattern, $this->getCloudyOutput());
    $this->assertSame(1, $this->getCloudyExitStatus());
  }

}
