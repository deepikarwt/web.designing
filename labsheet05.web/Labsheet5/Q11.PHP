import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

name = input("Enter name to search: ")

sql = "SELECT * FROM users WHERE name=%s"

cur.execute(sql, (name,))

records = cur.fetchall()

for row in records:
    print(row)

cur.close()
con.close()