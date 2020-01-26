#!/usr/bin/python
import os
import sys
import time

readers = { 'application/vnd.ms-excel':'xls2txt',
		'application/msword':'catdoc',
		'application/octet-stream':'catdoc',
		'application/vnd.ms-office':'xls2txt',
		'application/vnd.oasis.opendocument.text': 'odt2txt',
		'application/pdf':'pdftotext',
		'text':'text',
		'otros':'None'}

def typeFile(file_name):
	try:
		command = 'file -i '
		out = os.popen(command + file_name)
		aux = out.read()
		out.close()
		type = aux.split(': ')[1].split('; ')[0]
		if type[:4] == 'text':
			type = 'text'
		if not type in readers.keys():
			type = 'otros'
	except Exception, e:
		type = 'otros'
	return type

def getBaseName(file_name):
	file_name = '"' + file_name + '"'
	command = 'basename '
	out = os.popen(command + file_name)
	base_name = out.read().replace('\n', '')
	out.close()
	return base_name 

def getDirName(file_name):
	file_name = '"' + file_name + '"'
	command = 'dirname '
	out = os.popen(command + file_name)
	base_name = out.read().replace('\n', '')
	out.close()
	return base_name 

def getReader(type):
	try:
		reader = readers[type]
	except:
		reader = readers['otros']
	return reader

def transform2Txt(file_name, type):
	reader = getReader(type)
	if reader != 'text':
		file_read = file_name + '.txt'
		if reader == 'pdftotext':
			command = "/opt/drainware/bin/" + reader + ' "' + file_name + '"'
		else:
			#command = reader + ' "' + file_name + '" > "' + file_read + '"'
			command = "/opt/drainware/bin/" + reader + ' "' + file_name + '" > "' +  file_read + '" 2> error.dw || cat error.dw'
		out = os.popen(command)
		content = out.read()
		out.close()	
		os.remove('error.dw')
		if content.find('No Workbook found') >= 0:
			reader = 'catppt'
			command = "/opt/drainware/bin/" + reader + ' "' + file_name + '" > "' + file_read + '"'
			out = os.popen(command)
			out.close()
		os.remove(file_name)
		os.rename(file_read, file_name)
	
def getSsDeep(file_name):
	command = '/opt/drainware/bin/ssdeep -s "' + file_name + '" '
	out = os.popen(command)
	result = out.readlines()
	out.close()
	result = result[1].split(',')[0]
	return result

def copyFile(src):
	dst = getDirName(src) + '/' + getBaseName(src).replace(' ', '') + '.dw'
	out = os.popen('cp "' + src + '" "' + dst + '" > /dev/null')
	out.close()
	return dst

def ssDeepFile(file_name):
	dw_file_name = copyFile(file_name)
	type = typeFile(dw_file_name)
	if type != 'otros':
		transform2Txt(dw_file_name, type)
		ssdeep = getSsDeep(dw_file_name)
	else:
		ssdeep = 'unknown'
	try: os.remove(dw_file_name)
	except: pass
	print ssdeep

def usage():
	print "Usage:"
	print "%s filename" % (sys.argv[0])
	exit()

def main():
	argc = len(sys.argv)
	if argc != 2:
		usage()
	else:
		ssDeepFile(sys.argv[1])

if __name__ == '__main__':
    main()
