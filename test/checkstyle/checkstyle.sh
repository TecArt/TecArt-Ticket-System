#!/bin/sh
BASEDIR=$(dirname $(dirname $(dirname $0)))
php $BASEDIR/vendor/phpcheckstyle/phpcheckstyle/run.php --src $BASEDIR \
	--exclude vendor --exclude $BASEDIR/test/checkstyle/ --format console \
	--linecount --config $BASEDIR/test/checkstyle/checkstyle.cfg.xml
