import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

user_id = int(input("Enter User ID: "))
name = input("Enter New Name: ")
email = input("Enter New Email: ")
age = int(input("Enter New Age: "))

sql = """
UPDATE users
SET name=%s, email=%s, age=%s
WHERE id=%s
"""

cur.execute(sql, (name, email, age, user_id))

con.commit()

if cur.rowcount > 0:
    print("User updated successfully!")
else:
    print("User ID not found.")

cur.close()
con.close()