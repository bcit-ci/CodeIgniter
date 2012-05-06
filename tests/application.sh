#!/bin/bash
if test -f selenium.sh
then
	green='\e[0;32m'
	red='\e[1;31m'
	endColor='\e[0m'
	echo -e "${green} Set Environment... ${endColor}"
	export DISPLAY=:99.0
	sh -e /etc/init.d/xvfb start
	pyrus install http://pear.phpunit.de/get/PHPUnit_Selenium-1.2.6.tgz
	sudo apt-get -qq update
	echo -e "${green} Environment OK ${endColor}"
	echo -e "${green} Set Web Server... ${endColor}"
	sudo apt-get -y -qq install apache2
	sudo apt-get -y -qq install libapache2-mod-php5
	sudo /etc/init.d/apache2 restart
	echo -e "${green} Web Server OK ${endColor}"
	echo -e "${green} Set Up CodeIgniter on Root Directory... ${endColor}"
	sudo mkdir /var/www/codeigniter
	sudo chmod 777 /var/www/codeigniter -R
	sudo cp ../index.php /var/www/codeigniter/index.php
	sudo cp ../application/ /var/www/codeigniter/ -r
	sudo cp ../system/ /var/www/codeigniter/ -r
	echo -e "${green} CodeIgniter OK ${endColor}"
	echo -e "${green} Set Up Selenium... ${endColor}"
	wget http://selenium.googlecode.com/files/selenium-server-standalone-2.21.0.jar
	sudo mkdir /usr/lib/selenium
	sudo cp selenium-server-standalone-2.21.0.jar /usr/lib/selenium/selenium-server-standalone-2.21.0.jar
	sudo cp selenium.sh /etc/init.d/selenium
	sudo chmod 755 /etc/init.d/selenium
	sudo mkdir /var/log/selenium
	sudo chmod a+w /var/log/selenium
	/etc/init.d/selenium start
	/etc/init.d/selenium restart
	ps aux | grep 2.21
	sleep 5	
	echo -e "${green} Selenium OK ${endColor}"
	echo -e "${green} Application Test READY ${endColor}"
else
	echo -e "${red} Application Test NOT READY ${endColor}"
fi