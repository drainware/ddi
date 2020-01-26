#!/bin/sh

rm -f /opt/drainware/data/db/mongod.lock
/etc/init.d/mongodb start