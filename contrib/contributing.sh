#!/bin/sh

if [ ! -d ./src ]
	then
		echo "src/ folder missing, source code missing"
		echo "Maybe we are on the gh-pages-source branch?"
		echo "Skipping PHP code cleanup"
		exit 0
	fi

echo "### Paraunit: Tidyng up the src/ code ###"

./bin/phpcs -p --standard=PSR2 --warning-severity=6 --colors src/
if [ $? != 0 ]
	then
		echo "There are some code style issue in the src/ folder."
		echo "Fix them before submitting your code."
		echo "Try running ./bin/phpcbf --standard=PSR2 src/"
		exit 1
	fi

echo "### Paraunit: Tidyng up the tests/ code ###"

./bin/phpcs -p --standard=PSR2 --warning-severity=6 --colors tests/ --ignore=tests/Stub
if [ $? != 0 ]
	then
		echo "There are some code style issue in the tests/ folder."
		echo "Fix them before submitting your code."
		echo "Try running ./bin/phpcbf --standard=PSR2 tests/ --ignore=tests/Stub"
		exit 1
	fi

echo "### Paraunit: DONE! ###"
