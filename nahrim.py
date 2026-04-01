import json
import sqlite3

def generate_nahrim_json():
    conn = sqlite3.connect("DatabaseForGroupProject.db")
    cursor = conn.cursor()

    nahrim_data = []

    for state_name in ['SELANGOR', 'KEDAH']:
        table_name = f"RF_{state_name}_AveRCP_Nahrim_Data"
        cursor.execute(f"""
            SELECT LATITUDE, LONGITUDE, AVG([Ave_RCP2.6]), AVG([Ave_RCP4.5]), AVG([Ave_RCP6.0]), AVG([Ave_RCP8.5])
            FROM {table_name}
            WHERE YEAR = 2026 AND MONTH IN (3, 4, 5)
            GROUP BY LATITUDE, LONGITUDE
        """)
        rows = cursor.fetchall()
        for row in rows:
            lat, lon, rcp26, rcp45, rcp60, rcp85 = row
            nahrim_data.append({
                "LATITUDE": lat,
                "LONGITUDE": lon,
                "Ave_RCP2.6": rcp26,
                "Ave_RCP4.5": rcp45,
                "Ave_RCP6.0": rcp60,
                "Ave_RCP8.5": rcp85
            })

    with open('nahrim_data.json', 'w') as f:
        json.dump(nahrim_data, f, indent=4)
    conn.close()
    print("NAHRIM data JSON generated.")

generate_nahrim_json()