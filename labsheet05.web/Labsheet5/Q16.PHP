#!C:/Python312/python.exe

import mysql.connector
import json

print("Content-Type: application/json")
print()

try:
    con = mysql.connector.connect(
        host="localhost",
        user="root",
        password="your_mysql_password",
        database="python_web_db"
    )

    cur = con.cursor(dictionary=True)

    cur.execute("SELECT * FROM users")

    records = cur.fetchall()

    print(json.dumps(records))

    cur.close()
    con.close()

except Exception as e:
    print(json.dumps({"error": str(e)}))