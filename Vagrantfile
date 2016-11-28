VAGRANTFILE_API_VERSION = "2"

path = "#{File.dirname(__FILE__)}"

require 'yaml'
require path + '/scripts/homestead.rb'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.network "public_network", ip: "10.0.0.3", bridge: 'en0: Ethernet'
  Homestead.configure(config, YAML::load(File.read(path + '/Homestead.yaml')))
end
