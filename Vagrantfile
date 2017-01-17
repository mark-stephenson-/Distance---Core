VAGRANTFILE_API_VERSION = "2"

path = "#{File.dirname(__FILE__)}"

require 'yaml'
require path + '/scripts/homestead.rb'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  Homestead.configure(config, YAML::load(File.read(path + '/Homestead.yaml')))

  config.vm.provider :aws do |aws, override|
    settings = YAML::load(File.read(path + '/Homestead.yaml'))

    # Dummy Vagrant box provided for Vagrant AWS
    override.vm.box_url = "https://github.com/mitchellh/vagrant-aws/raw/master/dummy.box"
    override.vm.box = "dummy"

    aws.access_key_id = "AKIAIP3NXRNFHNPGT7TA"
    aws.secret_access_key = "6DRRcnkv9CTfwceaXNimsfDEzsuB+xApaLPWlCVx"

    # Set the AMI, and the region to Sydney
    aws.ami = "ami-57eae033"
    aws.region = "eu-west-2"
    aws.instance_type = "t2.micro"

    aws.security_groups = ["vagrant"]
    aws.keypair_name = "prase-staging"

    aws.tags = {
    'Name' => settings["name"]
    }

    override.ssh.username = "ubuntu"
    override.ssh.private_key_path = "certificates/prase-staging.pem"

    settings["folders"].each do |folder|
      # Don't sync `node_modules` or `vendor`, else we'll make provisioning super slow
      override.vm.synced_folder folder["map"], folder["to"], type: "rsync",
        rsync__exclude: ["node_modules/", "vendor/"]
    end
  end
end


