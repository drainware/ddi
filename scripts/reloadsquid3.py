#!/usr/bin/python
import os

def restartSquid3():
	os.system("/etc/init.d/squid3 reload")

def main():
	restartSquid3()

if __name__ == '__main__':
    main()
