
import json
import sqlite3
import requests
import time



def fetch_weather_data():
    conn = sqlite3.connect("DatabaseForGroupProject.db")
    db_cursor = conn.cursor()
    
    coords = set()
    for state_name in ['SELANGOR', 'KEDAH']:
        table_name = f"RF_{state_name}_AveRCP_Nahrim_Data"

        db_cursor.execute(f"SELECT DISTINCT LATITUDE, LONGITUDE FROM {table_name}")
        coords.update(db_cursor.fetchall())

    conn.close()

    rainfall_data = []

    url = "https://weather.googleapis.com/v1/forecast/days:lookup"
    api_key = "AIzaSyA_RFhMBhSY8pwXjZTIZ9hhrF9c6atOmKU"

    print(f"QPF for {len(coords)} points...")

    for lat, lon in coords:
        params = {

            "key": api_key,
            "location.latitude": lat,
            "location.longitude": lon,
            "days": 1,
            "pageSize": 1


        }


        res = requests.get(url, params=params, timeout=10)
        res.raise_for_status()
        data = res.json()


        first_day = data.get('forecastDays', [{}])[0]

        day_qpf = first_day.get('daytimeForecast', {}).get('precipitation', {}).get('qpf', {}).get('quantity', 0)
        night_qpf = first_day.get('nighttimeForecast', {}).get('precipitation', {}).get('qpf', {}).get('quantity', 0)
            

        total_rainfall = round(day_qpf + night_qpf, 2)

        rainfall_data.append({
            "LATITUDE": lat,
            "LONGITUDE": lon,
            "rainfall_mm": total_rainfall
        })
            
        print(f"Coord: {lat}, {lon} | Day: {day_qpf} | Night: {night_qpf} | Total: {total_rainfall}mm")



        time.sleep(0.1) 

    with open('google_live_rainfall.json', 'w') as f:
        json.dump(rainfall_data, f, indent=4)
    
    print(f"\n Saved to 'google_live_rainfall.json'")

if __name__ == "__main__":
    fetch_weather_data()