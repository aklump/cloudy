<?php

namespace AKlump\CloudyDocumentation\PHP;

use RuntimeException;

class GetPhpFunctions {

  public function __invoke(): array {
    $cloudy_runtime = __DIR__ . '/../..//cloudy_runtime/cloudy_runtime.sh';
    $names = [];
    $exit_status = NULL;
    exec("$cloudy_runtime functions", $names, $exit_status);
    if ($exit_status != 0) {
      throw new RuntimeException($names);
    }

    return array_map(function ($function) {
      return ['name' => $function];
    }, $names);
  }
}
