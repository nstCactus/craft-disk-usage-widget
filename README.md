# Disk usage dashboard widget for Craft

This plugin adds a new widget type that can be added to the Craft CMS dashboard.
<br>
The widget simply displays the disk usage on your server.


## Requirements

This plugin requires Craft CMS 3.7.0 or later (it may work with previous 3.x
versions, but I haven't tested, and you'll need to tweak the composer.json file).


## Installation

The plugin can be installed from the integrated plugin store by searching for
"Disk usage dashboard widget" or using Composer:

1. Open your terminal and navigate to your Craft project:

       cd /path/to/project

2. Then tell Composer to load the plugin:

       composer require nstcactus/craft-disk-usage-widget

3. Finally, install and enable the plugin:

       ./craft plugin/install disk-usage-widget
       ./craft plugin/enable disk-usage-widget


## Usage

After the plugin has been installed, widgets can be added to the dashboard.
<br>
Several instances of the widget may be added, each one monitoring the disk usage
of a partition.

### Classic mode

The classic way of determining disk usage leverages the `disk_free_space()`
& `disk_total_space()` PHP functions. You need to provide the path to a 
directory in the disk partition you want to monitor. The default value is the
path to the Craft CMS webroot, which is a reasonable value.

In that mode, you may set  **Soft limit**. If the disk usage exceeds that limit,
the widget indicator will turn red.

### Quotas mode

Some hosting providers use disk quotas, especially on shared hosting.

In such cases, the disk usage reported by the widget may be wrong. You can try 
enabling **The server uses disk quotas** setting to circumvent this. When 
enabled, the widget will try to use the `quota -s` shell command to get the disk
usage.

For this to work, your hosting provider has to let you use either the 
`proc_*()` or the `exec()` PHP functions. PHP must also run as the UNIX user the
disk quota is set on.

You need to select the partition to monitor in the **Partition** field. If no
partitions are available the **Partition** dropdown, you're out of luck…

Some hosting providers set the quota & limit to the same value. You can override the soft limit if you need to know when you're almost out of space.
