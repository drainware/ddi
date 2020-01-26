#!/usr/bin/env python
import os
import sys
import time
import re

from TailFollow import *

import signal
import sys

#pidfile = None 

exprDown = re.compile('\[down,!opt\]')

def signal_handler(signal, frame):
    os.remove(pidfile)
    sys.exit(0)


def checkLine(line):
    #check line
    line = line .rstrip()
    #if re.match(exprDown, line):
    if line.find('[down,!opt]') >= 0:
        os.popen('killall -9 c-icap')


if len(sys.argv) != 5:
    print "Usage python %s --logfile log_file --pidfile pid_file" % sys.argv[0]
    sys.exit(-10)

signal.signal(signal.SIGINT, signal_handler)


#FIXME: parse args instead using sys.argv later


# Sale de aqui solo cuando haya abierto el fichero con exito
while 1:
    try:
        tail = TailFollow(sys.argv[2], track=1)
        time.sleep(1)
        break
    except:
        time.sleep(1)
        # cerrar conexion a db
        pass


# fixme
global pidfile 
pidfile = sys.argv[4]
pid = str(os.getpid())

if os.path.isfile(pidfile):
  print "%s already exists, exiting" % pidfile 
  sys.exit()
else:
  file(pidfile, 'w').write(pid)

while 1:
    for line in tail:
        checkLine(line)

    time.sleep(0.0001)

sys.exit(-10)

try:
    tail.close()
except:
    pass



sys.exit(-1)


# EOF
