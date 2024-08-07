<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class FunctionReturn {

  public $value = 0;

  public $description = '';

  public $type = '';

  public function __construct(int $value, string $description) {
    $this->value = $value;
    $this->description = $description;
    $this->type = VarTypes::INTEGER;
  }

}
