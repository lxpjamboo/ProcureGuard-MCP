# scripts/init_db.py
import sqlite3
import os

def init_procureguard_db():
    db_path = 'procureguard.db'
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()

    # 1. Market Price Baselines Table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS market_baselines (
            item_code TEXT PRIMARY KEY,
            description TEXT NOT NULL,
            unit_baseline_kes REAL NOT NULL,
            category TEXT NOT NULL
        )
    ''')

    # 2. Corporate Registry Index Table (KRA / BRS Simulation)
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS corporate_registry (
            company_name TEXT PRIMARY KEY,
            kra_pin TEXT NOT NULL,
            director_identifier TEXT NOT NULL
        )
    ''')

    # Seed Baseline Sample Data
    baselines = [
        ('PUMP-7.5KW', 'Submersible Pump 7.5KW Heavy Duty', 216000.0, 'PUMPING_EQUIPMENT'),
        ('SOLAR-550W', 'Solar PV Panels 550W Monocrystalline', 28000.0, 'SOLAR_HARDWARE'),
        ('DRILL-200M', 'Hydro-geological Survey & Drilling 200m', 1300000.0, 'CIVIL_WORKS'),
        ('TANK-10000L', '10,000L Elevated Steel Storage Tank', 950000.0, 'STORAGE_HARDWARE')
    ]

    registry = [
        ('Apex Water Systems Ltd', 'A009845123Z', 'DIR-KEY-7781'),
        ('SunTech Innovations EA', 'A009845123Z', 'DIR-KEY-7781'),
        ('HydroFlow Hydrogeology Ltd', 'P051289301X', 'DIR-KEY-9902')
    ]

    cursor.executemany('INSERT OR REPLACE INTO market_baselines VALUES (?,?,?,?)', baselines)
    cursor.executemany('INSERT OR REPLACE INTO corporate_registry VALUES (?,?,?)', registry)

    conn.commit()
    conn.close()
    print("✓ SQLite baseline store successfully initialized: procureguard.db")

if __name__ == "__main__":
    init_procureguard_db()