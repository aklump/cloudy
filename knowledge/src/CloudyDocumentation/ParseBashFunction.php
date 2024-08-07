<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class ParseBashFunction {

  /**
   * Parse a BASH function snippet into a FunctionInfoInterface
   *
   * @param string $docblock
   *   A snippet extracted from a BASH script.
   *
   * @return \AKlump\LoftDocs\FunctionInfo
   *
   * @see \AKlump\Knowledge\User\CloudyDocumentation\ExtractFunctionsFromBashCode
   */
  public function __invoke(string $docblock): FunctionInfoInterface {
    if (empty($docblock)) {
      return new FunctionInfo();
    }

    $function_doc = new FunctionInfo();
    $lexer = new BashFunctionInfoLexer();
    $lexer->setInput($docblock);
    $lexer->moveNext();
    while ($token = $lexer->lookahead) {
      $lexer->moveNext();
      switch ($token->type) {
        case BashFunctionInfoLexer::T_NAME:
          $function_doc->setName($token->value);
          break;

        case BashFunctionInfoLexer::T_SUMMARY:
          $function_doc->setSummary($token->value);
          break;

        case BashFunctionInfoLexer::T_DESCRIPTION:
          $function_doc->setDescription($token->value);
          break;

        case BashFunctionInfoLexer::T_EXPORT:
          $function_doc->addExport($token->value);
          break;

        case BashFunctionInfoLexer::T_GLOBAL:
          $function_doc->addGlobal($token->value);
          break;

        case BashFunctionInfoLexer::T_PARAMETER:
          $function_doc->addParameter($token->value);
          break;

        case BashFunctionInfoLexer::T_OPTION:
          $function_doc->addOption($token->value);
          break;

        case BashFunctionInfoLexer::T_ECHO:
          $function_doc->addEcho($token->value);
          break;

        case BashFunctionInfoLexer::T_RETURN:
          $function_doc->addReturn($token->value);
          break;
      }
    }

    return $function_doc;
  }

}

