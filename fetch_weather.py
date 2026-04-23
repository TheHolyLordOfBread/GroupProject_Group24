import csv
import sqlite3
import requests
from datetime import datetime, timedelta
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
from datetime import datetime, timedelta

DB_FILE = "DatabaseForGroupProject.db"
HOSPITAL_TABLE = "HOSPITAL"

def fetch_hospital_data():
    try:
        print("Connecting to database...")
        conn = sqlite3.connect(DB_FILE)
        cursor = conn.cursor()
        cursor.execute(f"SELECT HOSPITAL_ID, NAME, STATE, LATITUDE, LONGITUDE FROM {HOSPITAL_TABLE}")
        hospitals = cursor.fetchall()
        conn.close()
        print(f"Fetched {len(hospitals)} hospitals from database")
        return hospitals
    except sqlite3.Error as e:
        print(f"Error fetching hospital data: {e}")
        return []

def fetch_rainfall_data(hospitals):
    start_date = datetime.today().date()
    end_date = start_date + timedelta(days=13)
    data = {}
    state_map = {0: 'KEDAH', 1: 'SELANGOR'}

    rows = []
    for h_id, name, state, lat, lon in hospitals:
        url = f"https://api.open-meteo.com/v1/forecast?latitude={lat}&longitude={lon}&daily=precipitation_sum&start_date={start_date}&end_date={end_date}&timezone=auto"
        try:
            print(f"Fetching rainfall data for Hospital {name} (ID: {h_id})...")
            res = requests.get(url, verify=False).json()
            state_name = state_map.get(state, 'Unknown')
            daily = res.get("daily", {})
            dates = daily.get("time", [])
            rainfall = daily.get("precipitation_sum", [])
            for i in range(len(dates)):
                date_parts = dates[i].split('-')
                rows.append({
                    'Year': date_parts[0],
                    'Month': date_parts[1],
                    'Day': date_parts[2],
                    'Hospital': name,
                    'State': state_name,
                    'Rainfall (mm)': rainfall[i]
                })
            print(f"Fetched rainfall data for Hospital {name} (ID: {h_id})")
        except Exception as e:
            print(f"Error fetching data for Hospital {name} (ID: {h_id}): {e}")
    print(f"Fetched rainfall data for {len(rows)} days")
    return rows

def write_to_csv(rows):
    fieldnames = ['Year', 'Month', 'Day', 'Hospital', 'State', 'Rainfall (mm)']
    import os
    file_exists = os.path.isfile('openmeteo.csv')
    with open('openmeteo.csv', 'a', newline='') as csvfile:
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        if not file_exists:
            writer.writeheader()
        writer.writerows(rows)
    print(f"Wrote data to openmeteo.csv")

hospitals = fetch_hospital_data()
rainfall_data = fetch_rainfall_data(hospitals)
write_to_csv(rainfall_data)
print("Done!")