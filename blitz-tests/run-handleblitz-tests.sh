#!/bin/bash

TEST_PHP_EXECUTABLE=/usr/bin/php /usr/bin/php ./run-tests.php -n -d include_path="../src/" handleblitz-tests
