#!/usr/bin/python
import os
import sys

def atCommand(command, args, time):
	at_command = "echo `" + command + " '" + args + "'` | at " + time
	print at_command
	os.system(at_command)
	
def main():
	command = sys.argv[1] 
	args = sys.argv[2] 
	time = sys.argv[3] 
	
	atCommand(command, args, time)

if __name__ == '__main__':
	if len(sys.argv) == 4:
		main()
	else:
		print "Error"