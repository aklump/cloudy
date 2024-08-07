#!/usr/bin/env bash

s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

# TODO Need to be running 'cloudy tests' somehow.
./bin/run_unit_tests.sh $@ || exit 1
./bin/run_integration_tests.sh $@ || exit 1
