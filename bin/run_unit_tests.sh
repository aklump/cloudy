#!/usr/bin/env bash
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

cd "$__DIR__/.."

# https://phpunit.readthedocs.io/en/9.5/textui.html#command-line-options
#./dist/vendor/bin/phpunit -c ./tests/Unit/phpunit.xml "$@"
./dist/vendor/bin/phpunit -c ./tests/Unit/phpunit.xml --testdox "$@"
#export XDEBUG_MODE=$XDEBUG_MODE,coverage;./dist/vendor/bin/phpunit -c ./tests/Unit/phpunit.xml "$@" --coverage-html=./tests/Unit/reports
#echo ./tests/Unit/reports/index.html

