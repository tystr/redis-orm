node default {
  $project_root = '/vagrant'

  yumrepo { 'webtatic':
    descr      => 'Webtatic Repository $releasever - $basearch',
    mirrorlist => 'http://repo.webtatic.com/yum/el6/$basearch/mirrorlist',
    gpgcheck   => 0,
    enabled    => 1,
    priority   => 1
  }
  yumrepo { "epel":
    descr      => 'epel',
    mirrorlist => 'http://mirrors.fedoraproject.org/mirrorlist?repo=epel-6&arch=$basearch',
    enabled    => 1,
    gpgcheck   => 0
  }
  Yumrepo <| |> -> Package <| |> -> File <| |>

  # PHP
  $php_packages = [
    'php55w',
    'php55w-intl',
    'php55w-xml',
    'php55w-mbstring',
    'php55w-fpm',
    'php55w-opcache',
    'php55w-pdo',
    'php55w-mysqlnd',
  ]
  package { $php_packages:
    ensure => latest
  }

  file { '/etc/php.ini':
    source => '/vagrant/provisioning/puppet/manifests/files/php.ini',
    owner  => 'root',
    group  => 'root',
    mode   => '0644'
  }


  # Composer
  exec { 'install-composer':
    command => 'curl -sS https://getcomposer.org/installer | php',
    cwd     => $project_root,
    user    => 'vagrant',
    path    => ['/usr/bin'],
    creates => "${project_root}/composer.phar"
  }
  Package[$php_packages] -> Exec['install-composer']

  # Composer install
  package { 'git':
    ensure => latest
  }
  exec { 'composer-install-dependencies':
    command     => 'php composer.phar install --prefer-source -o -n',
    environment => ['COMPOSER_HOME=/vagrant'],
    cwd         => $project_root,
    user        => 'vagrant',
    path        => ['/usr/bin'],
    timeout     => 600
  }
  Exec['install-composer'] ->
  Package['git'] ->
  Exec['composer-install-dependencies']

  # Firewall
  service { 'iptables':
    enable => true,
    ensure => running
  }
  file { '/etc/sysconfig/iptables':
    ensure => present,
    source => '/vagrant/provisioning/puppet/manifests/files/iptables',
    owner  => 'root',
    group  => 'root',
    mode   => '0600'
  }
  File['/etc/sysconfig/iptables'] ~> Service['iptables']

  # Redis
  package { 'redis':
    ensure => installed
  }
  service { 'redis':
    enable => true,
    ensure => running
  }
  Package['redis'] ~> Service['redis']
}
