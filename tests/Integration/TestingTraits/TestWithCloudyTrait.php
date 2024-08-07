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

  private $testRunner;

  /**
   * Load a cloudy config and set the directory for testing.
   *
   * @param string $cloudy_package_config A YAML file which defines the Cloudy app.  It's
   * parent directory -- CLOUDY_BASEPATH -- will be used to resolve the paths to be
   * tested, and should therefore contain them.
   * @param string $test_runner
   *
   * @return void
   */
  public function bootCloudy(string $cloudy_package_config, string $test_runner = 'test_runner.sh'): void {
    if (!is_file($cloudy_package_config)) {
      throw new TestFileNotFoundException($cloudy_package_config);
    }
    if (!file_exists($cloudy_package_config) || !is_file($cloudy_package_config)) {
      throw new TestFileNotFoundException($cloudy_package_config);
    }
    $this->cloudyPackageConfig = realpath($cloudy_package_config);
    $this->cloudyTestDir = dirname($cloudy_package_config);
    $this->testRunner = $test_runner;

    if ($this->didBaseConfigChange($cloudy_package_config)) {
      $this->clearCache();
      $log = $this->getCloudyLog();
      if (file_exists($log)) {
        file_put_contents($this->getCloudyLog(), '');
      }
    }
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
    return $this->getCloudyCoreDir() . '/cache';
  }

  protected function getCloudyPackageController(): string {
    if (empty($this->testRunner)) {
      throw new RuntimeException('Missing \$this->testRunner');
    }

    return realpath(__DIR__ . '/../cloudy_bridge/' . $this->testRunner);
  }

  protected function getCloudyPackageConfig(): string {
    return $this->cloudyPackageConfig;
  }

  protected function getCloudyLog(): string {
    touch(__DIR__ . '/../tests.log');

    return realpath(__DIR__ . '/../tests.log');
  }

  /**
   * Execute a script in a fully booted cloudy environment.
   *
   * @param string $test_script Path to the bash test file to execute; it must
   * be relative to the dirname of the $cloudy_package_config.
   * @param... Additional arguments sent to test script file.
   *
   * @return string
   *   The output from the execution.
   */
  public function execCloudy(string $test_script): string {
    $this->cloudyOutput = [];
    $this->cloudyResultCode = NULL;

    if (!$this->pointsToFile($test_script)) {
      $data = $test_script . PHP_EOL;
      $test_script = tempnam(sys_get_temp_dir(), time()) . '.sh';
      file_put_contents($test_script, $data);
      $file_to_delete = $test_script;
    }
    else {
      $test_script = $this->cloudyTestDir . "/$test_script";
    }

    if (!file_exists($test_script)) {
      throw new InvalidArgumentException(sprintf('%s does not exist.', $test_script));
    }

    $exports = '';

    // Enable logging for all test runners.
    $exports .= sprintf('export CLOUDY_CORE_DIR="%s"', CLOUDY_CORE_DIR);
    $exports .= sprintf('export CLOUDY_LOG="%s"', $this->getCloudyLog());
    $command = sprintf($exports . ';' . __DIR__ . '/../cloudy_bridge/%s "%s" "%s"', $this->testRunner, $this->cloudyPackageConfig, $test_script);
    exec($command, $this->cloudyOutput, $this->cloudyResultCode);

    if (isset($file_to_delete)) {
      unlink($file_to_delete);
    }

    return $this->getCloudyOutput();
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

}
