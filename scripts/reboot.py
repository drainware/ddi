#!/usr/bin/python
import os

def rebootDDI():
	os.system("/sbin/reboot")

def main():
	rebootDDI()

if __name__ == '__main__':
    main()
