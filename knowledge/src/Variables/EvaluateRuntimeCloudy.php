<?php

namespace AKlump\CloudyDocumentation\Variables;

class EvaluateRuntimeCloudy {

  /**
   * @var string
   */
  private $controller;

  public function __construct(string $cloudy_package_controller) {
    $this->controller = $cloudy_package_controller;
  }

  /**
   * @param string $bash_code The BASH string to be evaluated in a Cloudy
   * runtime environment.  E.g. 'echo "$CLOUDY_BASEPATH"'
   *
   * @return string
   *   The result of the evaluation.
   */
  public function __invoke(string $bash_code): string {
    $bash_code = str_replace(['$', '"'], ['\\$', '\"'], $bash_code);
    $command = sprintf('%s evaluate "%s"', $this->controller, $bash_code);
    exec($command, $output);

    return implode(PHP_EOL, $output);
  }
}
