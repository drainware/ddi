#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pymongo import MongoClient
from time import sleep
from datetime import date, datetime, timedelta

conexion = MongoClient('mongo')

db = conexion['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')

db = conexion['cloud']
user_coll = db['users'] 

db = conexion['ddi']
group_coll = db['groups']


criteria = dict()
criteria['type'] = 'premium'
criteria['expiry'] = dict()
criteria['expiry']['$lt'] = datetime.now() - timedelta(days=3)

for cloud_user in user_coll.find(criteria):
	try:
		print cloud_user['_id'], ':', cloud_user['license'] , '->', cloud_user['email']

		user_coll.update({'_id' : cloud_user['_id'] }, {'$set' : { 'type' : 'freemium' } }, True)
		
		default_group = group_coll.find_one({ 'license' : cloud_user['license'], 'name' :  'default' })
		group_coll.update({'_id' : default_group['_id'] }, {'$set' : { 'screenshot_severity' : 'low' } }, True)

		###################################### Refresh DLP and ATP Policies ############################################
		connection = pika.BlockingConnection( pika.ConnectionParameters( host = 'rabbitmq' ) )
		channel = connection.channel()
		channel.exchange_declare( exchange = 'server', type = 'direct',  durable = False )
		channel.basic_publish(exchange = 'server', routing_key = cloud_user['license'], properties = pika.BasicProperties( delivery_mode = 1, ), body = '{ "id" : "", "command" : "refresh", "args" : "" }' )
		connection.close()
		
	except Exception, e:
		print "Error: ", cloud_user
