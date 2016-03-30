#!/bin/sh

echo "### Paraunit: Tidyng up the code ###"

./vendor/bin/phpcs -p --standard=PSR2 --warning-severity=6 --colors src/
if [ $? != 0 ]
	then
		echo "There are some code style issue. Fix them before submitting your code."
		echo "Try running ./vendor/bin/phpcbf --standard=PSR2 src/"
		exit 1
	fi

echo "### Paraunit: DONE! ###"