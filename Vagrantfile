# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "nrel-CentOS-6.4-x86_64"
  config.vm.box_url = "http://developer.nrel.gov/downloads/vagrant-boxes/CentOS-6.4-x86_64-v20131103.box"
  config.vm.hostname = "tystr-redis-orm.dev"

  config.vm.network :private_network, ip: "192.168.42.42"
  config.vm.synced_folder ".", "/vagrant", :nfs => true

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "2048"]
    vb.customize ["modifyvm", :id, "--cpus", "4"]
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "provisioning/puppet/manifests"
    puppet.manifest_file  = "site.pp"
  end
end