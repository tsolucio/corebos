#!/bin/bash
HOST=localhost
USER=root
EXCLUDED_TABLES=(
vtiger_audit_trial
vtiger_modtracker_detail
vtiger_modtracker_basic
)

if [ -z "$1" ]
then
	echo "Dump database excluding audit and tracker information. Optionaly will dump only structure with no data."
	echo "To recover the skipped tables you can use the addAuditModtracker.sql file";
	echo "USAGE: $(basename $0) <database_name> [structure]"
	exit
fi

DATABASE=$1
DB_FILE=$1.sql

IGNORED_TABLES_STRING=''
for TABLE in "${EXCLUDED_TABLES[@]}"
do :
   IGNORED_TABLES_STRING+=" --ignore-table=${DATABASE}.${TABLE}"
done

if [ -z "$2" ]
then
	echo "Dump structure and content"
	mysqldump --user=${USER} -p ${DATABASE} ${IGNORED_TABLES_STRING} > ${DB_FILE}
else
	echo "Dump structure"
	mysqldump --user=${USER} -p --single-transaction --no-data ${DATABASE} > ${DB_FILE}
fi
