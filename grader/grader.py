import mysql.connector
import time
import urllib3
from os import path, environ
from Garedami.Src import Judge
import requests
import json

dbconnector = None
mycursor = None

def DBconnect():
    global dbconnector, mycursor
    
    # Get database configuration from environment variables with fallback values
    db_host = environ.get('DB_HOST')
    db_user = environ.get('DB_USERNAME')
    db_password = environ.get('DB_PASSWORD')
    db_name = environ.get('DB_SCHEMA')
    
    # For debugging
    print(f"Connecting to database: {db_host}, {db_user}, {db_name}")
    
    attempt = 0
    dbconnector = None
    mycursor = None

    while mycursor == None:
        try:
            dbconnector = mysql.connector.connect(
                host=db_host,
                user=db_user,
                password=db_password,
                database=db_name
            )
            mycursor = dbconnector.cursor(buffered=True)
            if (attempt != 0):
                print('[/] Connection Success!')
        except Exception as e:
            print("[!] ERROR on establishing database:\n", e)
            attempt+=1
            print(f"Retrying in {60*attempt} seconds...")
            time.sleep(60*attempt)
            if (attempt == 5):
                print("Maximum Attempt Reached")
                exit(0)

def getWaitSubmission():
    while True:
        try:
            mycursor = dbconnector.cursor(buffered=True)
            mycursor.execute("SELECT `id`,`user`,`problem`,`lang`,`script` FROM `submission` WHERE `result` = 'W' ORDER BY `id`") #Get specific data from submission SQL where result is W (Wait)
            return mycursor.fetchall()
        except Exception as e:
            print("[!] ERROR losing connection to database:\n", e)
            print("[!] The system will be halted for 30 seconds")
            time.sleep(30)
            DBconnect()

def getTimeAndMem(idTask):
    mycursor.execute(f"SELECT `time`,`memory` FROM `problem` WHERE `id` = {idTask} LIMIT 1")
    result = mycursor.fetchall()
    if (len(result)):
        return result[0][0],result[0][1]
    return -69,-420

if __name__ == '__main__':

    print("Grader.py started")

    while(1):
        DBconnect()
        queue = getWaitSubmission()
        if (len(queue)):
            print("Founded Waiting Queue : ",len(queue))
            print(queue)
        for myresult in queue:
            #Get data from query
            subID = myresult[0] #id is the 1st.
            userID = myresult[1] #user is the 2nd.
            probID = myresult[2] #problem is the 3rd.
            lang = myresult[3] #lang is the 4th.
            userCodeLocation = myresult[4] #script location is the 5th.

            print(f"----------<OwO>----------\nJudging: submission={subID}, problem={probID}, user={userID}")

            probTestcaseLocation = path.join("file","judge","prob",str(probID))
            #print(probTestcaseLocation)
            #All testcases will be here

            srcCode = ""
            try:
                with open(userCodeLocation,"r") as f:
                    srcCode = f.read()
            except FileNotFoundError:
                judgeResult = ("MissingFile",0,100,0,0,"Can't Locate User File")
            #print(srcCode)
            probTime,probMem = getTimeAndMem(probID)
            #print((probID,lang,probTestcaseLocation,srcCode,probTime,probMem))

            if probTime < 0:
                judgeResult = ("WebError",0,100,0,0,"Web API Down")
            else:
                judgeResult = Judge.judge(probID,lang,probTestcaseLocation,srcCode,probTime,probMem)
            #Result from judge
            result = judgeResult[0]
            score = int(judgeResult[1])
            maxScore = int(judgeResult[2])
            runningTime = int(judgeResult[3]) #ms
            memory = int(judgeResult[4]) #MB
            comment = judgeResult[5]

            #Update to SQL
            query = ("UPDATE `submission` SET `result` = %s,`score` = %s,`maxScore` = %s,`runningTime` = %s,`memory` = %s,`comment` = %s WHERE `id` = %s")
            data = (result, score, maxScore, runningTime, memory, comment, subID) #Don't forget to add subID
            mycursor.execute(query, data)
            print(f"Finished Judge submission={subID}, problem={probID}, user={userID} --> {result}")

            #Make sure that query is done.
            dbconnector.commit()
            time.sleep(1)
        dbconnector.commit()
        dbconnector.close()
        #Time sleep interval for 5 second.
        try:
            time.sleep(5)
        except KeyboardInterrupt:
            print("Bye bye")
            exit(0)