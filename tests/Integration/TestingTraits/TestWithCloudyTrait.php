<?php

namespace AKlump\Cloudy\Tests\Integration\TestingTraits;

use InvalidArgumentException;
use PHPUnit\TextUI\TestFileNotFoundException;
use RuntimeException;

/**
 * A trait for Integration testing Cloudy.
 */
trait TestWithCloudyTrait {

  private $cloudyPackageConfig;

  private $cloudyTestDir;

  private $cloudyOutput;

  private $cloudyResultCode;

  private $packageController;

  private $cloudyBasepath;

  /**
   * Load a cloudy config and set the directory for testing.
   *
   * @param string $cloudy_package_config A YAML file which defines the Cloudy app.  It's
   * parent directory -- CLOUDY_BASEPATH -- will be used to resolve the paths to be
   * tested, and should therefore contain them.
   * @param string $cloudy_package_controller This may be absolute or relative.
   * Relative paths will be assumed to be within the cloudy_bridge directory.
   *
   * @return void
   */
  public function bootCloudy(string $cloudy_package_config, string $cloudy_package_controller = 'test_runner.sh'): void {
    if (!is_file($cloudy_package_config)) {
      throw new TestFileNotFoundException($cloudy_package_config);
    }
    if (!file_exists($cloudy_package_config) || !is_file($cloudy_package_config)) {
      throw new TestFileNotFoundException($cloudy_package_config);
    }
    $this->cloudyPackageConfig = $this->getCanonicalPath($cloudy_package_config);
    $this->cloudyTestDir = dirname($cloudy_package_config);
    $this->setCloudyPackageController($cloudy_package_controller);

    // We are going to sniff out the basepath and cache in memory to speed up
    // subsequent calls with the same runner/config combo.
    $this->cloudyBasepath = NULL;
    static $basepaths = [];
    $cid = md5(json_encode(func_get_args()));
    if (!isset($basepaths[$cid])) {
      $basepaths[$cid] = $this->execCloudy('echo $CLOUDY_BASEPATH');
      $this->resetExecutionResults();
      if (!file_exists($basepaths[$cid])) {
        $basepaths[$cid] = $this->getCanonicalPath(dirname($cloudy_package_config));
      }
    }
    $this->cloudyBasepath = $basepaths[$cid];

    if ($this->didBaseConfigChange($cloudy_package_config)) {
      $this->clearCache();
      $log = $this->getCloudyLog();
      if (file_exists($log)) {
        file_put_contents($this->getCloudyLog(), '');
      }
    }
  }

  public function getCloudyBasepath(): string {
    if (empty($this->cloudyBasepath)) {
      throw new RuntimeException('You must call ::bootCloudy first.');
    }

    return $this->cloudyBasepath;
  }

  /**
   * @param string $cloudy_package_config
   *
   * @return bool True if the config has changed.
   */
  protected function didBaseConfigChange(string $cloudy_package_config): bool {
    if (!file_exists($this->getCloudyCacheDir() . '/_cached.test_runner.config.json')) {
      return FALSE;
    }
    $data = json_decode(file_get_contents($this->getCloudyCacheDir() . '/_cached.test_runner.config.json'), TRUE);
    if (empty($data)) {
      return TRUE;
    }

    return $cloudy_package_config !== $data['__cloudy']['CLOUDY_PACKAGE_CONFIG'];
  }


  private function clearCache(): void {
    $files = glob($this->getCloudyCacheDir() . '/_cached.test_runner.*');
    foreach ($files as $file) {
      unlink($file);
    }
  }

  protected function getCloudyCoreDir(): string {
    return CLOUDY_CORE_DIR;
  }

  protected function getCloudyCacheDir(): string {
    return rtrim($this->getCloudyBasepath(), '/') . '/.' . pathinfo($this->getCloudyPackageController(), PATHINFO_FILENAME) . '/.cache';
  }

  private function setCloudyPackageController(string $package_controller): void {
    if (substr($package_controller, 0, 1) !== '/') {
      $package_controller = realpath(__DIR__ . '/../cloudy_bridge/' . $package_controller);
    }
    $this->packageController = $package_controller;
  }

  protected function getCloudyPackageController(): string {
    if (empty($this->packageController)) {
      throw new RuntimeException('Missing \$this->packageController');
    }

    return $this->packageController;
  }

  protected function getCloudyPackageConfig(): string {
    return $this->cloudyPackageConfig;
  }

  protected function getCloudyLog(): string {
    touch(__DIR__ . '/../tests.log');

    return realpath(__DIR__ . '/../tests.log');
  }

  /**
   * Test cloudy tools with a CLI expression.
   *
   * This function will self-boot; no need to call ::bootCloudy()
   *
   * @param string $cli_command e.g. 'cloudy new bla --yes=foo'
   *
   * @code
   * $this->execCloudyTools('cloudy new bla --yes=foo');
   * @endcode
   *
   * @return void
   */
  protected function execCloudyTools(string $cli_command): string {
    $this->resetExecutionResults();
    $this->bootCloudy(__DIR__ . '/../t/CLI/cloudy_tools.yml');
    $replacement = '';
    $replacement .= realpath(__DIR__ . '/../cloudy_bridge/test_runner.cli.sh');
    $replacement .= ' "' . $this->getCloudyPackageConfig() . '" ';

    $command = $cli_command;
    $command = preg_replace('#^cloudy #', $replacement, $command, 1);
    $command = preg_replace('#;cloudy #', ';' . $replacement, $command, 1);

    if ($command === $cli_command) {
      throw new InvalidArgumentException(sprintf('Incorrect syntax: %s', $cli_command));
    }
    exec($command, $this->cloudyOutput, $this->cloudyResultCode);

    return $this->getCloudyOutput();
  }

  private function resetExecutionResults() {
    $this->cloudyOutput = [];
    $this->cloudyResultCode = NULL;
  }

  /**
   * Execute a script in a fully booted cloudy environment.
   *
   * @param string $test_script Path to the bash test file to execute; it must
   * be relative to the dirname of the $cloudy_package_config.
   * @param... Additional arguments sent to test script file; be aware that they
   * appear in the controller as $3, $4, etc. (not $1, $2 as you might expect).
   *
   * @return string
   *   The output from the execution.
   */
  public function execCloudy(string $test_script): string {
    $this->resetExecutionResults();
    static $script_base;
    if (empty($script_base)) {
      $script_base = sys_get_temp_dir() . '/cloudy/tests';
      if (!file_exists($script_base)) {
        mkdir($script_base, 0755, TRUE);
      }
    }

    $test_script_args = func_get_args();
    $test_script = array_shift($test_script_args);

    if (!$this->pointsToFile($test_script)) {
      $data = $test_script . PHP_EOL;
      $test_script = tempnam("$script_base", __FUNCTION__) . '.sh';
      file_put_contents($test_script, $data);
      $file_to_delete = $test_script;
    }
    else {
      $test_script = $this->cloudyTestDir . "/$test_script";
    }

    if (!file_exists($test_script)) {
      throw new InvalidArgumentException(sprintf('%s does not exist.', $test_script));
    }

    $exports = [];
    $exports[] = sprintf('export CLOUDY_CORE_DIR="%s"', CLOUDY_CORE_DIR);
    $exports[] = sprintf('export CLOUDY_LOG="%s"', $this->getCloudyLog());

    $command = sprintf('%s "%s"', $this->getCloudyPackageController(), $this->cloudyPackageConfig);
    $test_script_args = array_merge([$test_script], $test_script_args);
    $command .= $this->quoteArgumentsArray($test_script_args);

    $command = [$command];
    if ($exports) {
      $command = array_merge($exports, $command);
    }
    $command = implode(';', $command);
    try {
      exec($command, $this->cloudyOutput, $this->cloudyResultCode);
    }
    finally {
      if (isset($file_to_delete)) {
        unlink($file_to_delete);
      }
    }

    return $this->getCloudyOutput();
  }

  private function quoteArgumentsArray(array $arguments): string {
    return array_reduce($arguments, function ($carry, $arg) {
      return "$carry \"$arg\"";
    });
  }

  public function getCloudyExitStatus(): int {
    return $this->cloudyResultCode;
  }

  public function getCloudyOutput(): string {
    return implode(PHP_EOL, $this->cloudyOutput ?? []);
  }

  /**
   * @param string $test_script
   *
   * @return bool True if $test_script is a filename, rather than BASH code.
   */
  private function pointsToFile(string $test_script): bool {
    if (preg_match('#\S+\.\S+#', $test_script, $matches)
      && $matches[0] === basename($test_script)) {
      return TRUE;
    }
    $test_script_file = $this->cloudyTestDir . "/$test_script";
    if (file_exists($test_script_file)) {
      return TRUE;
    }

    return FALSE;
  }

  public function getCanonicalPath(string $path): string {
    $suffix = '';
    if (!file_exists($path)) {
      throw new \InvalidArgumentException(sprintf('$path does not exist: %s', $path));
    }
    if (is_file($path)) {
      $suffix = basename($path);
      $path = dirname($path);
    }
    $path = exec("cd \"$path\" && pwd -L");
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    if (!empty($suffix)) {
      $path = $path . DIRECTORY_SEPARATOR . $suffix;
    }

    return $path;
  }

}
