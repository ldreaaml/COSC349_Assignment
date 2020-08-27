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
        firstVM.vm.network "forwarded_port", guest: 80, host: 8888, host_ip: "127.0.0.1"

#        ping -c3 192.168.2.20

        # private network for communications between multiple VMs
        firstVM.vm.network "private_network", ip: "192.168.2.20"

        # synced folder is used to share directory between host machine and guest VMs
        firstVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
        
        firstVM.vm.provision "shell", inline: <<-SHELL
      #install apache package
     apt-get update
      apt-get install -y apache2 php libapache2-mod-php php-mysql
            
      # Change VM's webserver's configuration to use shared folder.
      # (Look inside test-website.conf for specifics.)
      cp /vagrant/test-website.conf /etc/apache2/sites-available/
      # activate our website configuration ...
      a2ensite test-website
      # ... and disable the default website provided with Apache
      a2dissite 000-default
      # Reload the webserver configuration, to pick up our changes
      service apache2 reload

SHELL
end
    
    # second VM - for admin 
        # second VM - for adminwebserver
        config.vm.define "secondVM" do |secondVM|
            secondVM.vm.hostname = "secondVM"

            # run on port 80 on the guest VM, and map to port 8081 on the host
            # user only allow access via local host ip
            secondVM.vm.network "forwarded_port", guest: 80, host: 8889, host_ip: "127.0.0.1"

    #        ping -c3 192.168.2.21

            # private network for communications between multiple VMs
            secondVM.vm.network "private_network", ip: "192.168.2.21"

            # synced folder is used to share directory between host machine and guest VMs
            secondVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
            
            secondVM.vm.provision "shell", inline: <<-SHELL
          #install apache package
         apt-get update
          apt-get install -y apache2 php libapache2-mod-php php-mysql
                
          # Change VM's webserver's configuration to use shared folder.
          # (Look inside test-website.conf for specifics.)
          cp /vagrant/test-website1.conf /etc/apache2/sites-available/
          # activate our website configuration ...
          a2ensite test-website1
          # ... and disable the default website provided with Apache
          a2dissite 000-default
          # Reload the webserver configuration, to pick up our changes
          service apache2 reload

    SHELL
        
    end
    
    # third VM- for db
    config.vm.define "thirdVM" do |thirdVM|
        thirdVM.vm.hostname = "third-machine"
        thirdVM.vm.network "private_network", ip: "192.168.2.22"
        thirdVM.vm.synced_folder ".","/vagrant", owner: "vagrant", group: "vagrant", mount_options: ["dmode=775,fmode=777"]
thirdVM.vm.provision "shell", inline: <<-SHELL
      # Update Ubuntu software packages.
      apt-get update
      
      # We create a shell variable MYSQL_PWD that contains the MySQL root password
      export MYSQL_PWD='insecure_mysqlroot_pw'

      # If you run the `apt-get install mysql-server` command
      # manually, it will prompt you to enter a MySQL root
      # password. The next two lines set up answers to the questions
      # the package installer would otherwise ask ahead of it asking,
      # so our automated provisioning script does not get stopped by
      # the software package management system attempting to ask the
      # user for configuration information.
      echo "mysql-server mysql-server/root_password password $MYSQL_PWD" | debconf-set-selections 
      echo "mysql-server mysql-server/root_password_again password $MYSQL_PWD" | debconf-set-selections

      # Install the MySQL database server.
      apt-get -y install mysql-server

      # Run some setup commands to get the database ready to use.
      # First create a database.
      echo "CREATE DATABASE timezone;" | mysql

      # Then create a database user "clouduser" with the given password.
      echo "CREATE USER 'clouduser'@'%' IDENTIFIED BY 'insecure_db_pw';" | mysql

      # Grant all permissions to the database user "clouduser" regarding
      # the "timezone" database that we just created, above.
      echo "GRANT ALL PRIVILEGES ON timezone.* TO 'clouduser'@'%'" | mysql
      
      # Set the MYSQL_PWD shell variable that the mysql command will
      # try to use as the database password ...
      export MYSQL_PWD='insecure_db_pw'

      # ... and run all of the SQL within the setup-database.sql file,
      # which is part of the repository containing this Vagrantfile, so you
      # can look at the file on your host. The mysql command specifies both
      # the user to connect as (clouduser) and the database to use (timezone).
      cat /vagrant/setup-database.sql | mysql -u clouduser timezone

      # By default, MySQL only listens for local network requests,
      # i.e., that originate from within the dbserver VM. We need to
      # change this so that the webserver VM can connect to the
      # database on the dbserver VM. Use of `sed` is pretty obscure,
      # but the net effect of the command is to find the line
      # containing "bind-address" within the given `mysqld.cnf`
      # configuration file and then to change "127.0.0.1" (meaning
      # local only) to "0.0.0.0" (meaning accept connections from any
      # network interface).
      sed -i'' -e '/bind-address/s/127.0.0.1/0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf

      # We then restart the MySQL server to ensure that it picks up
      # our configuration changes.
      service mysql restart
    SHELL
   
 end

end
