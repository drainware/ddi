#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pymongo import MongoClient
from time import sleep
import pika

conexion = MongoClient('mongo')

db = conexion['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')

db = conexion['cloud']
user_coll = db['customers'] 
db = conexion['ddi']
group_coll = db['groups']

for cloud_user in user_coll.find():
        try:
                print cloud_user['_id'], ':', cloud_user['license'] , '->', cloud_user['email']

		user_events = cloud_user['events']
		user_events['availability'] = True
		user_events['monthly']['general'] = 0
		user_events['monthly']['dlp'] = 0
		user_events['monthly']['atp'] = 0
		user_events['monthly']['forensics'] = 0
		user_events['screenshot'] = True

		user_coll.update({'_id' : cloud_user['_id'] }, {'$set' : { 'events' : user_events } }, True)
		if cloud_user['type'] == "freemium":
			default_group = group_coll.find_one({ 'license' : cloud_user['license'], 'name' :  'default' })
			group_coll.update({'_id' : default_group['_id'] }, {'$set' : { 'screenshot_severity' : 'low' } }, True)			
	except Exception, e:
		print "Error: ", cloud_user



###################################### Refresh DLP and ATP Policies ############################################
connection = pika.BlockingConnection( pika.ConnectionParameters( host = 'rabbitmq' ) )
channel = connection.channel()
channel.exchange_declare( exchange = 'server', type = 'direct',  durable = False )
channel.basic_publish(exchange = 'server', routing_key = '*', properties = pika.BasicProperties( delivery_mode = 1, ), body = '{ "id" : "", "command" : "refresh", "args" : "" }' )
connection.close()
