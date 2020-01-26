#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pymongo import MongoClient
from os import system
import sys

conexion = MongoClient('mongo')

#db = conexion['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')

db = conexion['cloud'] 

cloud_users = db['customers'] 

for cloud_user in cloud_users.find():
    try:
        if 'license' not in cloud_user:
            continue
        if 'email' not in cloud_user:
            continue
        print cloud_user['license'] , '->', cloud_user['email']
        command = 'python /ddi/scripts/drs/mapReduce.py ' + cloud_user['license'] 
        system(command)
    except Exception, e:
        print 'Error:', cloud_user

