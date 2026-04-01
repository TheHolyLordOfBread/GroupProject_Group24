import csv
import json
import sqlite3
import urllib.request
from datetime import datetime, timedelta
try:
    import requests
except ImportError:
    requests = None

DB_FILE = "DatabaseForGroupProject.db"
HOSPITAL_TABLE = "HOSPITAL"

def fetch_hospital_data():
    try:
        conn = sqlite3.connect(DB_FILE)
        cursor = conn.cursor()
        cursor.execute(f"SELECT HOSPITAL_ID, NAME, STATE, LATITUDE, LONGITUDE FROM {HOSPITAL_TABLE}")
        hospitals = cursor.fetchall()
        conn.close()
        return hospitals
    except sqlite3.Error as e:
        print(f"Error fetching hospital data: {e}")
        return []

def fetch_rainfall_data(hospitals):
    yesterday = (datetime.now() - timedelta(days=1)).strftime('%Y-%m-%d')
    data = {}
    state_map = {0: 'KEDAH', 1: 'SELANGOR'}
    import urllib3
    urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
    for h_id, name, state, lat, lon in hospitals:
        url = f"https://archive-api.open-meteo.com/v1/archive?latitude={lat}&longitude={lon}&start_date={yesterday}&end_date={yesterday}&hourly=precipitation&timezone=Asia/Singapore"
        try:
            if requests is not None:
                res = requests.get(url, verify=False).json()
            else:
                with urllib.request.urlopen(url) as response:
                    res = json.load(response)
            state_name = state_map.get(state, 'Unknown')
            daily_rainfall_mm = sum(res["hourly"]["precipitation"])
            data[(name, state_name)] = {
                'Year': yesterday.split('-')[0],
                'Month': yesterday.split('-')[1],
                'Day': yesterday.split('-')[2],
                'Hospital': name,
                'State': state_name,
                'Rainfall (mm)': daily_rainfall_mm
            }
        except Exception as e:
            print(f"Error fetching data for Hospital {name} (ID: {h_id}): {e}")
    return list(data.values())

def write_to_csv(rows):
    fieldnames = ['Year', 'Month', 'Day', 'Hospital', 'State', 'Rainfall (mm)']
    import os
    file_exists = os.path.isfile('daily_rainfall_data.csv')
    try:
        with open('daily_rainfall_data.csv', 'a', newline='') as csvfile:
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            if not file_exists:
                writer.writeheader()
            writer.writerows(rows)
    except Exception as e:
        print(f"Error writing data to CSV: {e}")

if __name__ == "__main__":
    hospitals = fetch_hospital_data()
    data = fetch_rainfall_data(hospitals)
    write_to_csv(data)
    print("Wrote data to daily_rainfall_data.csv")