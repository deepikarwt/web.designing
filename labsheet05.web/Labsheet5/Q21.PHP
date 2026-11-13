import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

cur.execute("SELECT COUNT(*) FROM users")

total_users = cur.fetchone()[0]

cur.execute("SELECT AVG(age) FROM users")

average_age = cur.fetchone()[0]

cur.execute("SELECT MIN(age), MAX(age) FROM users")

minimum_age, maximum_age = cur.fetchone()

print("===== USER ANALYTICAL REPORT =====")

print("Total Users:", total_users)

if average_age is not None:
    print("Average Age:", round(average_age, 2))
else:
    print("Average Age: No records available")

print("Minimum Age:", minimum_age)

print("Maximum Age:", maximum_age)

cur.close()
con.close()