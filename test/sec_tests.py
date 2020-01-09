import requests
import random
import string
import sys
import time
import json
from pprint import pprint
from random import randint
import os
import threading
import signal
from user_full_ctf import *


user_names = [
    "user1",  
    "user2<script>alert(1)</script>",  
    "user3' or 1=1 -- -",  
    "user4'\";><-",

]

def generate_bad_flag():
    return "badflag_'\")(;< >\n\r%00)"

def scenario_sec(noxterm, nocontainer, maxsleep):
    init()
    flags = load_flags()


    print ("========== Registering/Login users: ")
    for  x in user_names:
        user1  = UserSession()
        user1.login = x
        user1.password = x
        user1.mail = x
        print ("Try to Register user :"+user1.login)
        if (register_user(user1, user1.login, user1.password, user1.mail, 'YOLO')):
            users.append(user1)
            print "Register ok."+str(user1.id)
        else:
            print "Pb register "+str(user1.id)+". Try to login :)"
            if (login_user(user1, user1.login, user1.password)):
                users.append(user1)
                print "."+str(user1.id)
            else:
                print "Fail with "+str(user1.id)
    print ("Registered "+str(len(users))+" users.")
    
                

    
    # CTF ongoing
    nb_xterm=0
    nb_containers=0
    totalflag= len(flags['results']) * len(users)
    while (totalflag>0):
        print("")
        print ("=======================")
        print ("| Nb User       : "+str(len(users)))
        print ("| Nb Flags left : "+str(totalflag))
        print ("| Nb xterm      : "+str(nb_xterm))
        print ("| Nb containers : "+str(nb_containers))
        for u in users:
            #
            # Flags
            if (u.flag_count<len(flags['results'])):
                chal_id = flags['results'][u.flag_count]['challenge_id']
                # Try the right flag or a false one ?
                if (randint(0, 9)<u.skill):
                    flag = flags['results'][u.flag_count]['content']
                    u.flag_count = u.flag_count+1
                    totalflag = totalflag-1
                else:
                    flag = generate_bad_flag()
                print "["+str(u.id)+"] Send Flag "+str(chal_id)+" : ["+flag+"]"
                validate_flag(u, chal_id, flag)
                
            #
            # xterm
            if (not noxterm):
                if (not u.xterm):
                    if (randint(0, 9)>=8):    
                        starttime = time.time()
                        print "["+str(u.id)+"] Open Terminal"
                        open_terminal(u)
                        duration = time.time() - starttime
                        u.xterm=True
                        nb_xterm=nb_xterm+1
                        print "["+str(u.id)+"] Opened Terminal in "+str(round(duration))
                        #print "nb_xterm => "+str(nb_xterm) 

            #
            # Create container
            if (not nocontainer):
                if (u.container_count<len(containers)):
                    if (randint(0, 9)>=8):
                        cont_id = containers[u.container_count]
                        u.container_count= u.container_count+1
                        print "["+str(u.id)+"] Create container " +cont_id  
                        starttime = time.time()    
                        create_container(u, cont_id)
                        duration = time.time() - starttime
                        print "["+str(u.id)+"] Created container in "+str(round(duration))
                        nb_containers=nb_containers+1
                        #print "nb_containers => "+str(nb_containers)

                #
                # Run cmd in container
                if (randint(0, 9)>=5):
                    print "["+str(u.id)+"] start cmd in container "
                    run_rand_cmd(u)
                    print "["+str(u.id)+"] stop cmd in container "

        time.sleep(randint(2, maxsleep))


    ## Destroy all containers
    time.sleep(5)  # 5 seconds
    print ("Terminate containers")
    for u in users:
        for c in containers:
            terminate_container(u, c)
    return




#
# Main
#
if __name__ == '__main__':

    # Init
    nbUserMax = 60
    print ("= Init")
    scenario_sec(True, True, 2)
    exit()



    

    
    
    
    

