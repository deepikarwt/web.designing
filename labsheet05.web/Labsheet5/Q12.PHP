import mysql.connector
from mysql.connector import pooling

db_pool = pooling.MySQLConnectionPool(
    pool_name="mypool",
    pool_size=5,
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

con = db_pool.get_connection()

cur = con.cursor()

cur.execute("SELECT * FROM users")

records = cur.fetchall()

for row in records:
    print(row)

cur.close()
con.close()

print("Connection pooling completed successfully!")