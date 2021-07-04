#!/bin/bash
export XDEBUG_CONFIG="xdebug.mode=coverage"
script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
dir=$script_dir/../
$dir/vendor/bin/phpunit --coverage-html ./test_doc/coverage --testdox-html ./test_doc/index.html --coverage-text --group unit
sensible-browser $dir/test_doc/coverage/index.html
