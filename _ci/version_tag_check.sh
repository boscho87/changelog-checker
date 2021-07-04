#!/bin/bash
script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

changelog_file=$script_dir/../CHANGELOG.md
v_version=$(cat $changelog_file | grep -Po "(\d+.\d+.\d+)" | head -n1)

if [ "$v_version" != $CI_COMMIT_TAG ]
then
echo "Tag Version stimmt nicht mit $changelog_file überein"
    exit 100
fi
echo "Tag Version stimmt mit dem $changelog_file überein"
exit 0
