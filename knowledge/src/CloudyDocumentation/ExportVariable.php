<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class ExportVariable {

  public $name = '';

  public $description = '';

  public $type = '';

  public function __construct(string $name, string $description, string $type = \AKlump\Knowledge\User\CloudyDocumentation\VarTypes::STRING) {
    $this->name = $name;
    $this->description = $description;
    $this->type = $type;
  }

}
