<?php

$command = $argv[1];
switch ($command) {
  case 'simplest_form':
    break;

  case 'output':
    echo "Green leaves";
    break;

  case 'exit_status':
    fail_because('', '', 64);
    break;

}
