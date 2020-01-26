#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
#       sin título.py
#       
#       Copyright 2012  <cristian@mordisquitos>
#       
#       This program is free software; you can redistribute it and/or modify
#       it under the terms of the GNU General Public License as published by
#       the Free Software Foundation; either version 2 of the License, or
#       (at your option) any later version.
#       
#       This program is distributed in the hope that it will be useful,
#       but WITHOUT ANY WARRANTY; without even the implied warranty of
#       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#       GNU General Public License for more details.
#       
#       You should have received a copy of the GNU General Public License
#       along with this program; if not, write to the Free Software
#       Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
#       MA 02110-1301, USA.
#       
#       

from pymongo import MongoClient
from re import findall
from os import popen, system
from sys import argv
import re

CONF_SAMBA_FILE_NAME = '/etc/samba/smb.conf'
CONF_KRB5_FILE_NAME = '/etc/krb5.conf'
CONF_SQUID_FILENAME = "/opt/drainware/etc/squid.conf"

##################### Get Ldap & Icap Configuration ####################
	
def getAdvancedConfiguration():
	connection = MongoClient('mongo')
	db = connection['admin']
	db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')
	db = connection['ddi']
	collection = db.configuration
	conf = collection.find_one({"id": "advanced"})
	connection.disconnect()
	return conf


################## Update LDAP & Icap Configuration ####################

def readContentFileConf(file_name):
	fileconf = open(file_name, 'r')
	content = fileconf.readlines()
	fileconf.close()
	return content

def writeContentFileConf(file_name, content):
	fileconf = open(file_name, 'w')
	fileconf.writelines(content)
	fileconf.close()

def grepLine(string, content):
	expr = re.compile(string)
	return filter(expr.search,content)[0]

def commentLine(line):
	line = line.replace('#', '')
	return '#' + line
	
def uncommentLine(line):
	return line.replace('#', '')

def getAuthTypeStatus(auth_type):
  line = grepLine(auth_type, readContentFileConf(CONF_SQUID_FILENAME))
  status = line.replace('\n','').replace('\r','').split(' = ')
  return status[1]

def updateAuthentication(auth_type):
	content = readContentFileConf(CONF_SQUID_FILENAME)
	new_content = list()
	if auth_type == 'local':
		if getAuthTypeStatus('# Local Auth') == 'off':
			nline = 0
			for line in content:
				if line.find('# Local Auth') >= 0:
					line = '# Local Auth = on\n'
					for index in range(1,7):
						content[nline + index] = uncommentLine(content[nline + index])
				if line.find('# Ldap Auth') >= 0:
					line = '# Ldap Auth = off\n'
					for index in range(1,4):
						content[nline + index] = commentLine(content[nline + index])
				if line.find('# Access Local') >= 0:
					content[nline + 1] = uncommentLine(content[nline + 1])
				if line.find('# Access Ldap') >= 0:
					content[nline + 1] = commentLine(content[nline + 1])
				new_content.append(line)
				nline = nline + 1
			writeContentFileConf(CONF_SQUID_FILENAME, new_content)
			restartSquid()
	else:
		if getAuthTypeStatus('# Ldap Auth') == 'off':
			nline = 0
			for line in content:
				if line.find('# Local Auth') >= 0:
					line = '# Local Auth = off\n'
					for index in range(1,7):
						content[nline + index] = commentLine(content[nline + index])
				if line.find('# Ldap Auth') >= 0:
					line = '# Ldap Auth = on\n'
					for index in range(1,4):
						content[nline + index] = uncommentLine(content[nline + index])
				if line.find('# Access Local') >= 0:
					content[nline + 1] = commentLine(content[nline + 1])
				if line.find('# Access Ldap') >= 0:
					content[nline + 1] = uncommentLine(content[nline + 1])
				new_content.append(line)
				nline = nline + 1
			writeContentFileConf(CONF_SQUID_FILENAME, new_content)
			restartSquid()


def createKrb5Conf(ldap_domain, ldap_host):
	krb5 = open(CONF_KRB5_FILE_NAME, 'w')
	krb5.write('[libdefaults]\n')
	krb5.write('       default_realm = ' + ldap_domain.upper() + '\n')
	krb5.write('       clockskew = 300\n')
	krb5.write('\n')
	krb5.write('[realms]\n')
	krb5.write('       ' + ldap_domain.upper() + ' = {\n')
	krb5.write('               kdc = ' + ldap_host + '.' + ldap_domain + '\n')
	krb5.write('               default_domain = ' + ldap_domain + '\n')
	krb5.write('               admin_server = ' + ldap_host + '.' + ldap_domain + '\n')
	krb5.write('       }\n')
	krb5.write('\n')
	krb5.write('[logging]\n')
	krb5.write('       kdc = FILE:/var/log/krb5/krb5kdc.log\n')
	krb5.write('       admin_server = FILE:/var/log/krb5/kadmind.log\n')
	krb5.write('       default = SYSLOG:NOTICE:DAEMON\n')
	krb5.write('\n')
	krb5.write('[domain_realm]\n')
	krb5.write('       .' + ldap_domain + ' = ' + ldap_domain.upper() + '\n')
	krb5.write('\n')
	krb5.write('[appdefaults]\n')
	krb5.write('       pam = {\n')
	krb5.write('               ticket_lifetime = 1d\n')
	krb5.write('               renew_lifetime = 1d\n')
	krb5.write('               forwardable = true\n')
	krb5.write('               proxiable = false\n')
	krb5.write('               minimum_uid = 1\n')
	krb5.write('       }\n')
	krb5.close()

def createSambaConf(ldap_domain):
	content = list()
	for line in readContentFileConf(CONF_SAMBA_FILE_NAME):
		if line.find('realm') >= 0:
			line = '       realm = ' + ldap_domain.upper() + '\n'
		content.append(line)
	writeContentFileConf(CONF_SAMBA_FILE_NAME, content)

def getLdapDomain(content):
	ldap_domain = ''
	content = content.split(',dc=')
	for i in range(1,len(content)):
		ldap_domain = ldap_domain + '.' + content[i]
	ldap_domain = ldap_domain[1:]
	return ldap_domain

def getNameOfHost(ip):
	out = popen('nslookup ' + ip)
	result = out.read()
	out.close()
	coincidences = findall('name = (.*?)\.', result)
	if coincidences != []:
		result = coincidences[0]
	else:
		result = None
	return result
	

def checkJoinDomain():
	check = True
	out = popen('wbinfo -u')
	result = out.read().replace('\n','')
	out.close()
	if result == '' or result == 'Error looking up domain users':
		check = False
	return check

def checkWinbind():
	check = True
	out = popen('/etc/init.d/winbind status')
	result = out.read().replace('\n','')
	out.close()
	if result == 'winbind is not running ... failed!':
		check = False
	return check


def joinServerDomain(user, pswd):
	out = popen('net rpc join -U ' + user + '%' + pswd + ' 2> /dev/null')
	out.close()
	restartWinbind()
	while checkJoinDomain() == False:
		joinServerDomain(user, pswd)

def leaveServerDomain(user,pswd):
	out = popen('net ads leave -U ' + user + '%' + pswd + ' 2> /dev/null')
	out.close()
	restartWinbind()

def restartSquid():
	system('/etc/init.d/squid3 restart')
	#out = popen('/etc/init.d/squid3 restart')
	#out.close()

def restartWinbind():
	out = popen('/etc/init.d/winbind restart')
	out.close()

def ldap_icapConfiguration(command):
	conf = getAdvancedConfiguration()

	ldap_auth = conf['config']['authentication']['value']
	ldap_user = conf['config']['ldap']['user']['value']
	ldap_pswd = conf['config']['ldap']['password']['value']
	ldap_host = conf['config']['ldap']['host']['value']
	ldap_port = conf['config']['ldap']['port']['value']
	ldap_base = conf['config']['ldap']['base']['value']
	
	ldap_domain = getLdapDomain(ldap_base)
	ldap_hostname = getNameOfHost(ldap_host)
	
	if ldap_hostname != None:
		if command == 'join':
			updateAuthentication(ldap_auth)
			if ldap_auth == 'ldap':
				createKrb5Conf(ldap_domain, ldap_hostname)
				createSambaConf(ldap_domain)
				joinServerDomain(ldap_user, ldap_pswd)
				if checkWinbind():
					print 'Joined LDAP Server successfully'
				else:
					ldap_icapConfiguration('join')
			else:
				print 'Change Local successfully'
		else:
			leaveServerDomain(ldap_user, ldap_pswd)
			print 'Leave LDAP Server Successfully'	
	else:
		print 'Can not connet with the LDAP server'
	return 0

def main():	
	argc = len(argv)
	if argc < 2:
		usage()
	if (argc != 2 and argv[1] == 'join') or (argc != 2 and argv[1] == 'leave') :
		usage()
	command = argv[1]
	if command == 'join':
		ldap_icapConfiguration(command)
	elif command == 'leave':
		ldap_icapConfiguration(command)
	else:
		print argv[1]
		usage()
	return 0

def usage():
	print 'Usage:'
	print '%s join | leave' % (argv[0])
	print
	exit()

if __name__ == '__main__':
	main()
