<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class FunctionGlobal {

  public $name = '';

  public $description = '';

  public $type = '';

  public function __construct(string $name, string $description, string $type = VarTypes::STRING) {
    $this->name = $name;
    $this->description = $description;
    $this->type = $type;
  }

}
