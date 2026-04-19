import time
import requests
import sqlite3
import json
import csv

from datetime import datetime

DB = "DatabaseForGroupProject.db"
HOSPITAL = "HOSPITAL"

API_KEY = "AIzaSyA_RFhMBhSY8pwXjZTIZ9hhrF9c6atOmKU" 
API_URL = "https://weather.googleapis.com/v1/forecast/days:lookup"

def get_hospital_data():
        conn = sqlite3.connect(DB)

        db_cursor = conn.cursor()
        db_cursor.execute (f"SELECT NAME, STATE, LATITUDE, LONGITUDE FROM {HOSPITAL}")
        hospitals = db_cursor.fetchall()


        conn.close()
        return hospitals


def fetch_rainfall_data(hospitals):
    state_map = {0: 'KEDAH', 1: 'SELANGOR'}

    output_File = "daily_rainfall_data_Google.csv"

    with open(output_File, 'w', newline='') as c:
        writer = csv.writer(c, delimiter=',', quoting=csv.QUOTE_NONE, escapechar=' ')
        
        writer.writerow(["year","month","day", "hospital", "State", "Rainfall (mm)"])

        for name, state, lat, lon in hospitals:
            state_name = state_map.get(state, state)

            params = {
                "key": API_KEY,
                "location.latitude": lat,
                "location.longitude": lon,
                "days": 10,
                "pageSize": 10
            } #malaysia doesnt support 10 days by default

            try:
                get = requests.get(API_URL, params=params, timeout=10)
                get.raise_for_status()

                forecast_days = get.json().get('forecastDays', [])

                for day_data in forecast_days:
                    d_info = day_data.get('displayDate', {})
                    y = str(d_info.get('year', ''))
                    m = str(d_info.get('month', ''))
                    d = str(d_info.get('day', ''))

                    day_qpf = day_data.get('daytimeForecast', {}).get('precipitation',{}).get('qpf', {}).get('quantity', 0)
                    night_qpf = day_data.get('nighttimeForecast', {}).get('precipitation', {}).get('qpf', {}).get('quantity', 0)

                    total_rainfall = round(day_qpf + night_qpf, 2)

                    writer.writerow([y, m, d, name, state_name, total_rainfall])

                print(f"Finished: {name}, {y}-{m}-{d}")
                
            except requests.RequestException as e:
                print(f"Request failed for {name}:{e}")  
            
            time.sleep(0.1)

if __name__ == "__main__":
    hospital_list = get_hospital_data()
    if hospital_list:
        fetch_rainfall_data(hospital_list)
        print("\nSuccessfully wrote to daily_rainfall_data_Google.csv")
    else:
        print("finished all hospitals.")