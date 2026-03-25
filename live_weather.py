import csv
import json
import sqlite3
import urllib.request
import urllib3
from datetime import datetime, timedelta

try:
    import requests
except ImportError:
    print("requests module not found! Install with 'pip install requests'")
    exit()

def update_map_data():
    conn = sqlite3.connect("DatabaseForGroupProject.db")
    cursor = conn.cursor()
    cursor.execute("SELECT HOSPITAL_ID, NAME, ADDRESS, STATE, LONGITUDE, LATITUDE, TYPE_OF_HOSPITAL FROM HOSPITAL")
    rows = cursor.fetchall()
    hospitals = []

    for row in rows:
        h_id, name, address, state_code, longitude, latitude, type_code = row
        state_name = "KEDAH" if state_code == 0 else "SELANGOR"
        hosp_type = "Public" if type_code == 0 else "Private"
        hospitals.append({
            "NAME": name,
            "ADDRESS": address,
            "STATE": state_name,
            "LONGITUDE": longitude,
            "LATITUDE": latitude,
            "TYPE_OF_HOSPITAL": hosp_type
        })

    urllib3.disable_warnings()
    for hospital in hospitals:
        url = f"https://api.open-meteo.com/v1/forecast?latitude={hospital['LATITUDE']}&longitude={hospital['LONGITUDE']}&daily=rain_sum"
        weather_resp = requests.get(url, verify=False).json()
        daily = weather_resp['daily']
        hospital['rainfall_in'] = round(daily['rain_sum'][0] * 0.0393701, 2)  # mm to inches
        hospital['condition_code'] = weather_resp.get('current_weather', {}).get('weathercode', None)

    with open('map_data.json', 'w') as f:
        json.dump(hospitals, f, indent=4)
    print("Map data updated successfully.")

update_map_data()