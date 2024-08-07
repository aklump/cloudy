<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

use Doctrine\Common\Lexer\AbstractLexer;
use ReflectionClass;

class BashFunctionInfoLexer extends AbstractLexer {

  const T_NAME = 1;

  const T_SUMMARY = 2;

  const T_DESCRIPTION = 3;

  const T_GLOBAL = 4;

  const T_PARAMETER = 5;

  const T_OPTION = 6;

  const T_ECHO = 7;

  const T_RETURN = 8;

  const T_EXPORT = 9;

  protected $hasSummary = FALSE;

  protected $description = '';

  /**
   * @var bool
   */
  private $isCodeBlock;

  /**
   * @inheritDoc
   */
  protected function getCatchablePatterns(): array {
    return [
      'function\s+[^{]+',
      '#.+?\n',
    ];
  }

  /**
   * @inheritDoc
   */
  protected function getNonCatchablePatterns(): array {
    return [
      // This is the '#!/bin...' header.
      '^\#!.+\n',
    ];
  }

  /**
   * @inheritDoc
   */
  protected function getType(&$value): int {
    $tags = $this->getDocBlockTags();
    $tags_regex = '#(' . implode('|', $tags) . ')\s+([^\n]+)#siu';
    preg_match($tags_regex, $value, $matches);
    if ($matches) {
      array_shift($matches);

      return $this->handleDocBlockTagTokens($matches, $value);
    }

    if (preg_match('#function\s+([^({]+)#', $value, $matches)) {
      $value = $matches[1];

      return self::T_NAME;
    }

    if (!preg_match('/^#/', $value)) {
      return 0;
    }

    if (preg_match('/^#!\//', $value)) {
      return 0;
    }

    // Remove the indentation.
    $indent_regex = '#^\#{1,2}\s#';
    $is_indented = preg_match($indent_regex, $value, $matches);
    $indent = $matches[0] ?? '';
    if ($is_indented) {
      $value = substr($value, strlen($indent));
    }

    if (!$this->hasSummary) {
      $value = trim($value, "\n");
      if (!empty($value)) {
        $this->hasSummary = TRUE;

        return self::T_SUMMARY;
      }
    }

    // Every other line will be concatenated into the description.
    if (empty($value)) {
      return 0;
    }

    if (preg_match('#@code#', $value)) {
      $this->isCodeBlock = TRUE;
      $this->description .= "\n\n";
    }
    elseif (preg_match('#@endcode#', $value)) {
      $this->isCodeBlock = FALSE;
    }

    if (!$this->isCodeBlock) {
      $value = rtrim($value, "\n");
    }

    //    $value = preg_replace('#^@(end)?code#', "\n$0", $value);
    if ($this->description) {
      $value = preg_replace('#^\w#', ' $0', $value);
    }
    $this->description .= $value;
    $value = $this->description;
    $value = ltrim($value, "\n");

    return self::T_DESCRIPTION;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->description = '';
    $this->hasSummary = FALSE;
    parent::reset();
  }

  private function handleDocBlockTagTokens(array $parsed, &$value): int {
    list($flag, $line) = $parsed;

    $type = NULL;
    if (in_array($flag, [
      DocBlockTags::EXPORT,
      DocBlockTags::GLOBAL,
      DocBlockTags::PARAM,
      DocBlockTags::OPTION,
      DocBlockTags::RETURN,
    ])) {
      $enum = (new ReflectionClass(VarTypes::class))->getConstants();
      if (preg_match('#^(' . implode('|', $enum) . ') (.+)#i', $line, $matches)) {
        list(, $type, $line) = $matches;
      }
    }

    switch ($flag) {
      case DocBlockTags::EXPORT:
        $name = '';
        if (preg_match('#(\$\S+)(?:\s+([^\n]*))?#', $line, $matches)) {
          $name = $matches[1] ?? '';
          $line = $matches[2] ?? '';
        }
        $value = new ExportVariable($name, $line, $type);

        return self::T_EXPORT;

      case DocBlockTags::GLOBAL:
        $name = '';
        if (preg_match('#(\$\S+)(?:\s+([^\n]*))?#', $line, $matches)) {
          $name = $matches[1] ?? '';
          $line = $matches[2] ?? '';
        }
        $value = new FunctionGlobal($name, $line, $type);

        return self::T_GLOBAL;

      case DocBlockTags::PARAM:
        $type = $type ?? VarTypes::STRING;
        $value = new FunctionParameter($line, $type);

        return self::T_PARAMETER;

      case DocBlockTags::OPTION:
        $value = $this->parseOption($line, $type);

        return self::T_OPTION;

      case DocBlockTags::ECHO:
        $value = new FunctionEcho($line);

        return self::T_ECHO;

      case DocBlockTags::RETURN:
        $value = 0;
        if (preg_match('#(\d+)\s([^\n]*)#', $line, $matches)) {
          $value = (int) $matches[1];
          $line = $matches[2] ?? '';
        }
        $value = new FunctionReturn($value, $line);

        return self::T_RETURN;
      default:
        return 0;
    }
  }

  private function parseOption(string $line, $type): FunctionOption {
    $regex = '#--([^\s=]+)(?:=(\S+))?([^\n]*)#';
    if (!preg_match($regex, $line, $matches)) {
      $regex = '#-(\S)()([^\n]*)#';
      preg_match($regex, $line, $matches);
      $type = 'void';
    }
    $name = $matches[1] ?? '';
    $enum = [];
    if (isset($matches[2])) {
      $enum = array_filter(explode('|', $matches[2]));
    }
    $description = trim($matches[3] ?? '');

    // We'll let the explicit type trump... but if no =value then it's void.
    if (is_null($type) && empty($matches[2])) {
      $type = VarTypes::VOID;
    }
    $type = $type ?? VarTypes::STRING;

    return new FunctionOption($name, $description, $type, $enum);
  }

  private function getDocBlockTags(): array {
    return (new ReflectionClass(DocBlockTags::class))->getConstants();
  }

}
