#!/usr/bin/env python

# 
#=============================================================================
#
# File Name			: dlp_reporter_server.py
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
# ROLE			: Register AMQP responses from DLP requests.
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

def insertScreenshot(contents):
	return fs.put(contents)



#####################################  RPC #############################################

def sendEvent(data):
	try:
		event = {}
		data_parts = data.split('c2VwYXJhdG9yLmRyYWlud2FyZS5jb20=', 1)
		
		event_data = data_parts[0]
		print event_data
		event_data = json.JSONDecoder().decode(event_data)
		
		if not event_data['license'] is None:
			event['license'] = event_data['license']
		event['datetime'] = event_data['datetime']
		event['ip'] = event_data['ip']
		event['user'] =  event_data['user']
		event['origin'] =  event_data['origin']
		if 'geodata' in event_data.keys():			
			if not event_data['geodata'] is None:
				event['geodata'] =  json.JSONEncoder().encode(event_data['geodata']) 
		event['json'] =   json.JSONEncoder().encode(event_data['json'])

		if len(data_parts) == 2:
			event_image = data_parts[1]
			scid = insertScreenshot(event_image)
			event['scid'] = str(scid)

		opener = urllib2.build_opener(MultipartPostHandler.MultipartPostHandler)
		urllib2.install_opener(opener)
		request = urllib2.Request('http://balancer/ddi/?module=api&action=registerJsonEvents', event)
		response = urllib2.urlopen(request)

	except urllib2.HTTPError, e:
		print '    [--] HTTPError: ', e.code
		pass
	except urllib2.URLError, e:
		print '    [--] URLError: ', e.reason
		pass
	except ValueError, e:
		print e
		pass
	except KeyError, e:
		print '    [--] JSON Validation Error: ', e
		pass
	except Exception, e:
		print '    [--] Generic exception(03): ', e
		pass
		
	return event


def on_request(ch, method, props, body):
	response = sendEvent(body)
	print " [.] send DLP Event: %s" %(response,)
	
	ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str('ok'))
	ch.basic_ack(delivery_tag = method.delivery_tag)

try:
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port=5672))
	channel = connection.channel()
	
	channel.exchange_declare( exchange = 'client', durable=True)
	channel.queue_declare(queue='rpc_dlp_reporter_queue', durable=True, )
	channel.queue_bind(exchange='client', queue='rpc_dlp_reporter_queue', routing_key='rpc_dlp_reporter_queue')	

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_dlp_reporter_queue')

	print " [x] Awaiting RPC requests DLP events"
	channel.start_consuming()
	
except pika.exceptions.AMQPConnectionError, e:
	print '    [--] AMQP Connection Error: ', e
	os.remove(pidfile)
	pass
except Exception, e:
	print '    [--3] Generic exception(02): ', e
	os.remove(pidfile)
	pass
