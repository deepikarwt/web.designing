from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.orm import declarative_base, sessionmaker

engine = create_engine(
    "mysql+mysqlconnector://root:your_mysql_password@localhost/python_web_db"
)

Base = declarative_base()

class User(Base):
    __tablename__ = "orm_users"

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(100))
    email = Column(String(100))
    age = Column(Integer)

Base.metadata.create_all(engine)

Session = sessionmaker(bind=engine)
session = Session()

user1 = User(
    name="Aman",
    email="aman@gmail.com",
    age=22
)

session.add(user1)

session.commit()

print("User inserted using SQLAlchemy ORM!")

session.close()