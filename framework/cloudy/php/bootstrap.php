<?php

/**
 * @file
 * Bootstrap for all php files.
 */

use Ckr\Util\ArrayMerger;
use Symfony\Component\Yaml\Yaml;
use Jasny\DotKey;

/**
 * Root directory of the Cloudy instance script.
 */
define('ROOT', getenv('ROOT'));

/** @var \Composer\Autoload\ClassLoader $class_loader */
$class_loader = require_once getenv('COMPOSER_VENDOR') . '/autoload.php';

/**
 * Sort an array by the length of it's values.
 *
 * @param string ...
 *   Any number of items to be taken as an array.
 *
 * @return array
 *   The sorted array
 */
function array_sort_by_item_length() {
  $stack = func_get_args();
  uasort($stack, function ($a, $b) {
    return strlen($a) - strlen($b);
  });

  return array_values($stack);
}

/**
 * Convert a YAML string to a JSON string.
 *
 * @return string
 *   The valid YAML string.
 *
 * @throws \RuntimeException
 *   If the YAML cannot be parsed.
 */
function yaml_to_json($yaml) {
  if (empty($yaml)) {
    return '{}';
  }
  elseif (!($data = Yaml::parse($yaml))) {
    throw new \RuntimeException("Unable to parse invalid YAML string.");
  }

  return json_encode($data);
}

/**
 * Get a value from a JSON string.
 *
 * @param string $path
 *   The dot path of the data to get.
 * @param string $json
 *   A valid JSON string.
 *
 * @return mixed
 *   The value at $path.
 */
function json_get_value($path, $json) {
  $subject = json_decode($json);
  if (json_last_error() !== JSON_ERROR_NONE) {
    throw new \RuntimeException('Invalid JSON string: ' . json_last_error_msg());
  }

  return DotKey::on($subject)->get($path);
}

/**
 * Loads a JSON file to be used with json_get.
 *
 * Always use this function instead of $(cat foo.json) as json validation and
 * escaping is handled for you.
 *
 * @param string $path
 *
 * @return string
 *   The compressed JSON if file is valid, with single quotes escaped.
 * @throws \InvalidArgumentException If the file does not exist or the file is invalid.
 */
function json_load_file(string $path): string {
  if (!file_exists($path)) {
    throw new \RuntimeException("Missing JSON file: " . $path);
  }
  $contents = file_get_contents($path);

  return json_bash_filter($contents);
}

/**
 * @param string $json
 *   A JSON string to be used by cloudy.
 *
 * @return string
 *   The compressed and escaped as appropriate JSON string.
 */
function json_bash_filter(string $json): string {
  $data = json_decode($json);
  if (json_last_error() !== JSON_ERROR_NONE) {
    throw new \RuntimeException('Invalid JSON string: ' . json_last_error_msg());
  }

  return json_encode($data, JSON_UNESCAPED_SLASHES);
}

/**
 * Load a configuration file into memory.
 *
 * @param $filepath
 *   The absolute filepath to a configuration file.
 *
 * @return array|mixed
 */
function load_configuration_data($filepath, $exception_if_not_exists = TRUE) {
  $data = [];
  if (!file_exists($filepath)) {
    if ($exception_if_not_exists) {
      throw new \RuntimeException("Missing configuration file: " . $filepath);
    }

    return $data;
  }
  if (!($contents = file_get_contents($filepath))) {
    // TODO Need a php method to write a log file, and then log this.
    //    throw new \RuntimeException("Empty configuration file: " . realpath($filepath));
  }
  if ($contents) {
    switch (($extension = pathinfo($filepath, PATHINFO_EXTENSION))) {
      case 'yml':
      case 'yaml':
        try {
          if ($yaml = Yaml::parse($contents)) {
            $data += $yaml;
          }
        }
        catch (\Exception $exception) {
          $class = get_class($exception);
          $message = sprintf("Syntax error in configuration file: %s: %s", $filepath, $exception->getMessage());
          throw new $class($message, $exception->getCode());
        }
        break;

      case 'json':
        if ($json = json_decode($contents, TRUE)) {
          $data += $json;
        }
        break;

      default:
        throw new \RuntimeException("Configuration files of type \"$extension\" are not supported.");

    }
  }

  return $data;
}

/**
 * Merge an array of configuration arrays.
 *
 * @param... two or more arrays to merge.
 *
 * @return array|mixed
 *   The merged array.
 */
function merge_config() {
  $stack = func_get_args();
  $merged = [];
  while (($array = array_shift($stack))) {
    $merged = ArrayMerger::doMerge($merged, $array);
  }

  return $merged;
}

/**
 * Create a hash of a string of filenames separated by \n.
 *
 * @return string
 *   The has of filenames.
 */
function get_config_cache_id() {
  $paths = func_get_arg(0);

  return md5(str_replace("\n", ':', $paths));
}

/**
 * Expand a path based on $config_path_base.
 *
 * This function can handle:
 * - paths that begin with ~/
 * - paths that contain the glob character '*'
 * - absolute paths
 * - relative paths to `config_path_base`
 *
 * @param string $path
 *   The path to expand.
 *
 * @return array
 *   The expanded paths.  This will have multiple items when using globbing.
 */
function _cloudy_realpath($path) {
  global $_config_path_base;

  if (!empty($_SERVER['HOME'])) {
    $path = preg_replace('/^~\//', rtrim($_SERVER['HOME'], '/') . '/', $path);
  }
  if (!empty($path) && substr($path, 0, 1) !== '/') {
    $path = ROOT . '/' . "$_config_path_base/$path";
  }
  if (strstr($path, '*')) {
    $paths = glob($path);
  }
  else {
    $paths = [$path];
  }
  $paths = array_map(function ($item) {
    return is_file($item) ? realpath($item) : $item;
  }, $paths);

  return $paths;
}
