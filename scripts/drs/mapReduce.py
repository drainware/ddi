#!/usr/bin/env python
# -*- coding: utf-8 -*-

from pymongo import MongoClient
from datetime import *
from time import *
from bson import code
from math import ceil
import re
from os import popen
import sys
 
 
if (len(sys.argv) == 2):
    license = sys.argv[1] + '_'
    license = 'LIC_' + license.replace('-', '_')
else:
    license = ''
    
wf_coll_name = license + 'wf_events'
dlp_coll_name = license + 'dlp_events'
atp_coll_name = license + 'atp_events'

conexion = MongoClient('mongo')

#db = conexion['admin']
#db.authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb')

db = conexion['drs'] 

wf_coll         = db[wf_coll_name]
wf_coll_tmp    = db[wf_coll_name + '_tmp']
dlp_coll         = db[dlp_coll_name]
dlp_coll_tmp     = db[dlp_coll_name + '_tmp']
atp_coll         = db[atp_coll_name]
atp_coll_tmp     = db[atp_coll_name + '_tmp']

#
# Map and Reduce Function definitions
#

########################################################## MAP DLP ##########################################################

#Eventos generados por Politica-Accion-Severidad
map_dlp_activity = code.Code("""
function() {
    var event = this;
    event.policies_name.forEach(function(pname){
        emit({key: {date: event.date, policy: pname, action: event.action, severity: event.severity}},{count: 1});
    });
}""")

map_dlp_6m_activity = code.Code("""
function() {
    emit({key: {policy: this.policy, action: this.action, severity: this.severity}},{count: this.count});
}""")

# Usuarios que mas hacen saltar una politica
map_dlp_user_policy = code.Code("""
function(){
    var date = this.date;
    var user = this.user;
    this.policies_name.forEach(function(pname){
        emit({key: {date: date, policy: pname, user: user}}, {count: 1});
    });
}""")

# Usuarios que mas hacen saltar una politica
map_dlp_6m_user_policy = code.Code("""
function(){
    emit({key: {policy: this.policy, user: this.user}},{count: this.count});
}""")

# Grupos que mas hacen saltar una politica
map_dlp_group_policy = code.Code("""
function(){
    var date = this.date;
    var policies = this.policies_name;
    this.groups.forEach(function(gname){
        policies.forEach(function(pname){
            emit({key: {date: date, policy: pname, group: gname}}, {count: 1});
        });
    });
}""")

map_dlp_6m_group_policy = code.Code("""
function(){
    emit({key: {policy: this.policy, group: this.group}},{count: this.count});
}""")

# Eventos generados por politicas
map_dlp_by_policy = code.Code("""
function() {
    var date = this.date;
    this.policies_name.forEach(function(pname){
        emit({key: {date: date, policy: pname}}, {count: 1});
    });
}""")

map_dlp_6m_by_policy = code.Code("""
function() {
    emit({key: {policy: this.policy}},{count: this.count});
}""")

# Eventos generados por IPs
map_dlp_by_ip = code.Code("""
function() {
    emit({key: {date: this.date, ip: this.ip}},{count: 1});
}""")

map_dlp_6m_by_ip = code.Code("""
function() {
    emit({key: {ip: this.ip}},{count: this.count});
}""")

# Eventos generados por Grupos
map_dlp_by_group= code.Code("""
function() {
    var event = this;
    event.groups.forEach(function(gname){
        emit({key : {date: event.date, group: gname}},{count: 1});
    });
}""")

map_dlp_6m_by_group= code.Code("""
function() {
    emit({key: {group: this.group}},{count: this.count});
}""")

# Eventos generados por Usuarios
map_dlp_by_user = code.Code("""
function() {
    emit({key : {date: this.date, user: this.user}},{count: 1});
}""")

map_dlp_6m_by_user = code.Code("""
function() {
    emit({key: {user: this.user}},{count: this.count});
}""")

# Eventos generados por Action
map_dlp_by_action = code.Code("""
function() {
    emit({key : {date: this.date, action: this.action}},{count: 1});
}""")

map_dlp_6m_by_action = code.Code("""
function() {
    emit({key: {action: this.action}},{count: this.count});
}""")

# Eventos generados por Severidad
map_dlp_by_severity = code.Code("""
function() {
    emit({key : {date: this.date, severity: this.severity}},{count: 1});
}""")

map_dlp_6m_by_severity = code.Code("""
function() {
    emit({key: {severity: this.severity}},{count: this.count});
}""")



########################################################## MAP ATP ##########################################################
map_atp_by_app = code.Code(""" 
function() {
    emit({key : {date: this.date, app: this.processname}},{count: 1});
}""")

map_atp_6m_by_app = code.Code(""" 
function() {
    emit({key : {app: this.processname}},{count: this.count});
}""")

map_atp_by_group = code.Code(""" 
function() {
    var event = this;
    event.groups.forEach(function(gname){
        emit({key : {date: event.date, group: gname}},{count: 1});
    });
}""")

map_atp_6m_by_group = code.Code(""" 
function() {
    emit({key : {group: this.group}},{count: this.count});
}""")

rb = code.Code("""
function(key, values) {
    var count = 0;
    values.forEach(function(v) {
        count += v['count'];
    });

    return {count: count};
}""")


    
# Daily Procedures 
#


# Access
#
#In production map reduce must get only events from current day like this
#res = wf_coll.map_reduce(mb, rb, "access_tmp",query={"timetime": {"$gte": datetime(2010,1,5,0,0,0), "$lt": datetime(2010,1,5,23,59,59)}})

now = datetime.now()
def getDateByMonth(months = 0):
    year, month, day = now.timetuple()[:3]
    month += months
    d =  date(year + (month / 12), (((month -1) % 12) + 1), day)
    return d.strftime("%m")

s_sdate = now.strftime("%Y-%m-%d") + ' 00:00:00'
s_edate = now.strftime("%Y-%m-%d") + ' 23:59:59'
d_sdate = datetime.strptime(s_sdate, "%Y-%m-%d %H:%M:%S") - timedelta(days=1)
d_edate = datetime.strptime(s_edate, "%Y-%m-%d %H:%M:%S") - timedelta(days=1)
qcriteria = {"timetime": {"$gte": d_sdate, "$lte": d_edate}}
print qcriteria

scriteria = {"month": {"$gte": getDateByMonth(-6) , "$lte": now.strftime("%m") }}
print scriteria
print dlp_coll_name, db.collection_names()

if dlp_coll_name in db.collection_names():
    print dlp_coll_name
    action_values = {"log": 33, "alert": 66, "block": 99}
    severity_values = {"low": 33, "medium": 66, "high": 99}

    '''
    # Eliminar las siguientes tres lineas:
    qcriteria = {}
    db[dlp_coll_name + '_M_activity'].drop()
    db[dlp_coll_name + '_Y_activity'].drop()
    db[dlp_coll_name + '_G_activity'].drop()
    db[dlp_coll_name + '_M_group_policy'].drop()
    db[dlp_coll_name + '_Y_group_policy'].drop()
    db[dlp_coll_name + '_G_group_policy'].drop()
    db[dlp_coll_name + '_M_user_policy'].drop()
    db[dlp_coll_name + '_Y_user_policy'].drop()
    db[dlp_coll_name + '_G_user_policy'].drop()
    db[dlp_coll_name + '_M_by_policy'].drop()
    db[dlp_coll_name + '_Y_by_policy'].drop()
    db[dlp_coll_name + '_G_by_policy'].drop()
    db[dlp_coll_name + '_M_by_ip'].drop()
    db[dlp_coll_name + '_Y_by_ip'].drop()
    db[dlp_coll_name + '_G_by_ip'].drop()
    db[dlp_coll_name + '_M_by_group'].drop()
    db[dlp_coll_name + '_Y_by_group'].drop()
    db[dlp_coll_name + '_G_by_group'].drop()
    db[dlp_coll_name + '_M_by_user'].drop()
    db[dlp_coll_name + '_Y_by_user'].drop()
    db[dlp_coll_name + '_G_by_user'].drop()
    db[dlp_coll_name + '_M_by_action'].drop()
    db[dlp_coll_name + '_Y_by_action'].drop()
    db[dlp_coll_name + '_G_by_action'].drop()
    db[dlp_coll_name + '_M_by_severity'].drop()
    db[dlp_coll_name + '_Y_by_severity'].drop()
    db[dlp_coll_name + '_G_by_severity'].drop()
    '''
    
    res = dlp_coll.map_reduce(map_dlp_activity, rb, dlp_coll_name + '_tmp', query=qcriteria)
    event_list = {}
    for event in res.find():
        elem = {}
        elem['date']         = event['_id']['key']['date']
        elem['policy']         = event['_id']['key']['policy']
        elem['action']         = event['_id']['key']['action']
        elem['severity']     = event['_id']['key']['severity']
        elem['count']         = event['value']['count']
        
        tkey = elem['date'] + elem['policy']
        if not event_list.has_key(tkey):
            event_list[tkey] = [elem]
        else:    
            event_list[tkey].append(elem)
    
    for events in event_list.values():
        t_action = 0
        t_severity = 0
        t_count = 0
        for event in events:
            t_action += action_values[event['action']] * event['count']
            t_severity += severity_values[event['severity']] * event['count']
            t_count += event['count']
        
        elem = {}
        elem['date']         = event['date']
        elem['policy']         = event['policy']
        elem['action']         = ceil(t_action / t_count)
        elem['severity']     = ceil(t_severity / t_count)
        elem['count']         = t_count
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_activity'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'policy': elem['policy'], 'action': elem['action'], 'severity': elem['severity']  }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_activity'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'policy': elem['policy'], 'action': elem['action'], 'severity': elem['severity']  }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_activity'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register Activity:', elem    
    dlp_coll_tmp.remove()
    
    if dlp_coll_name + '_Y_activity' in db.collection_names():
        db[dlp_coll_name + '_S_activity'].remove()
        res = db[dlp_coll_name + '_Y_activity'].map_reduce(map_dlp_6m_activity, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['policy']         = event['_id']['key']['policy']
            elem['action']         = event['_id']['key']['action']
            elem['severity']     = event['_id']['key']['severity']
            elem['count']         = event['value']['count']
            db[dlp_coll_name + '_S_activity'].insert(elem)
        dlp_coll_tmp.remove()
    
    res = dlp_coll.map_reduce(map_dlp_group_policy, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['policy']     = event['_id']['key']['policy']
        elem['group']     = event['_id']['key']['group']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_group_policy'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'policy': elem['policy'], 'group': elem['group'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_group_policy'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'policy': elem['policy'], 'group': elem['group']   }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_group_policy'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register Group-Policy:', event
    dlp_coll_tmp.remove()
    
    if dlp_coll_name + '_Y_group_policy' in db.collection_names():
        db[dlp_coll_name + '_S_group_policy'].remove()
        res = db[dlp_coll_name + '_Y_group_policy'].map_reduce(map_dlp_6m_group_policy, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['policy']     = event['_id']['key']['policy']
            elem['group']     = event['_id']['key']['group']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_group_policy'].insert(elem)
        dlp_coll_tmp.remove()

    res = dlp_coll.map_reduce(map_dlp_user_policy, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['policy']     = event['_id']['key']['policy']
        elem['user']     = event['_id']['key']['user']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_user_policy'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'policy': elem['policy'], 'user': elem['user'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_user_policy'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'policy': elem['policy'], 'user': elem['user'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_user_policy'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register User-Policy:', event
    dlp_coll_tmp.remove()    

    if dlp_coll_name + '_Y_user_policy' in db.collection_names():
        db[dlp_coll_name + '_S_user_policy'].remove()
        res = db[dlp_coll_name + '_Y_user_policy'].map_reduce(map_dlp_6m_user_policy, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['policy']     = event['_id']['key']['policy']
            elem['user']     = event['_id']['key']['user']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_user_policy'].insert(elem)
        dlp_coll_tmp.remove()

    res = dlp_coll.map_reduce(map_dlp_by_policy, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['policy']     = event['_id']['key']['policy']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_policy'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'policy': elem['policy'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_policy'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'policy': elem['policy'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_policy'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by Policy:', event
    dlp_coll_tmp.remove()

    if dlp_coll_name + '_Y_by_policy' in db.collection_names():
        db[dlp_coll_name + '_S_by_policy'].remove()
        res = db[dlp_coll_name + '_Y_by_policy'].map_reduce(map_dlp_6m_activity, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['policy']     = event['_id']['key']['policy']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_policy'].insert(elem)
        dlp_coll_tmp.remove()

    res = dlp_coll.map_reduce(map_dlp_by_ip, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['ip']     = event['_id']['key']['ip']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_ip'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'ip': elem['ip'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_ip'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'ip': elem['ip'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_ip'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by IP:', event
    dlp_coll_tmp.remove()

    if dlp_coll_name + '_Y_by_ip' in db.collection_names():
        db[dlp_coll_name + '_S_by_ip'].remove()
        res = db[dlp_coll_name + '_Y_by_ip'].map_reduce(map_dlp_6m_by_ip, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['ip']     = event['_id']['key']['ip']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_ip'].insert(elem)
        dlp_coll_tmp.remove()

    res = dlp_coll.map_reduce(map_dlp_by_group, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['group']     = event['_id']['key']['group']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_group'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'group': elem['group'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_group'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'group': elem['group'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_group'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by Group:', event
    dlp_coll_tmp.remove()
    
    if dlp_coll_name + '_Y_by_group' in db.collection_names():
        db[dlp_coll_name + '_S_by_group'].remove()
        res = db[dlp_coll_name + '_Y_by_group'].map_reduce(map_dlp_6m_by_group, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['group']     = event['_id']['key']['group']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_group'].insert(elem)
        dlp_coll_tmp.remove()
    
    res = dlp_coll.map_reduce(map_dlp_by_user, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['user']     = event['_id']['key']['user']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_user'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'user': elem['user'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_user'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'user': elem['user'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_user'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by User:', event        
    dlp_coll_tmp.remove()

    if dlp_coll_name + '_Y_by_user' in db.collection_names():
        db[dlp_coll_name + '_S_by_user'].remove()
        res = db[dlp_coll_name + '_Y_by_user'].map_reduce(map_dlp_6m_by_user, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['user']     = event['_id']['key']['user']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_user'].insert(elem)
        dlp_coll_tmp.remove()

    res = dlp_coll.map_reduce(map_dlp_by_action, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']     = event['_id']['key']['date']
        elem['action']     = event['_id']['key']['action']
        elem['count']     = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_action'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'action': elem['action'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_action'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'action': elem['action'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_action'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by Action:', event
    dlp_coll_tmp.remove()
    
    if dlp_coll_name + '_Y_by_action' in db.collection_names():
        db[dlp_coll_name + '_S_by_action'].remove()
        res = db[dlp_coll_name + '_Y_by_action'].map_reduce(map_dlp_6m_by_action, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['action']     = event['_id']['key']['action']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_action'].insert(elem)
        dlp_coll_tmp.remove()
        
    res = dlp_coll.map_reduce(map_dlp_by_severity, rb, dlp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date']         = event['_id']['key']['date']
        elem['severity']     = event['_id']['key']['severity']
        elem['count']         = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[dlp_coll_name + '_M_by_severity'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'severity': elem['severity'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_Y_by_severity'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'severity': elem['severity'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[dlp_coll_name + '_G_by_severity'].update(criteria, new_data, upsert=True)
        
        print 'DLP Register by Severity:', event
    dlp_coll_tmp.remove()
    
    if dlp_coll_name + '_Y_by_severity' in db.collection_names():
        db[dlp_coll_name + '_S_by_severity'].remove()
        res = db[dlp_coll_name + '_Y_by_severity'].map_reduce(map_dlp_6m_by_severity, rb, dlp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['severity']     = event['_id']['key']['severity']
            elem['count']     = event['value']['count']
            db[dlp_coll_name + '_S_by_severity'].insert(elem)
        dlp_coll_tmp.remove()
    
dlp_coll_tmp.drop()

if atp_coll_name in db.collection_names():
    
    '''
    qcriteria = {}
    db[atp_coll_name + '_M_by_app'].drop()
    db[atp_coll_name + '_Y_by_app'].drop()
    db[atp_coll_name + '_G_by_app'].drop()
    db[atp_coll_name + '_M_by_group'].drop()
    db[atp_coll_name + '_Y_by_group'].drop()
    db[atp_coll_name + '_G_by_group'].drop()
    '''
    
    res = atp_coll.map_reduce(map_atp_by_app, rb, atp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date'] = event['_id']['key']['date']
        elem['app'] = event['_id']['key']['app']
        elem['count'] = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[atp_coll_name + '_M_by_app'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'app': elem['app'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[atp_coll_name + '_Y_by_app'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'app': elem['app'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[atp_coll_name + '_G_by_app'].update(criteria, new_data, upsert=True)
        
        print 'ATP Register by App:', event
    atp_coll_tmp.remove()
    
    if atp_coll_name + '_Y_by_app' in db.collection_names():
        db[atp_coll_name + '_S_by_app'].remove()
        res = db[atp_coll_name + '_Y_by_app'].map_reduce(map_atp_6m_by_app, rb, atp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['app']     = event['_id']['key']['app']
            elem['count']     = event['value']['count']
            db[atp_coll_name + '_S_by_app'].insert(elem)
        atp_coll_tmp.remove()

    res = atp_coll.map_reduce(map_atp_by_group, rb, atp_coll_name + '_tmp', query=qcriteria)
    for event in res.find():
        elem = {}
        elem['date'] = event['_id']['key']['date']
        elem['group'] = event['_id']['key']['group']
        elem['count'] = event['value']['count']
        
        # Inseramos en la tabla del Mes
        if( elem['date'].split('.')[1] == now.strftime("%m")):
            db[atp_coll_name + '_M_by_group'].insert(elem)
        
        # Insertamos en la tabla del Anio
        criteria = {'month': elem['date'].split('.')[1], 'group': elem['group'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[atp_coll_name + '_Y_by_group'].update(criteria, new_data, upsert=True)
        
        # Insertamos en la tabla General
        criteria = {'year': elem['date'].split('.')[0], 'group': elem['group'] }
        new_data = {'$inc': {'count': elem['count'] }}
        db[atp_coll_name + '_G_by_group'].update(criteria, new_data, upsert=True)
        
        print 'ATP Register by Group:', event
    atp_coll_tmp.remove()
    
    if atp_coll_name + '_Y_by_group' in db.collection_names():
        db[atp_coll_name + '_S_by_group'].remove()
        res = db[atp_coll_name + '_Y_by_group'].map_reduce(map_atp_6m_by_group, rb, atp_coll_name + '_tmp', query=scriteria)
        for event in res.find():
            elem = {}
            elem['group']     = event['_id']['key']['group']
            elem['count']     = event['value']['count']
            db[atp_coll_name + '_S_by_group'].insert(elem)
        atp_coll_tmp.remove()
        
atp_coll_tmp.drop()


