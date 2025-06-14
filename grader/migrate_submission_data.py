import mysql.connector
import os
from os import path
import time
import json
import datetime

dbconnector = None
mycursor = None

def checkSettings():
    # Check if settings is exist.
    if not path.isfile("settings.json"):
        json = """{
        "database": {
            "host":     "<<IP>>",
            "username": "<<USERNAME>>",
            "password": "<<PASSWORD>>",
            "database": "<<DATABASE>>"
        }
    }"""
        
        f = open("settings.json","w")
        f.write(str(json))
        print("/!\ Please config your connection settings first. /!\\")
        exit(0)

def DBconnect():
    global dbconnector, mycursor
    checkSettings()

    attempt = 0
    dbconnector = None
    mycursor = None

    #Loading settings.json
    f = json.loads(open("settings.json", "r").read())
    while mycursor == None:
        try:
            dbconnector = mysql.connector.connect(host=f['database']['host'],user=f['database']['username'],password=f['database']['password'],database=f['database']['database'])
            mycursor = dbconnector.cursor(buffered=True)
            if (attempt != 0):
                print('[/] Connection Success!')
        except Exception as e:
            print("[!] ERROR on establishing database:\n", e)
            attempt+=1
            print(f"Retrying in {60*attempt} seconds...")
            time.sleep(60*attempt)
            if (attempt == 5):
                print("/!\ Maximum Attempt Reached")
                exit(0)

if __name__ == '__main__':
    DBconnect()
    webLocation = "/" + path.join("var","www","lca.grader.ga")
    not_found = []
    try:
        mycursor = dbconnector.cursor(buffered=True)
        mycursor.execute("SELECT `id`,`script`,`uploadtime`,`user` FROM `submission` WHERE `id` NOT IN (SELECT `submission_id` FROM `submission_data`) ORDER BY id;") #Get specific data from submission SQL where result is W (Wait)
        data = mycursor.fetchall()
        for i in data:
            file_path = i[1].replace("..",webLocation)
            if not path.isfile(file_path):
                dt_obj = i[2] - datetime.timedelta(hours=7)
                epoch_time = int(dt_obj.timestamp())
                file_loc = "/" + path.join("var","www","lca.grader.ga","file","judge","upload",i[3])
                for f in os.listdir(file_loc):
                    if (epoch_time in f):
                        file_path =  path.join(file_loc,f)
                print("[!] ERROR file not found:", file_path)
                not_found.append(i[0])
                continue
            f = open(file_path,"r")
            mycursor.execute("INSERT INTO `submission_data` (`submission_id`,`data`) VALUES (%s,%s)",(i[0],f.read().replace("\n","\\n")))
            dbconnector.commit()
            f.close()
        print("[/] Done!")
        print("[/] Not Found:", not_found)
    except Exception as e:
        print("[!] ERROR losing connection to database:\n", e)
        print("[!] The system will be halted for 30 seconds")
        time.sleep(30)
        DBconnect()