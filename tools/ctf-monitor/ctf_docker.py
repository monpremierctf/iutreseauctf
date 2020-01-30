#
# pip3 install docker
#


import docker
from pprint import pprint
import json

client = docker.from_env()

ctfInfraNames = [
    "traefik",

    "webserver_nginx",
    "webserver_php",
    "webserver_mysql",

    "challenge-box-provider",

]

ctfSharedChallengesNames = [

    "ctf-sqli_nginx",
    "ctf-sqli_php",
    "ctf-sqli_mysql",

    "ctf-passwd-web",
    "ctf-passwd-php"
]

def getContainerCount():
    ret = { "count": len(client.containers.list()) }
    return ret

def getcontainerSummary():
    return listContainers()


def listContainers():
    print ("==== Containers ====")
    ctfInfraNamesCount={}
    ctfSharedChallengesNamesCount={}
    ctfChallengesNamesCount={}
    for container in client.containers.list(all=False):
        print (container.id, container.name, container.status)
        if container.name in ctfInfraNames:
            print ("ctfInfraNames")
            if (container.name in ctfInfraNamesCount):
                ctfInfraNamesCount[container.name] += 1
            else:
                ctfInfraNamesCount[container.name]=1
        elif container.name in ctfSharedChallengesNames:
            print ("ctfSharedChallengesNames") 
            if (container.name in ctfSharedChallengesNamesCount):
                ctfSharedChallengesNamesCount[container.name] += 1
            else:
                ctfSharedChallengesNamesCount[container.name]=1  
        elif 'ctf-uid' in container.labels:
            print ("ctfContainer")
            chall = container.name.split('_')[0]
            if (chall in ctfChallengesNamesCount):
                ctfChallengesNamesCount[chall] += 1
            else:
                ctfChallengesNamesCount[chall]=1  


        # Stats ok but very slow: 1s/call
        #stats_obj = container.stats(stream=False)
        #pprint(stats_obj)
        #pprint (container.labels)
    pprint(ctfInfraNamesCount)
    pprint(ctfSharedChallengesNamesCount)
    pprint(ctfChallengesNamesCount)
    ret= {
        'infra': ctfInfraNamesCount,
        'sharedChalls': ctfSharedChallengesNamesCount,
        'challs': ctfChallengesNamesCount
    }
    return ret

def listNetworks():
    print ("")
    print ("==== Networks ====")
    for network in client.networks.list():
        network.reload()
        print ("["+network.name+"]")
        #pprint (network.containers)
        for c in network.containers:
            print (" - "+c.name)
        print("")

    print ("")

def getContainers():
    c_traefik = client.containers.get("traefik")
    print (c_traefik.id, c_traefik.name, c_traefik.status)
    print ("")

def getContainerLogs(boxname):
    try:
        c_challprovider = client.containers.get(boxname)
        logs = c_challprovider.logs().decode()
    except:
        logs ="container not found" 
    ret = { "logs":  logs }
    return ret

def getLogsTraefik():
    return getContainerLogs("traefik")

def getLogsWebserverNginx():
    return getContainerLogs("webserver_nginx")

def getLogsWebserverPhp():
    return getContainerLogs("webserver_php")

def getLogsWebserverMySQL():
    return getContainerLogs("webserver_mysql")

def getLogsChallengeProvider():
    return getContainerLogs("challenge-box-provider")
