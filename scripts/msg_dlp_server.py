#!/usr/bin/env python

# 
#=============================================================================
#
# File Name			: msg_dlp_server.py
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
# ROLE			: Register users and return DLP policies of each endpoint user.
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

def getPoliciesOfUser(license, username):
	try:
		policies_json = '[]'
		
		if username != None:
			username = urllib2.unquote(username).decode('utf8')
			if license != None:
				url_group = 'http://balancer/ddi/?module=api&action=getGroupsOfUser&license=' + license + '&user=' + username
				url_policy = 'http://balancer/ddi/?module=api&action=getDlpGroupConfig&license=' + license + '&group='
			else:
				url_group = 'http://balancer/ddi/?module=api&action=getGroupsOfUser&user=' + username
				url_policy = 'http://balancer/ddi/?module=api&action=getDlpGroupConfig&group='
			
			response = urllib2.urlopen(url_group)
			groups =json.JSONDecoder().decode(response.read())

			policies = list()
			for group in groups:
				response = urllib2.urlopen(url_policy + group)
				policy = json.JSONDecoder().decode(response.read())
				policies.append(policy)
			
			policies_json = json.JSONEncoder().encode(policies)		

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
		print '    [--] Generic exception(04): ', e
		pass
	
	return policies_json

def on_request(ch, method, props, body):
		
	license = None
	username = None
	
	try:
		body_parts = json.JSONDecoder().decode(body)
		
		if body_parts.has_key('license'):
			license = body_parts['license']
		
		username = body_parts['username']

		print " [.] getPoliciesOfUser(%s: %s)"  % (license, username)
		dlp_config = getPoliciesOfUser(license, username)
		
		dlp_config_object = json.JSONDecoder().decode(dlp_config)
		message_object = { 'id' : '' , 'module' : 'dlp', 'command' : 'set',  'args' : dlp_config_object }
		message = json.JSONEncoder().encode(message_object)
		
		ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str(message))
		
	except ValueError, e:
		print e
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
	
	channel.exchange_declare( exchange = 'client', durable=True)
	channel.queue_declare(queue='rpc_dlp_queue', durable=True)
	channel.queue_bind(exchange='client', queue='rpc_dlp_queue', routing_key='rpc_dlp_queue')

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_dlp_queue')

	print " [x] Awaiting RPC DLP requests"
	channel.start_consuming()

except pika.exceptions.AMQPConnectionError, e:
	print '    [--] AMQP Connection Error: ', e
	os.remove(pidfile)
	pass
except Exception, e:
	raise
	print '    [--] Generic exception(02): ', e
	os.remove(pidfile)
	pass

