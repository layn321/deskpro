#!/bin/bash

######################################################
# This is a simple cron runner. This one long-living
# command can be used to ensure the DeskPRO cron job
# is executed every minute, even in environments where
# minutely cron jobs are forbidden.
######################################################
# CONFIG
######################################################

# The DeskPRO cron command to execute
CRON_CMD="/usr/bin/php /path/to/deskpro/cron.php"

# (Seconds) How long this runner should keep going until quitting
DP_TIME_LIMIT=790

######################################################
# DO NOT EDIT
######################################################

# (Seconds) How long between cron executions
DP_TIME_INTERVAL=60

DP_GO=1
DP_TIME_START=0
DP_TIME_END=0
DP_TIME_TAKEN=0
DP_WAIT_TIME=0

while [ $DP_GO -eq 1 ]; do
	let DP_TIME_START=$(date +"%s")
	$CRON_CMD
	let DP_TIME_END=$(date +"%s")
	let DP_TIME_TAKEN=($DP_TIME_END - $DP_TIME_START)

	let DP_TIME_LIMIT=($DP_TIME_LIMIT - $DP_TIME_TAKEN)
	if [ $DP_TIME_LIMIT -gt $DP_TIME_INTERVAL ]; then
		let DP_GO=1
		let DP_WAIT_TIME=($DP_TIME_INTERVAL - $DP_TIME_TAKEN)
		if [ $DP_WAIT_TIME -lt 3 ]; then
			let DP_WAIT_TIME=3
		fi

		let DP_TIME_LIMIT=($DP_TIME_LIMIT - $DP_WAIT_TIME)

		sleep $DP_WAIT_TIME
	else
		let DP_GO=0
	fi
done