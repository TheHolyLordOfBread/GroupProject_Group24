
import sqlite3
import json
import urllib3
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
    state_map = {
        0: "KEDAH",
        1: "SELANGOR",
    }
    weathercode_map = {
        0: "Clear sky", 1: "Mainly clear", 2: "Partly cloudy", 3: "Overcast",
        45: "Fog", 48: "Depositing rime fog", 51: "Light drizzle", 53: "Moderate drizzle", 55: "Dense drizzle",
        56: "Light freezing drizzle", 57: "Dense freezing drizzle", 61: "Slight rain", 63: "Moderate rain", 65: "Heavy rain",
        66: "Light freezing rain", 67: "Heavy freezing rain", 71: "Slight snow fall", 73: "Moderate snow fall", 75: "Heavy snow fall",
        77: "Snow grains", 80: "Slight rain showers", 81: "Moderate rain showers", 82: "Violent rain showers",
        85: "Slight snow showers", 86: "Heavy snow showers", 95: "Thunderstorm", 96: "Thunderstorm with slight hail", 99: "Thunderstorm with heavy hail",
    }
    for row in rows:
        h_id, name, address, state_code, longitude, latitude, type_code = row
        state_name = state_map.get(state_code, "Unknown")
        hosp_type = "Public" if type_code == 0 else "Private"
        hospital = {
            "NAME": name,
            "ADDRESS": address,
            "STATE": state_name,
            "LONGITUDE": longitude,
            "LATITUDE": latitude,
            "TYPE_OF_HOSPITAL": hosp_type,
            "rainfall_mm": '0.0000000'
        }
        urllib3.disable_warnings()
        url = f"https://api.open-meteo.com/v1/forecast?latitude={latitude}&longitude={longitude}&current_weather=true&daily=precipitation_sum&timezone=auto"
        try:
            weather_resp = requests.get(url, verify=False)
            weather_resp.raise_for_status()
            weather_data = weather_resp.json()
            if 'daily' in weather_data and 'precipitation_sum' in weather_data['daily']:
                rainfall = weather_data['daily']['precipitation_sum'][0]
                hospital['rainfall_mm'] = format(rainfall, '.7f')
            if 'current_weather' in weather_data:
                weathercode = weather_data['current_weather'].get('weathercode')
                hospital['condition'] = weathercode_map.get(weathercode, 'Unknown')
        except Exception as e:
            print(f"Error fetching weather for {name}: {e}")
        hospitals.append(hospital)
    with open('map_data.json', 'w') as f:
        json.dump(hospitals, f, indent=4)
    conn.close()
    print("Map data updated successfully.")

update_map_data()