LOCAL:=./.local
PIP:=$(LOCAL)/bin/pip
PHPENV_ROOT:=$(abspath ./.phpenv)
# PHPENV_ROOT:=~/.phpenv
PHPVERSION:=7.1.8
UNAME=$(strip $(shell uname))

ifeq ($(UNAME),Darwin)
# brew install re2c
# brew install openssl
# brew install bison
# brew install libxml2
# xcode-select --install
#    if => xcode-select: error: command line tools are already installed, use "Software Update" to install updates
#    sudo mv /Library/Developer/CommandLineTools ~/
#    and `xcode-select --install`
#    
PHP_BUILD_CONFIGURE_OPTS="--with-openssl=$$(brew --prefix openssl) --with-libxml-dir=$$(brew --prefix libxml2)"
endif

ifeq ($(UNAME),Linux)
# yum yum groupinstall "Development Tools"
# yum install kernel-devel kernel-headers
#
# yum -y --enablerepo=epel install re2c libmcrypt libmcrypt-devel
# yum -y install libxml2-devel bison bison-devel openssl-devel curl-devel libjpeg-devel libpng-devel libmcrypt-devel readline-devel libtidy-devel libxslt-devel httpd-devel enchant-devel libXpm libXpm-devel freetype-devel t1lib t1lib-devel gmp-devel libc-client-devel libicu-devel oniguruma-devel net-snmp net-snmp-devel bzip2-devel
# PHP_BUILD_CONFIGURE_OPTS="--with-openssl=$$(brew --prefix openssl) --with-libxml-dir=$$(brew --prefix libxml2)"
PHP_BUILD_CONFIGURE_OPTS=""
endif

setup: setup-pip setup-php-and-libs

setup-pip: $(PIP)

$(LOCAL):
	mkdir -p $@
get-pip.py:
	curl -O https://bootstrap.pypa.io/get-pip.py

$(PIP): $(LOCAL) get-pip.py
	export PYTHONUSERBASE=$(LOCAL) && python get-pip.py --user

setup-php-and-libs: ./bin/composer.phar
	php $< install

./bin/composer.phar: setup-php 
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	mkdir -p ./bin
	php composer-setup.php --install-dir=./bin
	php -r "unlink('composer-setup.php');"

setup-php: $(PHPENV_ROOT)/versions/$(PHPVERSION)

set-php-version:
	phpenv local $(PHPVERSION)

.php-version: set-php-version

$(PHPENV_ROOT)/versions/$(PHPVERSION): $(PHPENV_ROOT)/plugins/php-build
	export PHPENV_ROOT=$(PHPENV_ROOT) && \
	export PATH=$(PHPENV_ROOT)/bin:$$PATH && \
	eval "$$(phpenv init -)" && \
	export PHP_BUILD_CONFIGURE_OPTS=$(PHP_BUILD_CONFIGURE_OPTS) && \
	export PHP_BUILD_EXTRA_MAKE_ARGUMENTS=-j4 && \
	phpenv install $(PHPVERSION)

# ./lib/lib/libz.a: zlib-1.2.11
#	cd $<; ./configure --prefix=`pwd`/../lib; make; make install
#zlib-1.2.11: zlib-1.2.11.tar.gz
#	tar xvzf $<
#zlib-1.2.11.tar.gz:
#	curl http://zlib.net/zlib-1.2.11.tar.gz -o $@


$(PHPENV_ROOT)/plugins/php-build: $(PHPENV_ROOT)/plugins
	git clone https://github.com/CHH/php-build.git $(PHPENV_ROOT)/plugins/php-build

$(PHPENV_ROOT)/plugins: ./phpenv/bin/phpenv-install.sh
	export PHPENV_ROOT=$(PHPENV_ROOT) && \
	./phpenv/bin/phpenv-install.sh

./phpenv/bin/phpenv-install.sh:
    git clone https://github.com/CHH/phpenv.git

.PHONY: setup setup-pip setup-php set-php-version $(PHPENV_ROOT)/versions/$(PHPVERSION)
