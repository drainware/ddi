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
# ROLE			: Return WebFilter policies of each c-icap user.
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

def getPoliciesOfUser(user_data):
	try:
		wf_config = '{}'
		
		user_data_object = json.JSONDecoder().decode(user_data)

		if id_parts.has_key('ip'):
			policy_url = 'http://balancer/ddi/?module=api&action=getWebFilterConfig&ip=' + user_data_object['ip']
		elif id_parts.has_key('username'):
			policy_url = 'http://balancer/ddi/?module=api&action=getWebFilterConfig&user=' + user_data_object['username']
		else:
			policy_url = 'http://balancer/ddi/?module=api&action=getWebFilterConfig&user='

		response = urllib2.urlopen(policy_url)
		wf_config = response.read()
		
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
		
	return wf_config

def on_request(ch, method, props, body):
	
	print " [.] getWFConfig(%s)"  % (data,)
	response = getPoliciesOfUser(data)

	ch.basic_publish(exchange='server', routing_key=props.reply_to, properties=pika.BasicProperties(correlation_id = props.correlation_id, delivery_mode = 1, reply_to=props.reply_to, ), body=str(response))
	ch.basic_ack(delivery_tag = method.delivery_tag)
		
try:
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq', port=5672))
	channel = connection.channel()
	
	channel.exchange_declare(exchange = 'client', durable=True, )
	channel.queue_declare(queue='rpc_wf_queue', durable=True, )
	channel.queue_bind(exchange='client', queue='rpc_wf_queue', routing_key='rpc_wf_queue')

	channel.basic_qos(prefetch_count=1)
	channel.basic_consume(on_request, queue='rpc_wf_queue')

	print " [x] Awaiting RPC WebFilter requests"
	channel.start_consuming()
	

except pika.exceptions.AMQPConnectionError, e:
	print '    [--] AMQP Connection Error: ', e
	os.remove(pidfile)
	pass
except Exception, e:
	print '    [--] Generic exception(02): ', e
	os.remove(pidfile)
	pass		