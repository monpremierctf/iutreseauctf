#!/usr/bin/env python3

from http.server import HTTPServer, BaseHTTPRequestHandler, SimpleHTTPRequestHandler
import docker
from ctf_load import getHostCPU, getHostMem
from ctf_docker import listContainers, getContainerCount , getcontainerSummary
import json
import argparse


class MySimpleHTTPRequestHandler(SimpleHTTPRequestHandler):
    #def __init__(self):
    #    SimpleHTTPRequestHandler.__init__(self)
    #BASE_CONT_COUNT="/containerCount"
    #BASE_CONT_SUMMARY="/containerSummary"
    #BASE_HOST_MEM="/hostMem"
    #"BASE_HOST_CPU="/hostCPU"
    
    handlers = {
        "/containerCount":  getContainerCount,
        "/containerSummary": getcontainerSummary,
        "/hostMem": getHostMem,
        "/hostCPU": getHostCPU
    }
    def my_do_GET(self, fct):
        #listContainers()
        ret = fct()
        self.send_response(200)
        self.send_header('Content-type','application/json')
        self.end_headers()
        self.wfile.write(json.dumps(ret).encode())

    def do_GET(self):
        if self.path in self.handlers:
            self.my_do_GET(self.handlers[self.path])
        
        #else:
        #    SimpleHTTPRequestHandler.do_GET(self)
        else:
            self.send_response(200)
            self.end_headers()
            self.wfile.write(b'Ready to serve ['+self.path.encode()+b"]")   
        

#
# Main
#
if __name__ == '__main__':
    ip = '0.0.0.0'
    port = 7000
    my_parser = argparse.ArgumentParser()
    my_parser.add_argument('-ip', action='store')
    my_parser.add_argument('-port', action='store')
    args = my_parser.parse_args()
    if (args.ip):
        ip = args.ip
    if (args.port):
        port = args.port
    print(vars(args))
    print ("===========================")
    print ("| CTF Host Monitor")
    print ("|")
    print ("Listening on "+ip+":"+str(port))
    print ("")
    exit
    httpd = HTTPServer((ip, port), MySimpleHTTPRequestHandler)
    httpd.serve_forever()