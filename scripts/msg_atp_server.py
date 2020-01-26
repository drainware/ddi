#!/usr/bin/env python

# 
#=============================================================================
#
# File Name			: msg_atp_server.py
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
# ROLE			: Return ATP policies of each endpoint user.
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
import urllib2
import json
import os
import signal
import sys


##########################################################################  SERVICE ##################################################################################

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



##########################################################################  RPC ##################################################################################

def getAtpConfig(license):
	try:
		atp_config = '{}'
		response = urllib2.urlopen('http://balancer/ddi/?module=api&action=getAtpConfig&license=' + license)
		atp_config = response.read()
		
	except urllib2.HTTPError, e:
		print '    [--] HTTPError: ', e.code
		pass
	except urllib2.URLError, e:
		print '    [--] URLError: ', e.reason
		pass
	except Exception, e:
		print '    [--] Generic exception(04): ', e
		pass
		
	return atp_config

def on_request(ch, method, props, body):
	
	license = None
	
	try:	

		body_parts = json.JSONDecoder().decode(body)
		
		if body_parts.has_key('license'):
			license = body_parts['license']
		
		print " [.] getATPConfig(%s)" % (license,)
		atp_config = getAtpConfig(license)
		#print "     [+] policies: (%s)" % (atp_config,)

		atp_config_object = json.JSONDecoder().decode(atp_config)
		message_object = { 'id' : '' , 'module' : 'atp', 'command' : 'set',  'args' : atp_config_object }
		message = json.JSONEncoder().encode(message_object)

		ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str(message))
		
	except ValueError, e:
		print '    [--] JSON Value Error: ', e
		pass
	except KeyError, e:
		print '    [--] JSON Validation Error: ', e
		pass	
	except TypeError, e:
		print '    [--] Type Error: ', e
		pass			
	except Exception, e:
		print '    [--] Generic exception(03): ', e
		pass	
	finally:
		ch.basic_ack(delivery_tag = method.delivery_tag)
		
try:
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port=5672))
	channel = connection.channel()
	
	channel.exchange_declare(exchange = 'client', durable=True, )
	channel.queue_declare(queue='rpc_atp_queue', durable=True, )
	channel.queue_bind(exchange='client', queue='rpc_atp_queue', routing_key='rpc_atp_queue')

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_atp_queue')

	print " [x] Awaiting RPC ATP requests"
	channel.start_consuming()

except pika.exceptions.AMQPConnectionError, e:
	print '    [--] AMQP Connection Error: ', e
	os.remove(pidfile)
	pass
except Exception, e:
	print '    [--] Generic exception(02): ', e
	os.remove(pidfile)
	pass	
