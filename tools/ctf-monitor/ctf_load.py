# pip3 install psutil

import psutil
from pprint import pprint
import json

def getHostCPU():
    # gives a single float value
    #print (psutil.cpu_percent())
    ret = { "cpu_percent": psutil.cpu_percent() }
    return ret

def getHostMem():
    # gives an object with many fields
    #pprint(psutil.virtual_memory())
    # you can convert that object to a dictionary 
    return dict(psutil.virtual_memory()._asdict())