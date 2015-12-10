=== Rebuild Pages from Majestic ===
Contributors: NiteoWeb Ltd.
Tags: ebn csv import, ebn majestic csv import

Grab URL from CSV files and create pages for all those URL's


== Description ==
Rebuild pages with backlinks by importing Majestic CSV export.

= Features =


== Screenshots ==



== Installation ==

Installing the plugin:

1.  Unzip the plugin's directory into `wp-content/plugins`.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  The plugin will be available under Tools -> EBN Importer on
    WordPress administration page.


== Usage ==

Click on the EBN Importer link on your WordPress admin page, choose the
file you would like to import, fill minimum backlinks required by any URL 
for which page would be created and click Import. The `examples` directory
inside the plugin's directory contains one file that demonstrate
how to use the plugin. The best way to get started is to import the 
file and look at the results.

CSV is a tabular format that consists of rows and columns. 

= Basic post information =

= Custom fields =


= General remarks =

*   WordPress pages [don't have categories or tags][pages].


This plugin uses [php-csv-parser][3] by Kazuyoshi Tlacaelel.
It was inspired by Denis Kobozev [CSV Importer] plugin.

Contributors:

[3]: http://code.google.com/p/php-csv-parser/


== Changelog ==

= v0.1.0 =
*   Initial version of the plugin

