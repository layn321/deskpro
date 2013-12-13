#!/bin/bash

###############################################
# Correct line endings
###############################################

fromdos /vm_local/init/checkout_deskpro.sh
fromdos /vm_local/init/install_deskpro.sh

cp /vm_local/dpcmd /usr/bin/dpcmd
fromdos /usr/bin/dpcmd
chmod +x /usr/bin/dpcmd

###############################################
# Make log files / directories writable
###############################################

touch /var/log/php_errors.log
chmod 0777 /var/log/php_errors.log

# Dirs on /dev/shm
mkdir /dev/shm/deskpro-cache
mkdir /dev/shm/deskpro-logs
mkdir /dev/shm/deskpro-tmp

chown root:vagrant /dev/shm/deskpro-cache
chmod 0777 /dev/shm/deskpro-cache
chmod g+s /dev/shm/deskpro-cache

chown root:vagrant /dev/shm/deskpro-logs
chmod 0777 /dev/shm/deskpro-logs
chmod g+s /dev/shm/deskpro-logs

chown root:vagrant /dev/shm/deskpro-tmp
chmod 0777 /dev/shm/deskpro-tmp
chmod g+s /dev/shm/deskpro-tmp

mkdir /deskpro-data
mkdir /deskpro-data/backups
mkdir /deskpro-data/debug
mkdir /deskpro-data/files

chown -R root:vagrant /deskpro-data
chmod -R 0777 /deskpro-data
chmod g+s /deskpro-data /deskpro-data/backups /deskpro-data/debug /deskpro-data/files

# Links paths to /dev/shm
ln -s /dev/shm/deskpro-cache /deskpro-cache
ln -s /dev/shm/deskpro-logs /deskpro-data/logs
ln -s /dev/shm/deskpro-tmp /deskpro-data/tmp

sudo usermod -a -G vagrant www-data
sudo usermod -a -G www-data vagrant

###############################################
# Write /etc/rc.local to re-create /dev/shm dirs on boot
###############################################

echo "#!/bin/sh -e" > /etc/rc.local

echo "mkdir /dev/shm/deskpro-cache" >> /etc/rc.local
echo "chown root:vagrant /deskpro-cache" >> /etc/rc.local
echo "chmod 0777 /dev/shm/deskpro-cache" >> /etc/rc.local
echo "chmod g+s /dev/shm/deskpro-cache" >> /etc/rc.local

echo "mkdir /dev/shm/deskpro-logs" >> /etc/rc.local
echo "chown root:vagrant /dev/shm/deskpro-logs" >> /etc/rc.local
echo "chmod 0777 /dev/shm/deskpro-logs" >> /etc/rc.local
echo "chmod g+s /dev/shm/deskpro-logs" >> /etc/rc.local

echo "mkdir /dev/shm/deskpro-tmp" >> /etc/rc.local
echo "chown root:vagrant /dev/shm/deskpro-tmp" >> /etc/rc.local
echo "chmod 0777 /dev/shm/deskpro-tmp" >> /etc/rc.local
echo "chmod g+s /dev/shm/deskpro-tmp" >> /etc/rc.local

echo "exit 0" >> /etc/rc.local

###############################################
# Small changes to config
###############################################

echo "Increasing apc memory"
echo "" >> /etc/php5/conf.d/apc.ini
echo "apc.shm_size = 100M" >> /etc/php5/conf.d/apc.ini

###############################################
# Installing node / npm
###############################################

echo "Installing nodejs"
sudo apt-get update
sudo apt-get install --yes python-software-properties
sudo add-apt-repository --yes ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install --yes nodejs

echo "Installing grunt"
npm install -g grunt-cli

echo "Installing bower"
npm install -g bower

echo "Installing logio"
npm install -g log.io
cp -R /root/.log.io /home/vagrant
chown -R vagrant:vagrant /home/vagrant/.log.io
rm /home/vagrant/.log.io/harvester.conf
cp /vm_local/init/conf/logio-harvester.conf /home/vagrant/.log.io/harvester.conf

###############################################
# Installing Selenium, Firefox and xvfb
###############################################

# Note: Versions are important here
# - Selenium 2.35 wasn't working, so I reverted to 2.31
# - OpenJDK wasn't working properly, so I installed the latest Oracle Java
# - Support for Firefox 24 was buggy so I reverted to Firefox 21

add-apt-repository -y ppa:webupd8team/java
apt-get update

# xvfb is a virtual display required by firefox
# we're installing firefox itself here too so the appropriate dependencies are installed
# but we still fetch version 21 specifically below
apt-get install -y xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic xvfb x11-apps imagemagick firefox

# Pre-accept Oracle's licensing
echo debconf shared/accepted-oracle-license-v1-1 select true | sudo debconf-set-selections
echo debconf shared/accepted-oracle-license-v1-1 seen true | sudo debconf-set-selections

# Install oracle java from the repos we added above
apt-get install -y oracle-java7-installer

# Download and install firefox
cd /usr/local/bin
wget -O firefox-21.tar.bz2 'https://ftp.mozilla.org/pub/mozilla.org/firefox/releases/21.0/linux-x86_64/en-US/firefox-21.0.tar.bz2'
tar jxvf firefox-21.tar.bz2
rm /usr/bin/firefox
ln -s /usr/local/bin/firefox/firefox /usr/bin/firefox

# Download and install selenium
mkdir /usr/local/bin/selenium
cd /usr/local/bin/selenium
wget http://selenium.googlecode.com/files/selenium-server-standalone-2.31.0.jar

###############################################
# Init'ing MySQL
###############################################

echo "Setting MySQL root password to 'deskpro' and creating initial 'deskpro' database"
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS deskpro; CREATE DATABASE IF NOT EXISTS deskpro_testing; GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'deskpro' WITH GRANT OPTION; GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'deskpro' WITH GRANT OPTION; FLUSH PRIVILEGES;"

###############################################
# Init'ing DeskPRO
###############################################

echo "Checking out DesKPRO files"
if [ ! -d /deskpro/www/app ];then
	/bin/bash /vm_local/init/checkout_deskpro.sh
fi

echo "Installing DeskPRO"
if [ ! -f /deskpro/www/config ];then
	/bin/bash /vm_local/init/install_deskpro.sh
fi

echo "Installing DeskPRO cron job"
echo "* * * * * www-data php /deskpro/www/cron.php" >> /etc/crontab

echo "Restarting webserver"
service nginx restart
service php5-fpm restart