<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class ExtractFunctionsFromBashCode {

  /**
   * An option to exclude the brackets/body of the function.
   */
  const OPTION_WITHOUT_BODY = 1;

  /**
   * Get the functions snippets from a BASH file.
   *
   * @param string $bash_code
   *   For example, the contents of a BASH file.
   *
   * @return string[]
   */
  public function __invoke(string $bash_code, int $options = 0): array {
    preg_match_all('#\n{2,}(.+?function.+?)(\{.+?\n\})#ius', $bash_code, $functions);
    if (empty($functions)) {
      return [];
    }
    if ($options & self::OPTION_WITHOUT_BODY) {
      return $functions[1];
    }

    return $functions[0];
  }

}
