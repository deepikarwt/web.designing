from flask import Flask, session, request
import mysql.connector

app = Flask(__name__)

app.secret_key = "my_secret_key"

def connect_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="your_mysql_password",
        database="python_web_db"
    )

@app.route("/", methods=["GET", "POST"])
def login():

    if request.method == "POST":

        user_id = request.form["id"]

        con = connect_db()
        cur = con.cursor()

        cur.execute(
            "SELECT id, name FROM users WHERE id=%s",
            (user_id,)
        )

        user = cur.fetchone()

        cur.close()
        con.close()

        if user:
            session["user_id"] = user[0]
            session["user_name"] = user[1]

            return "Welcome " + user[1]

        return "User not found"

    return """
    <form method="post">
        Enter User ID:
        <input type="number" name="id" required>
        <input type="submit" value="Login">
    </form>
    """

@app.route("/logout")
def logout():

    session.clear()

    return "Logged out successfully!"

if __name__ == "__main__":
    app.run(debug=True)