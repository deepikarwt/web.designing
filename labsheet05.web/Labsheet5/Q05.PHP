import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

sql = """
INSERT INTO users (name, email, age)
VALUES (%s, %s, %s)
"""

records = [
    ("Aman", "aman@gmail.com", 22),
    ("Riya", "riya@gmail.com", 21),
    ("Rahul", "rahul@gmail.com", 23)
]

cur.executemany(sql, records)

con.commit()

print(cur.rowcount, "records inserted successfully!")

cur.close()
con.close()