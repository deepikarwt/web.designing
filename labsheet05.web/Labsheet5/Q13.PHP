import mysql.connector
import logging

logging.basicConfig(
    filename="database.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)

try:
    con = mysql.connector.connect(
        host="localhost",
        user="root",
        password="your_mysql_password",
        database="python_web_db"
    )

    cur = con.cursor()

    cur.execute("SELECT * FROM users")

    records = cur.fetchall()

    print("Records retrieved successfully!")

    logging.info("Database accessed successfully.")

    cur.close()
    con.close()

except mysql.connector.Error as e:
    print("Database error:", e)
    logging.error("Database error: %s", e)