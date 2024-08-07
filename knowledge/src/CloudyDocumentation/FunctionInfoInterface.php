<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

interface FunctionInfoInterface {

  public function getName(): string;

  public function getSummary(): string;

  public function getDescription(): string;

  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\ExportVariable[]
   */
  public function getExports(): array;
  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\FunctionGlobal[]
   */
  public function getGlobals(): array;

  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\FunctionParameter[]
   */
  public function getParameters(): array;

  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\FunctionOption[]
   */
  public function getOptions(): array;

  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\FunctionEcho[]
   */
  public function getEchos(): array;

  /**
   * @return \AKlump\Knowledge\User\CloudyDocumentation\FunctionReturn[]
   */

  public function getReturns(): array;

}
