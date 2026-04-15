import json
import sqlite3
import requests
import time

def generate_live_rainfall_json():
    conn = sqlite3.connect("DatabaseForGroupProject.db")
    cursor = conn.cursor()

    
    coords = set()
    for state_name in ['SELANGOR', 'KEDAH']:
        table_name = f"RF_{state_name}_AveRCP_Nahrim_Data"
        cursor.execute(f"SELECT DISTINCT LATITUDE, LONGITUDE FROM {table_name}")
        coords.update(cursor.fetchall())
    conn.close()

    rainfall_data = []

    
    coord_list = list(coords)
    for i in range(0, len(coord_list), 50):
        batch = coord_list[i:i+50]
        lats = ",".join([str(c[0]) for c in batch])
        lons = ",".join([str(c[1]) for c in batch])

        url = f"https://api.open-meteo.com/v1/forecast?latitude={lats}&longitude={lons}&daily=precipitation_sum&timezone=Asia/Kuala_Lumpur&forecast_days=1"

        try:
            res = requests.get(url, timeout=30)
            res.raise_for_status()
            data = res.json()

            
            if isinstance(data, list):
                results = data
            else:
                results = [data]

            for idx, result in enumerate(results):
                lat, lon = batch[idx]
                
                precip = result['daily']['precipitation_sum'][0] if result['daily']['precipitation_sum'] else 0
                rainfall_data.append({
                    "LATITUDE": lat,
                    "LONGITUDE": lon,
                    "rainfall_mm": precip
                })

        except Exception as e:
            print(f"Error fetching batch {i}: {e}")

        time.sleep(1) # Be nice to the free API

    with open('live_rainfall.json', 'w') as f:
        json.dump(rainfall_data, f, indent=4)
    print(f"live_rainfall.json generated with {len(rainfall_data)} points.")

if __name__ == "__main__":
    generate_live_rainfall_json()