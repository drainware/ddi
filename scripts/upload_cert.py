#!/usr/bin/python
import os
import shutil

def uploadSSLCert():
	if os.path.exists('/tmp/dw.pem'):
		shutil.move('/tmp/dw.pem', '/opt/drainware/etc/certs/dw.pem')
		
def main():
	uploadSSLCert()

if __name__ == '__main__':
    main()
