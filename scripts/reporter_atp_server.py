#!/usr/bin/env python

# 
#=============================================================================
#
# File Name			: atp_reporter_server.py
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
# ROLE			: Register AMQP responses from ATP requests.
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

def insertFile(contents):
	return fs.put(contents)



#####################################  RPC #############################################

def sendEvent(event_data):
	try:
		
		event = {}
		
		data_parts = event_data.split('c2VwYXJhdG9yLmRyYWlud2FyZS5jb20=', 1)
		
		sanbox_data = json.JSONDecoder().decode(data_parts[0])
		sanbox_screenshot = insertScreenshot(data_parts[1]) if len(data_parts) == 2 else None
		
		event['license'] = sanbox_data['license'] # 'xxxx-xxxx-xxxx-xxxx'
		event['datetime'] = sanbox_data['datetime'] #'2012-10-24 17:30:00'
		event['ip'] = sanbox_data['ip'] #'192.168.23.20'
		event['user'] =  sanbox_data['user'] #'csandoval'
		event['origin'] =  sanbox_data['origin'] #'endpoint'
		
		event['processname'] =  sanbox_data['json']['processname'] 
		del sanbox_data['json']['processname']
		
		event['variables'] = dict()
		for key, value in sanbox_data['json'].items():
			event['variables'][key] = value
		event['variables'] = json.JSONEncoder().encode(event['variables'])
		
		event['scid'] = str(sanbox_screenshot)
		event['geodata'] =  sanbox_data['geodata'] 
			
		
			
			
		opener = urllib2.build_opener(MultipartPostHandler.MultipartPostHandler)
		urllib2.install_opener(opener)
		request = urllib2.Request('http://balancerm/ddi/?module=api&action=registerAtpEvents', event)
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
	print " [.] send ATP Event: %s" %(response,)
	
	ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str('ok'))
	ch.basic_ack(delivery_tag = method.delivery_tag)

try:
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port=5672))
	channel = connection.channel()
	
	channel.exchange_declare( exchange = 'client', durable=True, )
	channel.queue_declare(queue='rpc_atp_reporter_queue', durable=True, )
	channel.queue_bind(exchange='client', queue='rpc_atp_reporter_queue', routing_key='rpc_atp_reporter_queue')

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_atp_reporter_queue')

	print " [x] Awaiting RPC requests ATP events"
	channel.start_consuming()
	
except pika.exceptions.AMQPConnectionError, e:
	print '    [--] AMQP Connection Error: ', e
	os.remove(pidfile)
	pass
except Exception, e:
	print '    [--] Generic exception(02): ', e
	os.remove(pidfile)
	pass