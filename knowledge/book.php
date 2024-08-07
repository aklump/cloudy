<?php

/** @var string $command */
/** @var string $book_path */
/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */

use AKlump\CloudyDocumentation\PHP\GetPhpFunctions;
use AKlump\CloudyDocumentation\Variables\LoadCodeExampleFileAsVariable;
use AKlump\Knowledge\Events\AssemblePages;
use AKlump\Knowledge\Events\AssembleWebpageAssets;
use AKlump\Knowledge\Events\GetVariables;
use AKlump\Knowledge\Model\BookPageInterface;
use AKlump\Knowledge\Model\Page;
use AKlump\Knowledge\ProcessorResults\ProblemSolution;
use AKlump\Knowledge\User\CloudyDocumentation\ExtractFunctionsFromBashCode;
use AKlump\Knowledge\User\CloudyDocumentation\SortFunctionsByName;
use AKlump\Knowledge\User\CloudyDocumentation\ParseBashFunction;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

const CLOUDY_CORE_DIR = __DIR__ . '/../dist';

/**
 * Create the API page by parsing the Cloudy source code.
 */
$dispatcher->addListener(AssemblePages::NAME, function (AssemblePages $event) {
  exec('which tomdoc.sh', $tomdoc);
  $tomdoc = array_pop($tomdoc);
  if (!$tomdoc) {
    return new ProblemSolution('Missing "Tomdoc.sh"', 'Make sure you have installed Tomdoc (http://tomdoc.org/)');
  }

  //
  // Process the Public API Functions.
  //
  $path_to_public_functions = CLOUDY_CORE_DIR . '/inc/cloudy.api.sh';
  if (!file_exists($path_to_public_functions)) {
    $path_to_public_functions = Path::canonicalize($path_to_public_functions);

    return new ProblemSolution("Cannot locate cloudy.sh for parsing.", "Make sure this path exists: %s", $path_to_public_functions);
  }
  $path_to_cloudy_testing_script = CLOUDY_CORE_DIR . '/inc/cloudy.testing.sh';
  if (!file_exists($path_to_cloudy_testing_script)) {
    $path_to_cloudy_testing_script = Path::canonicalize($path_to_cloudy_testing_script);

    return new ProblemSolution("Cannot locate cloudy.sh for parsing.", "Make sure this path exists: %s", $path_to_cloudy_testing_script);
  }

  $vars = [];
  $function_sorter = new SortFunctionsByName();
  $function_groups = [];
  $function_groups['api_functions'] = $path_to_public_functions;
  $function_groups['test_functions'] = $path_to_cloudy_testing_script;

  //
  // Generate the HTML.
  //
  $loader = new FilesystemLoader(dirname(__FILE__));
  $twig = new Environment($loader, ['cache' => FALSE]);

  foreach ($function_groups as $vars_key => $path) {

    $bash_code = file_get_contents($path);
    $functions = (new ExtractFunctionsFromBashCode())($bash_code, ExtractFunctionsFromBashCode::OPTION_WITHOUT_BODY);

    $vars[$vars_key] = [];
    $function_parser = new ParseBashFunction();
    $vars[$vars_key] = array_map($function_parser, $functions);
    uasort($vars[$vars_key], $function_sorter);

    // Convert objects to an arrays for template usage.
    $vars[$vars_key] = array_values(array_map(function ($function) {
      return json_decode(json_encode($function), TRUE);
    }, $vars[$vars_key]));

    // Add hyphens to option names for readibility.
    $vars[$vars_key] = array_map(function ($function) {
      $function['options'] = array_map(function ($option) {
        if (strlen($option['name']) === 1) {
          $option['name'] = '-' . $option['name'];
        }
        if (strlen($option['name']) > 1) {
          $option['name'] = '--' . $option['name'];
        }

        return $option;
      }, $function['options']);

      return $function;
    }, $vars[$vars_key]);

    $page = new Page($vars_key, 'about');
    $template = $twig->load("page--{$vars_key}.twig");
    $page_body = $template->render($vars);
    $page->setBody($page_body, BookPageInterface::MIME_TYPE_HTML);
    $event->addPage($page);
  }

  // php_file_runner_functions
  $functions = ['api_functions' => (new GetPhpFunctions())()];
  $vars_key = 'api_functions_php';
  $page = new Page($vars_key, 'about');
  $template = $twig->load("page--{$vars_key}.twig");
  $page_body = $template->render($functions);
  $page->setBody($page_body, BookPageInterface::MIME_TYPE_HTML);
  $event->addPage($page);
});

$dispatcher->addListener(GetVariables::NAME, function (GetVariables $event) {
  $loader = new LoadCodeExampleFileAsVariable();

  $base = $event->getPathToSource() . '/src/CloudyDocumentation';
  $event->setVariable('function_docblock', $loader("$base/example_function.sh"));
  $event->setVariable('file_docblock', $loader("$base/example_file.sh"));

  $base = $event->getPathToSource() . '/../tests/Integration/t/KnowledgeExamples/';
  $event->setVariable('php_usage_controller', $loader("$base/controller_include.sh"));
  $event->setVariable('php_usage_php_file_runner', $loader("$base/json_decode.php"));

  // Run a Cloudy instance to capture runtime variables from the current version.
  $cloudy_runtime = $event->getPathToSource() . '/cloudy_runtime/cloudy_runtime.sh';

  foreach (['php_file_runner_variables', 'bash_variables'] as $key) {
    $output = [];
    $exit_status = NULL;
    exec("$cloudy_runtime $key", $output, $exit_status);
    $output = implode(PHP_EOL, $output);
    if ($exit_status != 0) {
      throw new RuntimeException($output);
    }
    $event->setVariable($key, $output);
  }
});

