# Credits

php_opc_apcu_ is based on php_apc_ (https://github.com/vivid-planet/munin-php-apc/)

# Goals

The goal was to be as consistent as possible with the apc_apc_ graphs.
Although there are a number of PHP OpCache Munin plugins, they:
  - only monitored a small number of PHP OpCache statistics
  - did not support APCU which now runs parallel to PHP Opcache
  - did not support multiple servers which may run under different FastCGI instances


# Requirements

The `php_apc_` plugin has been tested on Munin v2.0.63

The Munin homepage can be found at: http://munin-monitoring.org/


# Documentation

(TBD)


# Installation and Usage

(TBD)


# Archive Contents

The complete project archive contains the following files:

    php_opc_apcu    _   - the php_opc_apcu_ Munin plugin.
    opc_apcu_info.php   - the PHP script that is called by the plugin.
    CHANGELOG.txt       - a list of changes made to the project.
    README.txt          - this file.


# Todo

  - verify all graphs and remove any that are no longer relevant
  - add additional PHP OpCache graphs, such as restarts and key usage
  - add a "suggest" section showing the list of graphs that can be generated
  - include instructions for overrding thresholds set by the plugin


# Licensing

`php_apc_` is licensed under the [MIT License][2].


[1]: http://munin-monitoring.org/wiki/Documentation "Munin Documentation"
[2]: http://www.opensource.org/licenses/mit-license.php "MIT License"

