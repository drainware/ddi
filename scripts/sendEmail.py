#!/usr/bin/env python
# -*- coding: utf-8 -*-

import urllib
import urllib2
import MultipartPostHandler
from pymongo import MongoClient
from datetime import *
from time import *

def sendEmail(type, id):
	data={'type': type, 'id': id}
	
	opener = urllib2.build_opener(MultipartPostHandler.MultipartPostHandler)
	urllib2.install_opener(opener)
	request = urllib2.Request('http://balancer/ddi/?module=api&action=sendEmail', data)
	response = urllib2.urlopen(request)

conexion = MongoClient('mongo')

#db = conexion['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')

db = conexion['cloud']
user_coll = db['customers']

now = datetime.now()
today = now.strftime("%Y-%m-%d") + ' 00:00:00'

today_7d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=7)
today_6d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=6)
criteria = { 'registered': { '$gte': today_7d, '$lt': today_6d },  'deployed': False, 'email': { '$ne': 'cpd@diputacionavila.es'}}

for cloud_user in user_coll.find(criteria):
	print '%s[%s]: %s -> %s' % (cloud_user['_id'], cloud_user['registered'], cloud_user['license'], cloud_user['email'])
	sendEmail('miss_you', str(cloud_user['_id']))


today_14d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=14)
today_13d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=13)
criteria = { 'registered': { '$gte': today_14d, '$lt': today_13d },  'deployed': False, 'email': { '$ne': 'cpd@diputacionavila.es'}}

for cloud_user in user_coll.find(criteria):
	print '%s[%s]: %s -> %s' % (cloud_user['_id'], cloud_user['registered'], cloud_user['license'], cloud_user['email'])
	sendEmail('miss_you', str(cloud_user['_id']))

today_21d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=21)
today_20d = datetime.strptime(today, "%Y-%m-%d %H:%M:%S") - timedelta(days=20)

criteria = { 'registered': { '$gte': today_21d, '$lt': today_20d }, '1stpolicy': False, 'email': { '$ne': 'cpd@diputacionavila.es'}}

for cloud_user in user_coll.find(criteria):
	print '%s[%s]: %s -> %s' % (cloud_user['_id'], cloud_user['registered'], cloud_user['license'], cloud_user['email'])
	sendEmail('1st_policy', str(cloud_user['_id']))




