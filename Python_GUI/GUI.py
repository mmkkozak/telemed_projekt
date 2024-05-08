from tkinter import *
from tkinter import ttk
from tkinter import messagebox
import socket
import threading
import time
import mysql.connector

magenta = "#EA0A8E"
green = "#21D375"
red = "#FF1919"

root = Tk()
root.geometry("600x400")
root.resizable(False, False)

regErrlabel1 = Label(root, text="Enter device name!", font=('Dotum 8'), fg=red)
regErrlabel2 = Label(root, text="Enter password!", font=('Dotum 8'), fg=red)
regErrlabel3 = Label(root, text="Device is already registered!", font=('Dotum 8'), fg=red)
sqlErrlabel1 = Label(root, text="No connection to the database. Registration failed.", font=('Dotum 8'), fg=red)
sqlPass = Label(root, text="Registration successful!", font=('Dotum 8'), fg=green)

def Register():
    
    E1_text = E1.get()
    E2_text = E2.get()
    in_use = False
    
    sqlPass.place_forget()
    
    if len(E1_text) == 0 or len(E2_text) == 0:
        if len(E1_text) == 0:
            regErrlabel1.place(relx=0.5, rely=0.49, anchor='center')
            if len(E2_text) != 0:
                regErrlabel2.place_forget()
        if len(E2_text) == 0:
            regErrlabel2.place(relx=0.5, rely=0.67, anchor='center')
            if len(E1_text) != 0:
                regErrlabel1.place_forget()
        root.update()
    else:
        regErrlabel1.place_forget()
        regErrlabel2.place_forget()
        isconn = True
        try:
            dbconn = mysql.connector.connect(
                host = "mysql.agh.edu.pl",
                user = "thespeck",
                password = "ukJQaDcQDYYSj99c",
                database = "thespeck")
        except:
            sqlErrlabel1.place(relx=0.5, rely=0.82, anchor='center')
            isconn = False
        finally:
            if isconn:
                
                sqlErrlabel1.place_forget()
                
                sql = dbconn.cursor()
                sql.execute("SELECT device_name FROM devices")
                device_name = sql.fetchall()
        
                resp_name = False
                resp_password = False
        
                if len(device_name) == 0:
                    comm = "INSERT INTO devices (device_name, user_email, user_password) VALUES (%s, %s, %s)"
                    val = (E1_text, "email", E2_text)
                    sql.execute(comm, val)
                    dbconn.commit()
                    sql.execute("SELECT device_name FROM devices")
                    device_name = sql.fetchall()
                    for name in device_name:
                        if name[0] == E1_text:
                            resp_name = True
                    if resp_name:
                        sqlPass.place(relx=0.5, rely=0.82, anchor='center')
                        
                    else:
                        pass
        
                else:
                    for name in device_name:
                        if E1_text == name[0]:
                            in_use = True
                            break
                        else:
                            pass
                if in_use:
                    regErrlabel3.place(relx=0.5, rely=0.49, anchor='center')
                else:
                    regErrlabel3.place_forget()
                    comm = "INSERT INTO devices (device_name, user_email, user_password) VALUES (%s, %s, %s)"
                    val = (E1_text, "email", E2_text)
                    sql.execute(comm, val)
                    dbconn.commit()
                    sql.execute("SELECT device_name FROM devices")
                    device_name = sql.fetchall()
                    for name in device_name:
                        if name[0] == E1_text:
                            resp_name = True
                    if resp_name:
                        sqlPass.place(relx=0.5, rely=0.82, anchor='center')
is_on = True

def ShowPassword():
    global is_on
    
    if is_on:
        B2.config(image=show)
        E2.config(show="*")
        is_on = False
        
    else:
        B2.config(image=hide)
        E2.config(show="")
        is_on = True

show = PhotoImage(file = "Python_GUI/show.png")
hide = PhotoImage(file = "Python_GUI/hide.png")

H1 = Label(root, text="Register your device", fg=magenta)
H1.config(font=("Dotum", 24 ))
H1.place(relx=0.5, rely=0.2, anchor='center')

H2 = Label(root, text="Device ID", fg=magenta)
H2.config(font=("Dotum", 16 ))
H2.place(relx=0.5, rely=0.37, anchor='center')

H3 = Label(root, text="Password", fg=magenta)
H3.config(font=("Dotum", 16 ))
H3.place(relx=0.5, rely=0.55, anchor='center')

E1 = Entry(root, justify='center', width=30)
E1.place(relx=0.5, rely=0.44, anchor='center')

E2 = Entry(root, justify='center', width=30, show="*")
E2.place(relx=0.5, rely=0.62, anchor='center')

B1 = Button(root, text="Register", command=Register)
B1.config(width=15, height=1)
B1.config(font=("Dotum", 12 ), fg=magenta, bd=2)
B1.place(relx=0.5, rely=0.75, anchor='center')

C1 = Canvas(root, width=30, height=30)
C1.place(relx=1.008, rely=1.028, anchor=SE)

B2 = Button(root, command=ShowPassword)
B2.config(width=17, height=16, bd=0)
B2.place(relx=0.68, rely=0.62, anchor='center')

conn_status = Label(root, font=("Dotum", 12))

def check_internet_connection():
    remote_server = "www.google.com"
    port = 80
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.settimeout(5)
    try:
        sock.connect((remote_server, port))
        return True
    except socket.error:
        return False
    finally:
        sock.close()

def IndicateConnection():
    while True:
        if check_internet_connection():
            conn_status.config(text="Connected to internet", fg=green)
            C1.create_oval(5,5,16,16, fill=green, outline=green)
            B1.config(state=NORMAL, bd=2)
        else:
            conn_status.config(text="No connection", fg=red)
            C1.create_oval(5,5,16,16, fill=red, outline=red)
            B1.config(state=DISABLED, bd=1)
        conn_status.place(rely=1.0, relx=0.95, x=0, y=0, anchor=SE)
        root.update()
        time.sleep(1)
        
ShowPassword()

t1 = threading.Thread(target=IndicateConnection)
t1.start()

root.mainloop()

