#!/usr/bin/env bash

cd $(dirname $BASH_SOURCE)

if [ $# -eq 0 ]; then
	echo 'Usage: '$BASH_SOURCE' <version_number>'
	exit 1
fi

version_number=$1

if [ ${#version_number} -lt 5 ]
then
	echo "Provided version number is too short"
	exit 1
elif [ ${version_number: -4} == "-dev" ]
then
	echo "'-dev' releases are not allowed"
	exit 1
fi

version_id=${version_number:0:5}
version_id=${version_id//./}
upgrade_rst='user_guide_src/source/installation/upgrade_'$version_id'.rst'

if [ ${#version_id} -ne 3 ]
then
	echo "Invalid version number format"
	exit 1
elif [ `grep -c -F --regexp="'$version_number'" system/core/CodeIgniter.php` -ne 1 ]
then
	echo "Provided version number doesn't match in system/core/CodeIgniter.php"
	exit 1
elif [ `grep -c -F --regexp="'$version_number'" user_guide_src/source/conf.py` -ne 2 ]
then
	echo "Provided version number doesn't match in user_guide_src/source/conf.py"
	exit 1
elif [ `grep -c -F --regexp="$version_number (Current version) <https://codeload.github.com/bcit-ci/CodeIgniter/zip/$version_number>" user_guide_src/source/installation/downloads.rst` -ne 1 ]
then
	echo "user_guide_src/source/installation/downloads.rst doesn't appear to contain a link for this version"
	exit 1
elif [ ! -f "$upgrade_rst" ]
then
	echo "${upgrade_rst} doesn't exist"
	exit 1
fi

echo "Running tests ..."

php -d zend.enable_gc=0 -d date.timezone=UTC -d mbstring.func_overload=7 -d mbstring.internal_encoding=UTF-8 vendor/bin/phpunit --coverage-text --configuration tests/travis/sqlite.phpunit.xml

if [ $? -ne 0 ]
then
	echo "Build FAILED!"
	exit 1
fi

cd user_guide_src/

echo ""
echo "Building HTML docs; please check output for warnings ..."
echo ""

make html

echo ""

if [ $? -ne 0 ]
then
	echo "Build FAILED!"
	exit 1
fi

cd ..

if [ -d user_guide/ ]
then
	rm -r user_guide/
fi

cp -r user_guide_src/build/html/ user_guide/
git add user_guide/

echo "Build complete."
