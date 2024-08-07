<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class FunctionParameter {

  public $description = '';

  public $type = '';

  public function __construct(string $description, string $type = VarTypes::STRING) {
    $this->description = $description;
    $this->type = $type;
  }

}
