###########
Oracle-DB-parameter-monitor
###########

Puppet fact based web tool written in PHP for monitoring:
  * Oracle Database parameter changes
  * Oracle Database FRA usage
  * Oracle Database Switchover/Failover

.. image:: https://raw.githubusercontent.com/Marius2805/Oracle-DB-parameter-monitor/master/screenshots/screenshot_01.jpg
   :alt: Parameter Overview/History
   :width: 1024
   :height: 662
   :align: center
   
.. image:: https://raw.githubusercontent.com/Marius2805/Oracle-DB-parameter-monitor/master/screenshots/screenshot_02.jpg
   :alt: FRA Moinitor
   :width: 1024
   :height: 568
   :align: center

Word of caution
===============

Oracle Parameter Monitor is at a very early step and there a many things witch still needs to be improved.

The application should just give a first approch how Data from Oracle Database cann be visualised.

At the moment no framework, expect JQuery is used, therefore MVC and UI frameworks needs to be implemented. The projects needs also improvment on cronjob handling and support for other databases expect MySQL.

Installation
============
Download the folder 'oracle-parameter-monitor' and extract it to your webserver. Atleast PHP 5.3 is needed.

Application database
----------
The application supports at the moment of writing only MySQL/MariaDB 5. Please create here a new user/schema.
For importing the defualt structure and data please execute first mysql_structure.sql (install folder), then mysql_settings.sql and mysql_parameter.sql.
At last step please configure the database settinggs inside /includes/templates/configuration.php on the webserver



