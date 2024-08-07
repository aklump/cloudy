<?php

namespace AKlump\CloudyDocumentation\Tests\Unit\CloudyDocumentation;

use AKlump\Knowledge\User\CloudyDocumentation\ParseBashFunction;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\Knowledge\User\CloudyDocumentation
 */
final class ParseBashFunctionTest extends TestCase {

  public function testExport() {
    $bash_code = file_get_contents(__DIR__ . '/../testing_files/function.sh');
    $info = (new ParseBashFunction())($bash_code);
    $this->assertSame('$LOREM', $info->getExports()[0]->name);
    $this->assertSame('string', $info->getExports()[0]->type);
    $this->assertSame('will be set and exported.', $info->getExports()[0]->description);

    $bash_code = file_get_contents(__DIR__ . '/../testing_files/function2.sh');
    $info = (new ParseBashFunction())($bash_code);
    $this->assertSame('$LOREM', $info->getExports()[0]->name);
    $this->assertSame('string', $info->getExports()[0]->type);
    $this->assertSame('will be set and exported.', $info->getExports()[0]->description);
  }

  public function testGlobal() {
    $bash_code = file_get_contents(__DIR__ . '/../testing_files/array_csv.sh');
    $info = (new ParseBashFunction())($bash_code);
    $this->assertSame('$array_csv__array', $info->getGlobals()[0]->name);
    $this->assertSame('array', $info->getGlobals()[0]->type);
    $this->assertSame('', $info->getGlobals()[0]->description);

    $this->assertSame('Use comma+space and then the word "all" as the final separator as when writing English prose, e.g. "do, re and mi".', $info->getOptions()[0]->description);
  }

  public function testPathIsYaml() {
    $bash_code = <<<BASH
    ##
     # Check if a filepath is a YAML file.
     #
     # @param string Path to file in question.
     #
     # @return 0 If it is.
     # @return 1 If it is not a YAML file.
     ##
    function path_is_yaml()
    BASH;
    $info = (new ParseBashFunction())($bash_code);
    $this->assertSame('path_is_yaml', $info->getName());
    $this->assertSame('Check if a filepath is a YAML file.', $info->getSummary());
    $this->assertEmpty($info->getDescription());
    $this->assertSame('string', $info->getParameters()[0]->type);
    $this->assertSame('Path to file in question.', $info->getParameters()[0]->description);
    $this->assertSame(0, $info->getReturns()[0]->value);
    $this->assertSame('If it is.', $info->getReturns()[0]->description);
    $this->assertSame(1, $info->getReturns()[1]->value);
    $this->assertSame('If it is not a YAML file.', $info->getReturns()[1]->description);
    $this->assertEmpty($info->getEchos());
  }

  public function testJsonGet() {
    $bash_code = <<<BASH
    # Get the set JSON
    #
    # @echo The JSON string set by json_set
    #
    # @code
    #   json="$(json_get)"
    # @endcode
    #
    function json_get()
    BASH;
    $info = (new ParseBashFunction())($bash_code);
    $this->assertSame('json_get', $info->getName());
    $this->assertSame('Get the set JSON', $info->getSummary());
    $this->assertSame('The JSON string set by json_set', $info->getEchos()[0]->description);
    $this->assertSame("@code\n  json=\"$(json_get)\"\n@endcode", $info->getDescription());
  }

  public function dataFortestInvokeProvider() {
    $tests = [];
    $tests[] = [
      file_get_contents(__DIR__ . '/../testing_files/function.sh'),
    ];
    $tests[] = [
      file_get_contents(__DIR__ . '/../testing_files/function2.sh'),
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke(string $bash_code) {
    $info = (new ParseBashFunction())($bash_code);

    // Assert the summary
    $this->assertSame('Set a JSON string to be later read by json_get_value().', $info->getSummary());

    // Assert the description
    $this->assertSame("Call this once to put your json string into memory, then make unlimited calls to json_get_value as necessary.  You may check the return code to ensure JSON syntax is valid.  If your string contains single quotes, you will need to escape them.\n\n@code\n  json_set '{\"foo\":{\"bar\":\"baz et al\"}}'\n@endcode", $info->getDescription());

    // Assert the globals.
    $globals = $info->getGlobals();
    $this->assertCount(1, $globals);
    $this->assertSame('$json_content', $globals[0]->name);
    $this->assertSame('string', $globals[0]->type);
    $this->assertSame('will by set with the mutated JSON.', $globals[0]->description);

    // Assert the params.
    $params = $info->getParameters();
    $this->assertCount(2, $params);
    $this->assertSame('string', $params[0]->type);
    $this->assertSame('A JSON string, wrapped by single quotes.', $params[0]->description);
    $this->assertSame('number', $params[1]->type);
    $this->assertSame('The level of cleaning to use.', $params[1]->description);

    // Assert the options.
    $options = $info->getOptions();
    $this->assertCount(3, $options);
    $this->assertSame('echo', $options[0]->name);
    $this->assertSame('void', $options[0]->type);
    $this->assertSame('', $options[0]->description);
    $this->assertSame('style', $options[1]->name);
    $this->assertSame('string', $options[1]->type);
    $this->assertSame('Specifiy the output format. Defaults to json', $options[1]->description);
    $this->assertSame('count', $options[2]->name);
    $this->assertSame('int', $options[2]->type);
    $this->assertSame('Indicate the number of items.', $options[2]->description);

    // Echos
    $echos = $info->getEchos();
    $this->assertCount(1, $echos);
    $this->assertSame('The cleaned JSON string if --echo is used', $echos[0]->description);

    // Returns
    $returns = $info->getReturns();
    $this->assertCount(2, $returns);
    $this->assertSame('int', $returns[0]->type);
    $this->assertSame(0, $returns[0]->value);
    $this->assertSame('If the JSON is valid.', $returns[0]->description);
    $this->assertSame('int', $returns[1]->type);
    $this->assertSame(1, $returns[1]->value);
    $this->assertSame('If the JSON is invalid.', $returns[1]->description);

    // Function name
    $this->assertSame('json_set', $info->getName());
  }
}
