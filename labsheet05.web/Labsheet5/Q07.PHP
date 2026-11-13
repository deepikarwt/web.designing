#!C:/Python312/python.exe

import cgi
import mysql.connector
import html

print("Content-Type: text/html")
print()

form = cgi.FieldStorage()

name = form.getfirst("name", "")
email = form.getfirst("email", "")
age = form.getfirst("age", "")

try:
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

    print("<h2>User inserted successfully!</h2>")

    cur.close()
    con.close()

except Exception as e:
    print("<h2>Error inserting user</h2>")
    print(html.escape(str(e)))