#!/usr/bin/python

import re
import os
import sys

SQUID_FILENAME = "/opt/drainware/etc/squid.conf"

def readFile(file_name):
	dwfile = open(file_name, 'r')
	content = dwfile.readlines()
	dwfile.close()
	return content

def writeFile(file_name, content):
	dwfile = open(file_name, 'w')
	dwfile.writelines(content)
	dwfile.close()

def commentLine(line):
	line = line.replace('#', '')
	return '#' + line
	
def uncommentLine(line):
	return line.replace('#', '')

def grepLine(string,content):
	expr = re.compile(string)
	return filter(expr.search,content)[0]

def reloadSquid3():
	os.system("/etc/init.d/squid3 reload")

def usage():
	print "Usage:"
	print "%s get" % (sys.argv[0])
	print "%s set [groups/unique] " % (sys.argv[0])
	print
	exit()


def getFilterType():
  line = grepLine('Access Unique Policy', readFile(SQUID_FILENAME))
  status = line.replace('\n','').replace('\r','').split(' = ')
  if status[1] == 'on':
    filter_type = "unique"
  else:
    filter_type = "groups"
  return filter_type

def changeGroupsPolitic():
	if(getFilterType() != 'groups'):
		content = readFile(SQUID_FILENAME)
		new_content = list()
		access = '# Access Local'
		nline = 0
		for line in content:
			if line.find('# Local Auth = on') >= 0:
				access = '# Access Local'
			if line.find('# Ldap Auth = on') >= 0:
				access = '# Access Ldap'
			if line.find(access) >= 0:
				content[nline + 1] = uncommentLine(content[nline + 1])
			if line.find('# Access Unique Policy = on') >= 0:
				line = '# Access Unique Policy = off\n'
				content[nline + 1] = commentLine(content[nline + 1])
			new_content.append(line)
			nline = nline + 1
		writeFile(SQUID_FILENAME, new_content)
	

def changeUniquePolitic():
	if(getFilterType() != 'unique'):
		content = readFile(SQUID_FILENAME)
		new_content = list()
		nline = 0
		for line in content:
			if line.find('# Access Local') >= 0:
				content[nline + 1] = commentLine(content[nline + 1])
			if line.find('# Access Ldap') >= 0:
				content[nline + 1] = commentLine(content[nline + 1])
			if line.find('# Access Unique Policy = off') >= 0:
				line = '# Access Unique Policy = on\n'
				content[nline + 1] = uncommentLine(content[nline + 1])
			new_content.append(line)
			nline = nline + 1
		writeFile(SQUID_FILENAME, new_content)

def main():
	argc = len(sys.argv)

	if argc < 2:
		usage()

	if (argc != 3 and sys.argv[1] == "set") or (argc != 2 and sys.argv[1] == "get") :
		usage()
	command = sys.argv[1]
	if command == "set":
		filter_type = sys.argv[2]
		if filter_type == "groups":
			changeGroupsPolitic()
			reloadSquid3()
		elif filter_type == "unique":
			changeUniquePolitic()
			reloadSquid3()
		else:
			usage()
	elif command == "get":
		print getFilterType() 
	else:
		print sys.argv[1]
		usage()

if __name__ == '__main__':
	main()

