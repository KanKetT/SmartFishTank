import RPi.GPIO as GPIO
from time import sleep

GPIO.setwarnings(False)
GPIO.cleanup()
GPIO.setmode(GPIO.BOARD)
GPIO.setup(22, GPIO.OUT)
#GPIO.setup()
pwm = GPIO.PWM(22, 50)



#for i in range(0,10):
pwm.start(0)
pwm.ChangeDutyCycle(6.5)
sleep(2.4)
print("done1")
pwm.ChangeDutyCycle(7.2)
print("done2")
sleep(0.1)
    #pwm.ChangeDutyCycle(0)
pwm.stop()
 #   print("round", i)
