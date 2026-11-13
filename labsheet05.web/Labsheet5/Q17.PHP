import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

cur.execute("SHOW CREATE TABLE users")

table = cur.fetchone()

with open("backup.sql", "w") as file:

    file.write(table[1] + ";\n\n")

    cur.execute("SELECT * FROM users")

    records = cur.fetchall()

    for row in records:

        values = []

        for value in row:
            if value is None:
                values.append("NULL")
            elif isinstance(value, str):
                values.append("'" + value.replace("'", "''") + "'")
            else:
                values.append(str(value))

        sql = "INSERT INTO users VALUES (" + ", ".join(values) + ");\n"

        file.write(sql)

print("Database backup created successfully!")

cur.close()
con.close()