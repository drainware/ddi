#!/usr/bin/env python

# 
#=============================================================================
#
# File Name			: reporter_forensics_server.py
# Author			: Cristian Sandoval
# Creation Date		: March 2012
#
#
#
#=============================================================================
#
# PRODUCT			: DRAINWARE PLATFORM
#
# MODULE			: DDI
#
# ROLE			: Register AMQP responses from forensics requests.
#
#
# DEPENDANCE SYS.	: 
#
# ---------------------------------------------------------------------------
# This document is the property of Drainware : no part of it shall be
# reproduced or transmitted without the express prior written authorization
# of Drainware and its contents shall not be disclosed.
# (c) Copyright 2013, Drainware Systems S.L.
# ---------------------------------------------------------------------------
#
#=============================================================================
#



import pika
import urllib
import urllib2
import MultipartPostHandler
import json
import tempfile
import os
import signal
import sys
import gridfs

import threading

from pymongo import MongoClient


#####################################  SERVICE #############################################

def signal_handler(signal, frame):
	os.remove(pidfile)
	sys.exit(0)

try:
	if len(sys.argv) != 3:
		print "Usage python %s --pidfile pid_file" % sys.argv[0]
		sys.exit(-10)

	signal.signal(signal.SIGINT, signal_handler)

	global pidfile 
	pidfile = sys.argv[2]
	pid = str(os.getpid())

	if os.path.isfile(pidfile):
		print "%s already exists, exiting" % pidfile 
		sys.exit()
	else:
		file(pidfile, 'w').write(pid)
		
except Exception, e:
	print '    [--] Generic exception(01): ', e
	pass



#####################################  gridfs #############################################

conn = MongoClient('mongo')

#db = conn['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')
db = conn['storage']

fs = gridfs.GridFS(db)

def insertRemoteFile(contents):
	#return fs.put(contents.encode('utf-8'))
	return fs.put(contents)


#####################################  RPC #############################################

def registerForensicsEvent(event_data):
	try:
		fevent = dict()

		data = event_data.split('c2VwYXJhdG9yLmRyYWlud2FyZS5jb20=', 1)
		forensics_data = json.JSONDecoder().decode(data[0])['response']
		#print "=============="
		#print data[1]
		#print "=============="
		forensics_file = str(insertRemoteFile(data[1])) if len(data) > 1 else None
		
		print forensics_data

		fevent['license'] 	= forensics_data['license']		if forensics_data.has_key('license') 	else None
		fevent['ip'] 		= forensics_data['ip']
		fevent['device'] 	= forensics_data['machine']
		fevent['user'] 		= forensics_data['user'] 		if forensics_data.has_key('user')	 	else None
		fevent['command']	= forensics_data['command']
		fevent['code']		= forensics_data['code']		if forensics_data.has_key('code') 		else None
		fevent['id']		= forensics_data['id']			if forensics_data.has_key('id') 		else forensics_data['query']	 			if  forensics_data.has_key('query')  	else None
		fevent['payload']	= forensics_data['payload'] 	if forensics_data.has_key('payload') 	else dict(enumerate(forensics_data['resultset'])) if forensics_data.has_key('resultset') 	else None
		fevent['geodata']	= forensics_data['geodata'] 	if forensics_data.has_key('geodata') 	else None
		fevent['datetime'] 	= forensics_data['datetime']	if forensics_data.has_key('datetime') 	else None

		if not fevent['payload'] is None:
			if not forensics_file is None:
				fevent['payload']['contents'] = forensics_file
			fevent['payload'] = json.JSONEncoder().encode(fevent['payload'])
			
		if not fevent['geodata'] is None:
			fevent['geodata'] = json.JSONEncoder().encode(fevent['geodata'])
		
		opener = urllib2.build_opener(MultipartPostHandler.MultipartPostHandler)
		urllib2.install_opener(opener)
		request = urllib2.Request('http://balancer/ddi/?module=api&action=registerRemoteQueryResults', fevent)
		response = urllib2.urlopen(request)

	except urllib2.HTTPError, e:
		print '    [--] HTTPError: ', e.code
		pass
	except urllib2.URLError, e:
		print '    [--] URLError: ', e.reason
		pass
	except ValueError, e:
		print '    [--] JSON Value Error: ', e
		pass
	except KeyError, e:
		print '    [--] JSON Validation Error: ', e
		pass
	except Exception, e:
		print '    [--] Generic exception(03): ', e
		pass
	
	return fevent

def on_request(ch, method, props, body):
	response = registerForensicsEvent(body)
	print "\n [.] send Remote Search Result:(%s)" %(response,)

	ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str('ok'))
	ch.basic_ack(delivery_tag = method.delivery_tag)


def receive_command():

  #try:
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port=5672))
	channel = connection.channel()

	channel.exchange_declare( exchange = 'client', durable=True, )
	channel.queue_declare(queue='rpc_reporter_remote_search_queue', durable=True, )
	channel.queue_bind(exchange='client', queue='rpc_reporter_remote_search_queue', routing_key='rpc_reporter_remote_search_queue')

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_reporter_remote_search_queue')

	print " [x] Register Results Remote Search"
	channel.start_consuming()

  #except pika.exceptions.AMQPConnectionError, e:
  #	print '    [--] AMQP Connection Error: ', e
  # 	os.remove(pidfile)
  #	pass
  #except Exception, e:
  #	print '    [--] Generic exception(02): ', e
  #	os.remove(pidfile)
  #	pass

def start():
  t_msg = threading.Thread(target=receive_command)
  t_msg.start()
  t_msg.join(0)
  #self.receive_command()

start()

