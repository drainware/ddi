#!/usr/bin/python

import requests
from random import choice


tweets = []

tweets.append('Enjoying @drainware, a security solution for protecting sensitive data. It is free! #infosec')
tweets.append('Testing out @drainware, a free data security package. Looks good. Anybody else? #infosec')
tweets.append('Freemium version of @drainware protects sensitive information of data theft attempts #infosec')
tweets.append('Did you know you can use @drainware to prevent data loss for free? #infosec')
tweets.append('Try @drainware to prevent data leakage #infosec #cloudsec')
tweets.append('Catch theft attempts of information before the jerks win with the free version of @drainware #infosec')
tweets.append('You can spot malicious employees automatically with @drainware for free #infosec')
tweets.append('Keep your data safe from snoops and criminals for free with @drainware #infosec')
tweets.append('Get DLP - Data Leakage Prevention - free with @drainware #infosec')

tweet = choice(tweets)

payload = {'formData[username]': 'Drainware', 'formData[mail]' : 'development@drainware.com', 'formData[filename]' : 'Drainware', 'formData[dlurl]' : 'http://drai.nwa.re/17nDOkV', 'formData[message]' : tweet, 'formData[websiteUrl]' : 'www.drainware.com', 'formData[maxdlAmount]' : '0', 'formData[btnDesc]' : 'DLP, Sandbox and Inspector', 'formData[day]' : '', 'formData[month]' : '', 'formData[year]' : '', 'formData[uniqueID]' : '37scaapyz', 'formData[networks]' : '1,0,0,0' }

r = requests.post("http://www.paywithatweet.com/php/ajax.edit.php", data=payload)

