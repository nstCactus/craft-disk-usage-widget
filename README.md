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
