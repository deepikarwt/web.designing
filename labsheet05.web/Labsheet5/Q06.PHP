import mysql.connector

con = mysql.connector.connect(
    host="localhost",
    user="root",
    password="your_mysql_password",
    database="python_web_db"
)

cur = con.cursor()

cur.execute("SELECT * FROM users")

records = cur.fetchall()

html = """
<html>
<body>
<h2>User Records</h2>
<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Age</th>
</tr>
"""

for row in records:
    html += f"""
    <tr>
    <td>{row[0]}</td>
    <td>{row[1]}</td>
    <td>{row[2]}</td>
    <td>{row[3]}</td>
    </tr>
    """

html += "</table></body></html>"

print(html)

cur.close()
con.close()