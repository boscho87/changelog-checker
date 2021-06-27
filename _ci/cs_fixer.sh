#!/bin/bash
script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
dir=$script_dir/../

./vendor/bin/php-cs-fixer fix $dir/tests --using-cache=no --diff
./vendor/bin/php-cs-fixer fix $dir/src --dry-run --using-cache=no --diff
if [ "$?" != 0 ]
then
./vendor/bin/php-cs-fixer fix $dir/tests --using-cache=no --diff
./vendor/bin/php-cs-fixer fix $dir/src --using-cache=no --diff
    exit 100
fi

if grep -Enr 'var_dump\(' ${dir}src/ | grep -Enr 'var_dump\(' ${dir}tests/
then
    echo "remove the var_dump() listed in the comments above"
    exit 100;
fi

echo "CS Fixer finished"
exit $?0
