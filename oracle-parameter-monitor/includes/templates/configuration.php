<?php
    # Database Settings
    $_SESSION['db_host']="localhost";
    $_SESSION['db_user']="oraparacollect";
    $_SESSION['db_password']="";
    $_SESSION['db_database']="oraparacollect";

    # Puppet DB
    $GLOBALS['config']['puppet_db_url'] = '';

    # Import Script
    # token needs to be provied to input.php and notificaton.php cronjob
    $GLOBALS['config']['cronjob_token'] = 'yKUhfxFrGZUeZ2WUJgsbDbyng';

    # Server
    # Server Domain, meanst everything behind the hostname of a server <hostname>.server.example.com
    $GLOBALS['config']['server_domain'] = '';

    # Puppet Facts name standard
    # Define the prefix for database and server related facts
    # Facts for database should be build like this: ora_<sid>_<parameter_name>=<value>
    # Facts related for database host server should be build like this: ora_system_<parameter_name>=<value>
    $GLOBALS['config']['puppet_fact_db_prefix'] = 'ora_p';
    $GLOBALS['config']['puppet_fact_server_prefix'] = 'ora_system';

    # Authentication
    # Authentication
    # ldap_host = Active Directory Controller
    # ldap_user_domain = @example.com
    # ldap_dn = distinguished name
    $GLOBALS['config']['ldap_host'] = '';
    $GLOBALS['config']['ldap_user_domain'] = '';
    $GLOBALS['config']['ldap_dn'] = '';

    # Specify which LDAP Groups are allowed to login
    $GLOBALS['config']['ldap_groups'][] = '';
    $GLOBALS['config']['ldap_groups'][] = '';

    # Notification
    # mail addresses for notification
    $GLOBALS['config']['fra_notification_mail'] = '';
    $GLOBALS['config']['switchover_notification_mail'] = '';
?>