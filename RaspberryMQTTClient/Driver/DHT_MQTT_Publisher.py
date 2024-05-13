import network
import time
import ujson
from umqtt.simple import MQTTClient
import ssl
import dht
import machine

d = dht.dht11(machine.Pin(28))
# Fill in your WiFi network name (ssid) and password
# REMEMBER TO CHANGE IT!
wifi_ssid = "Your_SSID"
wifi_password = "Your_Password"

# Connect to WiFi
wlan = network.WLAN(network.STA_IF)
wlan.active(True)
wlan.config(pm = 0xa11140)
wlan.connect(wifi_ssid, wifi_password)
while wlan.isconnected() == False:
    print('Waiting for connection...')
    time.sleep(3)
print("Connected to WiFi")

# Filling in connection information
mqtt_host = b"MQTT_Broker"
mqtt_port = 0
mqtt_username = b"Your_User"  # Username
mqtt_password = b"Your_Password"  # Password for user

# Unique ID for this client
mqtt_client_id = b"raspberry_pi_1"

context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
# Initializing MQTTClient and connecting to the MQTT server
mqtt_client = MQTTClient(
        client_id=mqtt_client_id,
        server=mqtt_host,
        port=mqtt_port,
        user=mqtt_username,
        password=mqtt_password,
        ssl = context
        )
mqtt_client.connect()

# Sending simulated data every 10 seconds

mqtt_publish_topic = "dht_data"
try:
    while True:
        
        d.measure()
        temperature = d.temperature()
        humidity = d.humidity()
        y, mm, dd, h, m, s = time.localtime()[:6]
        if mm<10:
            timestamp = str(y)+'-'+'0'+str(mm)+'-'+str(dd) + ' ' + str(h)+':'+str(m)+':'+str(s)
        else:
            timestamp = str(y)+'-'+str(mm)+'-'+str(dd) + ' ' + str(h)+':'+str(m)+':'+str(s)
        
        
        data = {}
        data["deviceId"] = "raspberry_pi"
        data["temperature"] = temperature
        data["humidity"] = humidity
        data["timestamp"] = timestamp
        
        data = ujson.dumps(data)
        
        print(f'Publish: {data}')
        
        # Publish the data to the topic!
        mqtt_client.publish(mqtt_publish_topic, data)

        # Delay a bit to avoid hitting the rate limit
        time.sleep(10)
except Exception as e:
    print(f'Failed to publish message: {e}')
finally:
    mqtt_client.disconnect()