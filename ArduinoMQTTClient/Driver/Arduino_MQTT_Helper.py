import serial  # type: ignore
import paho.mqtt.client as paho # type: ignore
from paho import mqtt # type: ignore
import json 
import datetime


client = paho.Client(client_id="arduino", userdata=None, protocol=paho.MQTTv5, callback_api_version = paho.CallbackAPIVersion.VERSION2)
client.tls_set(tls_version=mqtt.client.ssl.PROTOCOL_TLS)
client.username_pw_set("Your_Username", "Your_Password") # Info about user for specific broker

client.connect("MQTT_Broker", 8883) # address of broker

msg_topic = "hc_data" 

arduino = serial.Serial(port='COM5', baudrate=115200, timeout=10)

while True:

    data = arduino.readline().decode(encoding='ascii', errors='ignore')
    if data == "": # checking if data is empty
        continue 
    data = data.split('\r\n')
    data = data[0]

    timestamp = datetime.datetime.now()
    timestamp = timestamp + datetime.timedelta(seconds=.5)
    timestamp = timestamp.replace(microsecond=0)

    msg_data = {}
    msg_data["deviceId"] = "arduino_uno"
    msg_data["distance"] = data
    msg_data["timestamp"] = str(timestamp)

    message = json.dumps(msg_data)

    print(f'Publish for topic: {msg_topic}, data: {msg_data}')
    client.publish(msg_topic, payload=message)
    

