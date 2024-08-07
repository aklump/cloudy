<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

/**
 * Represents a documentation for a single function.
 */
class FunctionInfo implements FunctionInfoInterface, \JsonSerializable {

  protected $name = '';

  protected $summary = '';

  protected $description = '';

  protected $exports = [];

  protected $globals = [];

  protected $parameters = [];

  protected $options = [];

  protected $echos = [];

  protected $returns = [];

  public function addExport(ExportVariable $export): void {
    $this->exports[] = $export;
  }

  public function getExports(): array {
    return $this->exports;
  }

  public function addGlobal(FunctionGlobal $global): void {
    $this->globals[] = $global;
  }

  public function getGlobals(): array {
    return $this->globals;
  }

  /**
   * Return the value of Summary.
   *
   * @return mixed
   *   Lorem.
   */
  public function getSummary(): string {
    return !is_null($this->summary) ? $this->summary : '';
  }

  /**
   * Set the value of Summary.
   *
   * @param mixed $summary
   *   Lorem.
   *
   * @return FunctionInfo
   *   Lorem.
   */
  public function setSummary($summary) {
    $this->summary = $summary;

    return $this;
  }

  /**
   * Get a value by key.
   *
   * This is used by Twig.
   *
   * @param string $key
   *   The key to ask for.
   *
   * @return mixed
   *   The value.
   */
  public function __get($key) {
    $method = "get$key";

    return $method();
  }

  public function getOptions(): array {
    return $this->options;
  }

  public function addParameter(FunctionParameter $param): void {
    $this->parameters[] = $param;
  }

  public function addOption(FunctionOption $option): void {
    $this->options[] = $option;
  }

  public function addReturn(FunctionReturn $return): void {
    $this->returns[] = $return;
  }

  public function getReturns(): array {
    return $this->returns;
  }


  public function getName(): string {
    return $this->name;
  }

  public function setName($name): void {
    $this->name = trim($name, '()');
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): void {
    $this->description = $description;
  }

  public function getParameters(): array {
    return $this->parameters;
  }

  public function jsonSerialize() {
    return [
      'name' => $this->getName(),
      'summary' => $this->getSummary(),
      'globals' => $this->getGlobals(),
      'parameters' => $this->getParameters(),
      'options' => $this->getOptions(),
      'echos' => $this->getEchos(),
      'returns' => $this->getReturns(),
      'description' => $this->getDescription(),
    ];
  }

  public function addEcho(FunctionEcho $echo): void {
    $this->echos[] = $echo;
  }

  public function getEchos(): array {
    return $this->echos;
  }
}
