#!/usr/bin/env bash

source="${BASH_SOURCE[0]}"
s="${BASH_SOURCE[0]}";[[ "$s" ]] || s="${(%):-%N}";while [ -h "$s" ];do d="$(cd -P "$(dirname "$s")" && pwd)";s="$(readlink "$s")";[[ $s != /* ]] && s="$d/$s";done;__DIR__=$(cd -P "$(dirname "$s")" && pwd)

cd "$__DIR__/.."
php ./knowledge/vendor/aklump/knowledge/bin/handle_page_change.php './knowledge/pages/**/*'
