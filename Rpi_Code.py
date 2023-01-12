# import and set
import serial
import RPi.GPIO as GPIO
import time
import _thread
import subprocess
import pytz
from datetime import datetime
# import web
import requests
import json
# import web

GPIO.setwarnings(False)
GPIO.cleanup()
GPIO.setmode(GPIO.BOARD)
# communicate
ser = serial.Serial('/dev/ttyUSB0',
                    baudrate=9600,
                    parity=serial.PARITY_NONE,
                    stopbits=serial.STOPBITS_ONE)
# Initial read ----
data1 = ser.readline()
data2 = ser.readline()
temp = data1.decode('utf-8').rstrip()
tds = data2.decode('utf-8').rstrip()
temp = float(temp)
tds = float(tds)
# --------

# temp setup
heat = 29
cold = 31
GPIO.setmode(GPIO.BOARD)
GPIO.setup(heat, GPIO.OUT) # Heat
GPIO.setup(cold, GPIO.OUT) # Cold

# safety
safety_state = 0

# feeding routine
routine = 0

#<<<<<-----  web start ----->>>>>>
'''
<<< Dowload part >>>
'''

'''
Original code before tidy up

UserInput = "https://cingalese-flames.000webhostapp.com/api/UserInput/specific.php?id=6"
response = requests.get(http_UserInput)
res = json.loads(response.text)
ID = res['UserInput'][0]['id']
TEMP_SET = int(res['UserInput'][0]['Temp'])
FEED_SET = res['UserInput'][0]['Feed']
print("TEMP_SET =",TEMP_SET)
print("FEED_SET =",FEED_SET)

'''
UserInput = "https://cingalese-flames.000webhostapp.com/api/UserInput/specific.php?id=6"

#Tank = "https://cingalese-flames.000webhostapp.com/api/Tank/specific.php?id=6"

def req_table(http):
    '''
    request data from GET method via URL
    return json data
    '''
    response = requests.get(http)
    res = json.loads(response.text)
    return res

def get_data(response,table, col):
    '''
    parse data from json format by specified table and column name
    return ready to use data
    '''
    return (response[table][0][col])

def feed_01(txt):
    '''
    Due to the data base store as "on" and "off" but we're using it here as 0 and 1
    Traslate from on off -> 1 0
    '''
    return 1 if txt == "on" else 0

'''
<<< Upload part >>>
'''

Tank_update_url  = "https://cingalese-flames.000webhostapp.com/api/Tank/insert.php?"

UserInput_update_url = "https://cingalese-flames.000webhostapp.com/api/UserInput/update.php?"

def update(url, *data):
    if (len(data) == 2):
        req_str = url + "temp=" + str(temp) + "&tds=" + str(tds)
        resp = requests.get(req_str)
        #print("Updated temp = " + str(temp) + " tds = " + str(tds))
    elif (len(data) == 1):
        req_str = url + "Feed=" + str(data[0])
        resp = requests.get(req_str)
        #print("Updated Feed = off")

'''
Get configuration from user via website
'''
res = req_table(UserInput)
temp_set = float(get_data(res, "UserInput", "Temp"))
feed_set = feed_01(get_data(res, "UserInput", "Feed"))
feed_time = get_data(res, "UserInput", "Feeder")
#print("feed_time = ", type(feed_time))
#<<<<<-----  web end ----->>>>>>

#temp_set = TEMP_SET # Waiting for recieving value from www.
timer_t = time.time() # control temp

# feed setup
#feed_set = feed_01(FEED_SET)# Waiting for recieving value from www.
GPIO.setup(22, GPIO.OUT)
pwm = GPIO.PWM(22, 50)

# safety setup
safety_state=0

# UART reading
def readVal():
    global temp, tds 
    # communication
    data1 = ser.readline()
    data2 = ser.readline()
    temp = data1.decode('utf-8').rstrip()
    tds = data2.decode('utf-8').rstrip()
    temp = float(temp)
    tds = float(tds)
_thread.start_new_thread(readVal,())

def controlTemp():
    while(1):
        global timer_t, temp_set, safety_state
        timercontrol = time.time()-timer_t
        if safety_state == 0:
            if timercontrol < 15: # activate 15 seconds
                if(temp_set+1>temp):
                    print("Heating")
                    GPIO.output(heat, GPIO.LOW) # activate
                    GPIO.output(cold, GPIO.HIGH)
                elif(temp_set-1<temp):
                    print("Cooling")
                    GPIO.output(cold, GPIO.LOW)
                    GPIO.output(heat, GPIO.HIGH)
                else:
                    GPIO.output(heat, GPIO.HIGH)
                    GPIO.output(cold, GPIO.HIGH)
            #print("timercontrol=",timercontrol)
            if 15 < timercontrol < 30: #deactivate 30 sec
                GPIO.output(heat, GPIO.HIGH)
                GPIO.output(cold, GPIO.HIGH)
            #   print("Here")
            if timercontrol > 30: # reset time
                timer_t = time.time()
        else:
            print("safety toggle")
            print("Your Fish is in Cooking Process!!!")

_thread.start_new_thread(controlTemp,())
# safety # run parallel to check
def SafetyCondition():
    global temp, temp_set,safety_state
    while(1):
        while(float(temp) > 35 or float(temp) < 15 or
            temp_set > 35 or temp_set < 15):
            GPIO.output(heat, GPIO.HIGH)
            GPIO.output(cold, GPIO.HIGH)
            safety_state = 1
        safety_state = 0

_thread.start_new_thread(SafetyCondition,())

while(1):


    # Update and Get data
    update(Tank_update_url,temp,tds)
    res = req_table(UserInput)
    temp_set = float(get_data(res, "UserInput", "Temp"))
    feed_set = feed_01(get_data(res, "UserInput", "Feed"))
    feed_time = get_data(res, "UserInput", "Feeder")
    feed_time = feed_time[:5]


    tz_TH = pytz.timezone('Asia/Bangkok')
    t = datetime.now(tz_TH)
    time_now = t.strftime("%H:%M")

    if routine == 0 :
        if time_now == feed_time:
            feed_set = 1
            routine =1
    if time_now!=feed_time:
        routine = 0


    if feed_set==1:
        subprocess.call("python libservo2.py", shell=True)
        feed_set=0
        update(UserInput_update_url, "off")
