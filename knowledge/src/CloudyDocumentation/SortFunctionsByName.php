<?php

namespace AKlump\Knowledge\User\CloudyDocumentation;

class SortFunctionsByName {

  public function __invoke(FunctionInfoInterface $a, FunctionInfoInterface $b) {
    return strcasecmp($a->getName(), $b->getName());
  }

}
