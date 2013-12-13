Exec { path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ] }

exec { 'apt-get-update':
	command => 'apt-get update',
	path    => '/usr/bin/',
	timeout => 60,
	tries   => 3,
}

class php ($version = 'latest') {
	package { [ "php5", "php5-cli", "php5-dev", "php5-fpm", "php5-mysql", "php5-curl", "php5-gd", "php-apc", "php5-xdebug", "php5-intl", "php5-mcrypt", "php5-imagick", "php-pear"]:
		ensure       => $version,
		before       => File['/etc/php5/cli/php.ini'],
		require      => Exec['apt-get-update'],
	}

	file {['/etc/php5/fpm/php.ini', '/etc/php5/cli/php.ini']:
		ensure       => file,
		owner        => 'root',
		require      => Package['php5-fpm', 'php5-cli'],
		content      => template("config/php.ini"),
	}

	file {'/etc/php5/conf.d/xdebug.ini':
		ensure       => present,
		require      => Package['php5-xdebug'],
		content      => template("config/xdebug.ini"),
	}

	file {'/etc/php5/fpm/pool.d/www.conf':
		ensure      => present,
		require     => Package['nginx', 'php5-fpm'],
		content     => template("config/php5-fpm-www.conf"),
	}

	service {'php5-fpm':
		ensure      => running,
		enable      => true,
		require     => Package['php5', 'php5-fpm'],
		subscribe   => File['/etc/php5/fpm/php.ini', '/etc/php5/fpm/pool.d/www.conf'],
	}

	exec { 'install-composer':
		command     => 'curl -sS https://getcomposer.org/installer | php && /bin/mv composer.phar /usr/local/bin/composer',
		unless      => 'test -f /usr/local/bin/composer',
		path        => '/usr/bin',
		require     => Package['php5-cli', 'curl'],
	}
}

class nginx ($version = 'latest') {
	package {'nginx':
		ensure      => $version,
		before      => File['/etc/nginx/nginx.conf'],
		require     => Exec['apt-get-update'],
	}

	package {'apache2':
		ensure      => absent,
		before      => Package['nginx']
	}

	file {'/etc/nginx/nginx.conf':
		ensure        => file,
		owner         => 'www-data',
		content       => template("config/nginx.conf"),
	}

	file {'/etc/nginx/sites-available/default':
		ensure        => present,
		require       => Package['nginx', 'php5-fpm'],
		content       => template("config/nginx-default-host.ini"),
	}

	file {'/deskpro/www':
		ensure        => "directory"
	}

	service {'nginx':
		ensure        => running,
		enable        => true,
		subscribe     => File['/etc/nginx/nginx.conf', '/etc/nginx/sites-available/default'],
	}
}

class mysql5 ($version = 'latest') {

	$mysqlPackages = ['mysql-server', 'mysql-common', 'mysql-client']

	package { $mysqlPackages:
		ensure        => $version,
		before        => File['/etc/mysql/my.cnf'],
		require       => Exec['apt-get-update'],
	}

	file {'/etc/mysql/my.cnf':
		ensure        => file,
		owner         => 'root',
		content       => template("config/my.cnf")
	}

	service {'mysql':
		ensure        => running,
		enable        => true,
		subscribe     => File['/etc/mysql/my.cnf']
	}
}

class dev ($version = 'latest') {
	$devPackages = [ "tofrodos", "curl", "git", "rubygems", "htop", "imagemagick", "ruby", "python" ]

	package { $devPackages:
		ensure => installed,
		require => Exec['apt-get-update'] ,
	}
}

include mysql5
include nginx
include php
include dev