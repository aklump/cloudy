<?php

namespace AKlump\CloudyDocumentation\Variables;

use InvalidArgumentException;

class LoadCodeExampleFileAsVariable {

  public function __invoke(string $path_to_code_example): string {
    if (!file_exists($path_to_code_example)) {
      throw new InvalidArgumentException(sprintf("Missing documentation example file.\nCheck if the path %s, has moved.", $path_to_code_example));
    }
    $contents = file_get_contents($path_to_code_example);
    switch (pathinfo($path_to_code_example, PATHINFO_EXTENSION)) {
      case 'php':
        $contents = preg_replace('#^<\?php\s+#i', '', $contents);
        $contents = "```php\n" . rtrim($contents, "\n") . "\n```\n";
        break;
      case 'sh':
        $contents = preg_replace('/#!.+?\n+/', '', $contents);
        $contents = "```shell\n" . rtrim($contents, "\n") . "\n```\n";
        break;
    }

    return $contents;
  }
}
