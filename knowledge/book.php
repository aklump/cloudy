<?php

/** @var string $command */
/** @var string $book_path */
/** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */

use AKlump\Knowledge\Events\AssemblePages;
use AKlump\Knowledge\Events\AssembleWebpageAssets;
use AKlump\Knowledge\Model\BookPageInterface;
use AKlump\Knowledge\Model\Page;
use AKlump\Knowledge\ProcessorResults\ProblemSolution;
use AKlump\Knowledge\User\CloudyDocumentation\ExtractFunctionsFromBashCode;
use AKlump\Knowledge\User\CloudyDocumentation\SortFunctionsByName;
use AKlump\Knowledge\User\CloudyDocumentation\ParseBashFunction;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


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
  $path_to_public_functions = __DIR__ . '/../framework/cloudy/inc/cloudy.api.sh';
  if (!file_exists($path_to_public_functions)) {
    $path_to_public_functions = Path::canonicalize($path_to_public_functions);

    return new ProblemSolution("Cannot locate cloudy.sh for parsing.", "Make sure this path exists: %s", $path_to_public_functions);
  }
  $path_to_cloudy_testing_script = __DIR__ . '/../framework/cloudy/inc/cloudy.testing.sh';
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
});


