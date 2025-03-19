#!/bin/sh

usage() {
	echo "Get diff for CodeIgniter two versions"
	echo " usage: $0 <old_zip> <new_zip>"
	echo "    eg: $0 CodeIgniter-3.0.0.zip CodeIgniter-3.0.1.zip"
}

if [ $# -eq 0 ]; then
	usage
	exit
fi

f1="$1"
f2="$2"
v1=${f1%.*}
v2=${f2%.*}

rm -rf "$v1" "$v2"

unzip "$v1"
unzip "$v2"

rm -rf "$v1/user_guide"
rm -rf "$v2/user_guide"
diff -uwbrN "$v1" "$v2" > "$v1-$v2.diff"

# Please add files which you modify.
list="
index.php
system/core/Loader.php
system/core/Input.php
system/core/Common.php
system/core/CodeIgniter.php
system/helpers/url_helper.php
"

diff="$v1-$v2.ci-phpunit-test-only.diff"
echo -n > "$diff"

for i in $list
do
	diff -uwb "$v1/$i" "$v2/$i" >> "$diff"
done
