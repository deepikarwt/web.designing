import mysql.connector
import re

try:
    name = input("Enter Name: ").strip()
    email = input("Enter Email: ").strip()
    age = input("Enter Age: ").strip()

    if not name:
        raise ValueError("Name cannot be empty")

    if not re.match(r"^[\w.-]+@[\w.-]+\.\w+$", email):
        raise ValueError("Invalid email address")

    if not age.isdigit() or int(age) <= 0:
        raise ValueError("Age must be a positive number")

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

    cur.execute(sql, (name, email, int(age)))

    con.commit()

    print("User inserted successfully!")

    cur.close()
    con.close()

except ValueError as e:
    print("Input Error:", e)

except mysql.connector.Error as e:
    print("Database Error:", e)