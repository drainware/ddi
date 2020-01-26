#!/usr/bin/env python
import pika
import json
import sys
import base64
import time

# jose
import syslog




if len(sys.argv) == 2:

	#syslog.syslog("test")
	amqp_options = json.JSONDecoder().decode(sys.argv[1])
        
	amqp_host = str(amqp_options['host'])
	amqp_port = str(amqp_options['port'])
	amqp_exchange = str(amqp_options['exchange'])
	amqp_routing_key = str(amqp_options['routing_key'])
	amqp_message = base64.b64decode(amqp_options['message'])
	
	#credentials = pika.PlainCredentials('guest', 'guest')
	#connection = pika.BlockingConnection(pika.ConnectionParameters(host=amqp_host, credentials=credentials, port=5671, ssl=True, ssl_options=ssl_options))
	connection = pika.BlockingConnection(pika.ConnectionParameters(host=amqp_host, port=5672))
	channel = connection.channel()
	channel.exchange_declare( exchange = amqp_exchange, type = 'direct',  durable = False )
	channel.basic_publish(exchange = amqp_exchange, routing_key = amqp_routing_key, properties = pika.BasicProperties( delivery_mode = 1, ), body = amqp_message )
	connection.close()

else:
	print "Usage python %s amqp_options" % sys.argv[0]


