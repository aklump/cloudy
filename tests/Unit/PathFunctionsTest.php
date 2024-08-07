<?php

namespace AKlump\Cloudy\Tests\Unit;

use AKlump\Cloudy\Tests\Unit\TestingTraits\TestWithFilesTrait;
use PHPUnit\Framework\TestCase;

/** @var string $CLOUDY_CORE_DIR */

require_once CLOUDY_CORE_DIR . '/php/cloudy.api.php';
require_once CLOUDY_CORE_DIR . '/php/cloudy.functions.php';

/**
 * @covers path_is_yaml
 * @covers path_extension
 * @covers path_filename
 * @covers path_is_absolute
 * @covers path_make_absolute
 * @covers path_make_relative
 * @covers path_make_pretty
 * @covers path_filesize
 */
class PathFunctionsTest extends TestCase {

  use TestWithFilesTrait;

  public function testResolvePathGlobsDoubleGlob() {
    $expected = $this->getTestFileFilepath('/lorem/ipsum/dolar.csv', TRUE);
    $result = _cloudy_resolve_path_globs($this->getTestFileFilepath('**.csv'));
    $this->assertSame($expected, $result[0]);
    $this->deleteAllTestFiles();
  }

  public function testResolvePathGlobsSingleGlob() {
    $expected = $this->getTestFileFilepath('lorem.md', TRUE);
    $result = _cloudy_resolve_path_globs($this->getTestFileFilepath('*.md'));
    $this->assertSame($expected, $result[0]);
    $this->deleteAllTestFiles();
  }

  public function testPathExtension() {
    $this->assertSame('json', path_extension('config.json'));
    $this->assertSame('json', path_extension('/foo/bar/config.json'));
    $this->assertSame('twig', path_extension('config.html.twig'));
  }

  public function testPathFilename() {
    $this->assertSame('config', path_filename('config.json'));
    $this->assertSame('config', path_filename('do/re/mi/config.json'));
  }

  public function testPathFilesize() {
    $this->createMirrorOfBashTestStubDirectory();
    $subject = $this->getTestFileFilepath('tests/stubs/charlie.md');
    $this->assertSame(446, path_filesize($subject));
  }

  public function testPathIsAbsolute() {
    $this->assertTrue(path_is_absolute('/do/re'));
    $this->assertFalse(path_is_absolute('do/re'));
  }

  public function testPathIsYaml() {
    $this->assertTrue(path_is_yaml('foo.yaml'));
    $this->assertTrue(path_is_yaml('foo.yml'));
    $this->assertTrue(path_is_yaml('foo.YAML'));
    $this->assertTrue(path_is_yaml('foo.YML'));
    $this->assertFalse(path_is_yaml('foo.json'));
  }

  public function testPathMakeRelativeEchosNothingSends1WhenCannotMakeRelative() {
    $exit_status = 0;
    $result = path_make_relative('/foo', '/bar', $exit_status);
    $this->assertEmpty($result);
    $this->assertSame(1, $exit_status);
  }

  public function testPathMakeRelativeRemovesTrailingSlash() {
    $this->assertSame('foo', path_make_relative('/bar/baz/foo/', '/bar/baz'));
  }

  public function testPathMakeRelativeEchosRelativeSends0AsExpected() {
    $exit_status = 0;

    $result = path_make_relative('/some/great/path/tree.md', '/some/great', $exit_status);
    $this->assertSame('path/tree.md', $result);
    $this->assertSame(0, $exit_status);

    $result = path_make_relative('/some/great/path/tree.md', '/some/great/', $exit_status);
    $this->assertSame('path/tree.md', $result);
    $this->assertSame(0, $exit_status);
  }

  public function testPathMakeRelativeEchosDotWhenBothArgsAreTheSame() {
    $exit_status = 0;

    $result = path_make_relative('/foo/bar', '/foo/bar', $exit_status);
    $this->assertSame('.', $result);
    $this->assertSame(0, $exit_status);

    $result = path_make_relative('/foo/bar/', '/foo/bar', $exit_status);
    $this->assertSame('.', $result);
    $this->assertSame(0, $exit_status);
  }

  public function testPathMakeRelativeEchosRealpath() {
    $this->createMirrorOfBashTestStubDirectory();
    $ROOT = rtrim($this->getTestFilesDirectory(), DIRECTORY_SEPARATOR);

    $this->assertSame("tests", path_make_relative("$ROOT/tests/stubs/../../tests", "$ROOT"));
    $this->assertSame("bogus/stubs/../../tests", path_make_relative("$ROOT/bogus/stubs/../../tests", "$ROOT"));

    $this->deleteAllTestFiles();
  }

  public function testPathMakeAbsoluteEchosNothingSends1WhenFirstIsNotRelative() {
    $exit_status = 0;
    $result = path_make_absolute('/foo', '/bar', $exit_status);
    $this->assertEmpty($result);
    $this->assertSame(1, $exit_status);
  }

  public function testPathMakeAbsoluteEchosNothingSends2WhenSecondIsNotAbsolute() {
    $exit_status = 0;
    $result = path_make_absolute('foo', 'bar', $exit_status);
    $this->assertEmpty($result);
    $this->assertSame(2, $exit_status);
  }

  public function testPathMakeAbsoluteEchosAbsoluteSends0AsExpected() {
    $exit_status = 0;

    $result = path_make_absolute('foo', '/bar/baz', $exit_status);
    $this->assertSame(0, $exit_status);
    $this->assertSame('/bar/baz/foo', $result);

    $result = path_make_absolute('foo', '/bar/baz/', $exit_status);
    $this->assertSame(0, $exit_status);
    $this->assertSame('/bar/baz/foo', $result);
  }

  public function testPathMakeAbsoluteRemovesTrailingSlash() {
    $this->assertSame('/bar/baz/foo', path_make_absolute('foo/', '/bar/baz'));
  }

  public function testPathMakeAbsoluteEchosRealpath() {
    $this->createMirrorOfBashTestStubDirectory();
    $ROOT = rtrim($this->getTestFilesDirectory(), DIRECTORY_SEPARATOR);

    $this->assertSame("$ROOT/tests", path_make_absolute('tests/stubs/../../tests', "$ROOT"));
    $this->assertSame("$ROOT/bogus/stubs/../../tests", path_make_absolute("bogus/stubs/../../tests", "$ROOT"));

    $this->deleteAllTestFiles();
  }

  public function testPathMakePretty() {
    $this->assertSame('/foo/bar/file.md', path_make_pretty('/foo/bar/file.md'));
    $this->assertSame('./file.md', path_make_pretty(getcwd() . '/file.md'));
  }

}
