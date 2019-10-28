help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  tag            to modify the version, update changelog, and chag tag"
	@echo "  clean          to remove build artifacts"
	@echo "  package        to build the phar and zip files"

package:
	php -d phar.readonly=0 -d date.timezone="Africa/Accra" build/packager.php

clean:
	rm -rf artifacts/*

