#!/bin/bash

PATH=/usr/kerberos/bin:/opt/tvd/Basenv/tvdperl-5.8.4_64/bin:/u01/app/oracle/product/rdbms/11.2.0.3.6/bin:/u01/app/oracle/product/rdbms/11.2.0.3.6/ctx/bin:/usr/bin:/usr/local/bin:/sbin:/usr/sbin:/opt/tvd/Basenv/dba/bin:/opt/tvd/Basenv/tvdusr/bin:/bin:/u01/app/admindb/bin:/home/oracle/bin

. /opt/tvd/Basenv/.profile 1>/dev/null 2>/dev/null
source /u01/app/admindb/lib/collect_functions.lib

setup_tmp_dir collect_oracle_parameter_$RANDOM
generate_prod_db_list

while read server_parameter; do
	collect_all_parameter $(echo $server_parameter)
	
	if [ "$(grep -i 'procedure successfully completed' $tmp_dir/sql_output)" != "" ]; then
		cat $tmp_dir/sql_output | grep -i ora_ | grep -v =$
	fi
done < $tmp_dir/prod_db_list

collect_system_parameter

cleanup
