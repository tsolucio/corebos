# change to location of PHP binary
PHP=php

all:

test:
	$(PHP) -d zend.enable_gc=1 vendor/phpunit/phpunit/composer/bin/phpunit --configuration phpunit.xml-dist
