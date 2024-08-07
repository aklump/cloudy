#!/usr/bin/env bash

function my_bootstrap_boot() {
  echo 'my bootstrap worked'
}

event_listen "boot" "my_bootstrap_boot"
