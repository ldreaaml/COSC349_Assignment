# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
    
    config.vm.box = "ubuntu/xenial64"
 
    # first VM - for webserver
    config.vm.define "firstVM" do |firstVM|
        firstVM.vm.hostname = "firstVM"

        # run on port 80 on the guest VM, and map to port 8080 on the host
        # user only allow access via local host ip
        firstVM.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"

#        ping -c3 192.168.2.20

        # private network for communications between multiple VMs
        firstVM.vm.network "private_network", ip: "192.168.2.20"

        # synced folder is used to share directory between host machine and guest VMs
        firstVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
        
        firstVM.vm.provision "shell", inline: <<-SHELL
      #install apache package
      apt-get update
    apt-get install -y apache2

    # Change VM's webserver's configuration to use shared folder.
    # (Look inside test-website.conf for specifics.)
    cp /vagrant/test-website.conf /etc/apache2/sites-available/
    # install our website configuration and disable the default
    a2ensite test-website
    a2dissite 000-default
    service apache2 reload
SHELL
end
    
    # second VM - for database 
    config.vm.define "secondVM" do |secondVM|
        secondVM.vm.hostname = "secondVM"
        secondVM.vm.network "private_network", ip: "192.168.2.21"
        secondVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
        
    end
    
    # third VM
    config.vm.define "thirdVM" do |thirdVM|
        thirdVM.vm.hostname = "third-machine"
        thirdVM.vm.network "private_network", ip: "192.168.2.22"
        thirdVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
    end

end
