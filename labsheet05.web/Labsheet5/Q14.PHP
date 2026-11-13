import csv
import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

with open("users.csv", "r") as file:
    reader = csv.reader(file)

    next(reader)

    records = []

    for row in reader:
        records.append((row[0], row[1], int(row[2])))

sql = """
INSERT INTO users (name, email, age)
VALUES (%s, %s, %s)
"""

cur.executemany(sql, records)

con.commit()

print("CSV data inserted successfully!")

cur.close()
con.close()