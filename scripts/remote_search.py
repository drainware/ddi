#!/usr/bin/env python
import pika
import urllib2
import sys


connection = pika.BlockingConnection(pika.ConnectionParameters(host='rabbitmq'))
channel = connection.channel()
channel.exchange_declare(exchange='direct_policies', type='direct', durable=True)

group = sys.argv[1]
message = sys.argv[2]

	
channel.basic_publish(exchange='direct_policies', routing_key=group, properties=pika.BasicProperties(delivery_mode = 2,), body=message)

print " [x] Sent request of search: %r => %r" % (group, message)

connection.close()
