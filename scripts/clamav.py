#!/usr/bin/python
import sys
import re
import os

"""
#icap_service service_clamav_req reqmod_precache bypass=1 icap://127.0.0.1:1344/squidclamav
#adaptation_access service_clamav_req allow all
#icap_service service_clamav_resp respmod_precache bypass=1 icap://127.0.0.1:1344/squidclamav
#adaptation_access service_clamav_resp allow all
"""

ICAP_SERVICE = "  service_clamav"
content = ""

def grep(string,list):
  expr = re.compile(string)
  line = filter(expr.search,list)
  return line
  #return filter(expr.search,list)


def main():
  global content
  argc = len(sys.argv)
  global fname 
  fname = "/opt/drainware/etc/squid.conf"
  with open(fname) as f:
    content = f.readlines()
  
  if argc < 2:
    usage()

  if (argc != 3 and sys.argv[1] == "set") or (argc != 2 and sys.argv[1] == "get") :
    usage()
  command = sys.argv[1]
  if command == "set":
    status = sys.argv[2]
    if status == "on":
      startAv()
      restartSquid3()
    elif status == "off":
      stopAv()
      restartSquid3()
    else:
      usage()
  elif command == "get":
    status = getStatus() 
    print status
  else:
    print sys.argv[1]
    usage()

def getStatus():
  line = grep(ICAP_SERVICE, content)
  # Now getting the last coincidence
  if line.pop().startswith('#'):
    status = 'off'
  else:
    status = 'on'
  return status

def startAv():
  lines = []
  for line in content:
    if line.find(ICAP_SERVICE) > 0:
      line = line.replace('#','')
    lines.append(line)
  f = open(fname, 'w')
  f.writelines(lines)
  f.close()

def stopAv():
  if getStatus() == 'off':
    return
  lines = []
  for line in content:
    if line.find(ICAP_SERVICE) > 0:
      line = '#{0}'.format( line )
    lines.append(line)
  f = open(fname, 'w')
  f.writelines(lines)
  f.close()

def restartSquid3():
	os.system("/etc/init.d/squid3 reload")  

def usage():
  print "Usage:"
  print "%s get" % (sys.argv[0])
  print "%s set [on/off] " % (sys.argv[0])
  print
  exit()

if __name__ == '__main__':
    main()
