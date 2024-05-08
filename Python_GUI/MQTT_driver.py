import paho.mqtt.client as paho # pip install paho-mqtt
from paho import mqtt
import time

# Unique client id will be automatically assigned after driver instalation on every device
client = paho.Client(client_id="", userdata=None, protocol=paho.MQTTv5, callback_api_version=paho.CallbackAPIVersion.VERSION2)

# Enable TLS for secure connection - just leave it like that
client.tls_set(tls_version=mqtt.client.ssl.PROTOCOL_TLS)

# Set username and password - every customer assigns they're own in idk some kind of setup wizard
client.username_pw_set("MK123", "Password#3")

# This is unique MQTT brooker to which devices will be connected
client.connect("6b490305805f494d9a2adb1b1e1ee8e9.s1.eu.hivemq.cloud", 8883)

# Publish a message
# temperature - the topic of a message, client should public data of each topic separately (temperature, humidity, pressure, etc.)
# payload - data to send
# qos - type of message (0, 1, 2) - read the documentation

while True:
    client.publish("temperature", payload="hot", qos=1)
    time.sleep(3)
    
# Idk if necessary now, subscription to a topic allows to read data from MQTT server
client.subscribe("temperature", qos=1)