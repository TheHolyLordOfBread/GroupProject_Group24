import json
import sqlite3

def generate_nahrim_json():
    conn = sqlite3.connect("DatabaseForGroupProject.db")
    cursor = conn.cursor()
    import datetime

    x = datetime.datetime.now()
    year = x.strftime("%Y")
    month = x.strftime("%m")
    day = x.strftime("%d")

    nahrim_data = []
    
    for state_name in ['SELANGOR', 'KEDAH']:
        table_name = f"RF_{state_name}_AveRCP_Nahrim_Data"
        cursor.execute(f"""
            SELECT *
            FROM {table_name}
            WHERE YEAR = ? AND MONTH = ? AND DAY = ?
            GROUP BY LATITUDE, LONGITUDE
        """,(year, month, day))
        rows = cursor.fetchall()
        for row in rows:
            year, month, day, lat, lon, rcp26, rcp45, rcp60, rcp85 = row
            nahrim_data.append({
                "YEAR": year,
                "MONTH": month,
                "DAY": day,
                "LATITUDE": lat,
                "LONGITUDE": lon,
                "Ave_RCP26": rcp26,
                "Ave_RCP45": rcp45,
                "Ave_RCP60": rcp60,
                "Ave_RCP85": rcp85
            })

    with open('nahrim_data.json', 'w') as f:
        json.dump(nahrim_data, f, indent=4)
    conn.close()
    print("NAHRIM data JSON generated.")

generate_nahrim_json()
