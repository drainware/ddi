#!/usr/bin/python
from os import system, popen

WEBFILTER_CONFIG_FILE = '/opt/drainware/etc/dwfilter.conf'
DLP_CONFIG_FILE = '/opt/drainware/etc/dwDLP.conf'
CLAMAV_CONFIG_FILE = '/opt/drainware/etc/squidclamav.conf'

def restartCIcap():
	system("/etc/init.d/c-icap restart")

def restartNetworking():
	system("/etc/init.d/networking restart")
	
def readContentFile(file_name):
	dwft = open(file_name, 'r')
	content = dwft.readlines()
	dwft.close()
	return content

def writeContentFile(file_name, content):
	dwft = open(file_name, 'w')
	for line in content:
		dwft.write(line)
	dwft.close()

def getCurrentIP():
	out = popen("cat /etc/network/interfaces | grep address | awk '{ print $2 }' ")
	ip = out.read().replace('\n','')
	out.close()
	return ip

def changeWebFilterConfFile():
	content = list()
	for line in readContentFile(WEBFILTER_CONFIG_FILE):
		if line.find('dwfilter.BlockingPage') >= 0:
			line = 'dwfilter.BlockingPage http://' + getCurrentIP() + '/ddi/public/blocking_page/index.php\n'
		content.append(line)
	writeContentFile(WEBFILTER_CONFIG_FILE, content)

def changeDLPConfFile():
	content = list()
	for line in readContentFile(DLP_CONFIG_FILE):
		if line.find('dwDLP.BlockingPage') >= 0:
			line = 'dwDLP.BlockingPage http://' + getCurrentIP() + '/ddi/public/blocking_page/index.php\n'
		content.append(line)
	writeContentFile(DLP_CONFIG_FILE, content)

def changeClamAvConfFile():
	content = list()
	for line in readContentFile(CLAMAV_CONFIG_FILE):
		if line.find('redirect http') >= 0:
			line = 'redirect http://' + getCurrentIP() + '/ddi/public/blocking_page/index.php\n'
		content.append(line)
	writeContentFile(CLAMAV_CONFIG_FILE, content)

def changeIpInConfFiles():
	changeWebFilterConfFile()
	changeDLPConfFile()
	changeClamAvConfFile()

def main():
	restartNetworking()
	changeIpInConfFiles()
	restartCIcap()

if __name__ == '__main__':
    main()
