import mysql.connector

try:
    con = mysql.connector.connect(
        host="localhost",
        user="root",
        password="your_mysql_password",
        database="python_web_db"
    )

    cur = con.cursor()

    user_id = int(input("Enter User ID to delete: "))

    sql = "DELETE FROM users WHERE id=%s"

    cur.execute(sql, (user_id,))

    con.commit()

    if cur.rowcount > 0:
        print("Record deleted successfully!")
    else:
        print("User ID not found.")

except mysql.connector.Error as e:
    print("Database Error:", e)

except ValueError:
    print("Please enter a valid numeric ID.")

finally:
    if 'cur' in locals():
        cur.close()

    if 'con' in locals() and con.is_connected():
        con.close()