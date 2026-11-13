import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password"
)

cur = con.cursor()

cur.execute("SELECT VERSION()")

version = cur.fetchone()

print("MySQL Server Version:", version[0])

cur.close()
con.close()