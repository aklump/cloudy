<?php

namespace AKlump\Cloudy\Tests\Unit;

use AKlump\Cloudy\DeserializeBashArray;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Cloudy\DeserializeBashArray
 */
class DeserializeBashArrayTest extends TestCase {

  public function dataFortestInvokeProvider() {
    $tests = [];
    $tests[] = [
      [],
      "declare -ax CLOUDY_FAILURES='()'",
    ];
    $tests[] = [
      [],
      "declare -a CLOUDY_FAILURES='()'",
    ];
    $tests[] = [
      [
        'do',
        're',
        'mi',
        'fa sol',
      ],
      'declare -a CLOUDY_FAILURES=\'([0]="do" [1]="re" [2]="mi" [3]="fa sol")\'',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke(array $expected, string $serialized_array) {
    $result = (new DeserializeBashArray())($serialized_array);
    $this->assertSame($expected, $result);
  }

}
