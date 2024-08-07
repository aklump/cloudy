<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class FunctionOption {

  public $name = '';

  public $description = '';

  public $type = '';

  public $enum = [];

  public function __construct(string $name, string $description, string $type = VarTypes::STRING, array $enum = []) {
    $this->name = $name;
    $this->description = $description;
    $this->type = $type;
    $this->enum = $enum;
  }

}
