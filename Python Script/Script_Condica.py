import time
import RPi.GPIO as GPIO #folosim RPI-LGPIO pentru compatibilitate cu Raspberry Pi 5. RPi-LGPIO emuleaza libraria RPi.GPIO.
from mfrc522 import SimpleMFRC522 #Libraria mfrc522 ne ajuta cu interfatarea modulelor RC522.
import mysql.connector
from RPLCD.i2c import CharLCD #Libraria RPLCD.i2c ne ajuta cu interfatarea ecranului LCD utilizand protocolul i2c
from datetime import datetime
import spidev
import os
import shutil
# Configuram pinii tastaturii matriciale. Ne referim la pini dupa numerele lor fizice de pe placa si nu la numaratoarea BCM. 
L1 = 40
L2 = 38
L3 = 36
L4 = 32
C1 = 37
C2 = 35
C3 = 33
C4 = 31
GPIO.setmode(GPIO.BOARD) # Setam modul BOARD pentru a utiliza pinii dupa numerele lor fizice.
BUZZER_PIN = 29 # Pinul Difuzorului
RELAY_PIN= 11 #Pinul Releului
GPIO.setup(BUZZER_PIN, GPIO.OUT) #Configuram difuzorul ca iesire
GPIO.setup(RELAY_PIN, GPIO.OUT) #Configuram releul ca iesire
########################################START-FUNCTIE-BUZZER#####################################################################  
def buzzer(pin, frequency, duration):
    # Calculam perioada pe baza frecventei tonului
    period = 1.0 / frequency
    
    # Timpul pentru jumatate de perioada
    half_period = period / 2.0
    
    # Calculate the number of cycles for the given duration
    cycles = int(duration / period)
    
    for _ in range(cycles):
        # Set the GPIO pin to HIGH
        GPIO.output(pin, GPIO.HIGH)
        
        # Wait for half of the period
        time.sleep(half_period)
        
        # Set the GPIO pin to LOW
        GPIO.output(pin, GPIO.LOW)
        
        # Wait for the other half of the period
        time.sleep(half_period)
########################################END-FUNCTIE-BUZZER#####################################################################  
              
GPIO.setwarnings(False)

cod_admin="123" #Codul pentru a intra in meniul de administrator
cod_in="" #Codul tastat 
########################################START-KEYPAD#####################################################################  
lines = [L1, L2, L3, L4] #Liniile tastaturii
columns = [C1, C2, C3, C4] #Coloanele tastaturii

for line in lines:
    GPIO.setup(line, GPIO.OUT) #Toate liniile sunt output

for column in columns:
    GPIO.setup(column, GPIO.IN, pull_up_down=GPIO.PUD_DOWN) 
    
def readLine(line, characters):
        GPIO.output(line, GPIO.HIGH)
        for idx, column in enumerate(columns):
            if GPIO.input(column) == 1:
                time.sleep(0.3) # Mic delay pentru a reduce posibilitatea de tastare multipla al unui caracter la apasare.
                if line == L1 and column == C4: #Conditie pentru a intra in meniul de administrator din bucla principala
                    admin_login()                          
                return characters[idx]                            
        GPIO.output(line, GPIO.LOW)

def keyboard():

         readLine(L1, ["1","2","3","A"])
         readLine(L2, ["4","5","6","B"])
         readLine(L3, ["7","8","9","C"])
         readLine(L4, ["*","0","#","D"])
########################################END-KEYPAD#####################################################################           
########################################START-MENIU-ADMIN#####################################################################
  
def admin_login():
    global cod_in
    lcd.clear()
    lcd.write_string("Introduceti codul deacces:")
    while True: #Bucla pentru citirea tastaturii
     s = readLine(L1, ["1","2","3","A"]) or readLine(L2, ["4","5","6","B"]) or readLine(L3, ["7","8","9","C"]) or readLine(L4, ["*","0","#","D"])
     if s =="D": #Iesire din meniul de autentificare.
         lcd.clear()
         cod_in=""
         break
     if s is not None: #Se introduce codul. Afisarea codului este mascata cu asterisk
      lcd.write_string("*")   
      cod_in+=s
     if len(cod_in) ==len(cod_admin) and cod_in == cod_admin: #Daca codul introdus corespunde cu codul admin intram in meniu.
      lcd.clear()
      cod_in=""
      meniu_admin()
      break
     elif len(cod_in)<len(cod_admin): #Daca lungimea codului introdus nu corespunde cu lungimea codului admin se continua cu tastarea. 
      pass
     else: #Daca codul este incorect iesim din functie.
      lcd.clear()
      lcd.cursor_pos=(0,3)
      lcd.write_string("~Cod incorect~")
      time.sleep(2)
      cod_in="" 
      break


def meniu_admin():
    lcd.clear()
    lcd.write_string("1. Inrolare")
    lcd.cursor_pos=(1,0)
    lcd.write_string("2. Export Raport")
    lcd.cursor_pos=(2,0)
    lcd.write_string("3. Vizualizare User")
    lcd.cursor_pos=(3,0)
    lcd.write_string("4. Stergere User")
    
    while True:
        key_press=(readLine(L1, ["1","2","3","A"]) or readLine(L2, ["4","5","6","B"]) or readLine(L4, ["*","0","#","D"]))
        
        if key_press=="1":
            lcd.clear()
            return inrolare()
        if key_press=="2":
            lcd.clear()
            return export_usb()
        if key_press=="3":
            lcd.clear()
            return vizualizare_user()   
        if key_press=="4":
            lcd.clear()  
            return stergere_user() 
        if key_press=="D":
            lcd.clear()
            break     

########################################END-MENIU-ADMIN#####################################################################              
########################################START-FUNCTIE-VIZUALIZARE-USER#####################################################################   
#Selectam utilizatorii din baza de date         
def utilizatori():
    cursor.execute("SELECT id, nume FROM utilizator")
    return cursor.fetchall()
#Afisam utilizatorii
def display(user, start_index): #Unde user reprezinta lista de utilizatori extrasa din baza de date si start_index reprezinta indexul de la care sa inceapa afisarea.
    for i in range(4): #Iteram de patru ori pentru cele 4 linii ale ecranului LCD
        user_index = start_index + i #index utilizator pt. afisare pe randul curent
        if user_index < len(user): # Conditie pentru a nu indexa in afara celor 4 linii ale ecranului
            user_id, user_name = user[user_index] #Extragem ID-ul si numele utilizatorului cu indexul user_index
            lcd.cursor_pos = (i, 0) # Unde i este pozitia randului unde se afiseaza utilizatorii. Se afiseaza initial primii 4 utilizatori din lista.
            lcd.write_string(f"ID{user_id}- {user_name}") 

def vizualizare_user():
    user = utilizatori()
    total_user = len(user)
    start_index = 0
    while True:
        display(user, start_index) 
        key_press = (readLine(L3, ["7","8","9","C"]) or readLine(L2, ["4", "5", "6", "B"]) or readLine(L4, ["*","0","#","D"]))
        if key_press == "C": #Scroll in jos pentru a afisa mai multi utilizatori
            lcd.clear()
            start_index += 1 #Se afiseeaza cate un utilizator
            if start_index >= total_user: # Cand terminam lista de utilizatori indexul va fi 0
                lcd.clear()
                start_index = 0
        elif key_press == "B": #Scroll in sus pentru a afisa utilizatorii precedenti
            lcd.clear()
            start_index -= 1 
            if start_index < 0:
                lcd.clear()
                start_index = total_user - 1 #In cazul in care derulam in sus si index-ul incepe de la 0 afisam ultimul utilizator din lista.
        elif key_press == "D": #Revenim in meniul de administrator
            return meniu_admin()

########################################START-FUNCTIE-STERGERE-USER#####################################################################
def stergere_user():
    lcd.write_string("Indicati ID-ul")
    lcd.cursor_pos=(1,0)
    lcd.write_string("Dupa apasati 'B'")
    input_id="" #Aici stocam id-ul introdus
    while True:
        key_press = (readLine(L1, ["1","2","3","A"]) or readLine(L2, ["4", "5", "6", "B"]) or readLine(L3, ["7","8","9","C"]) or readLine(L4, ["*","0","#","D"]))
        if key_press == "D": #Revenire in meniul de administrator
            return meniu_admin()
        if key_press is not None:    
         input_id+=key_press 
         lcd.cursor_pos=(2,0)
         lcd.write_string(input_id) #Afisam ID-ul tastat
        if key_press == "B": #Verificare ID
                utilizator=query_id(input_id) #apelam functia query_id cu id-ul tastat
                lcd.clear()
                if utilizator: #Daca ID-ul este prezent in baza de date
                    nume=utilizator[0] #numele primit
                    lcd.write_string("Stergeti utilizatorul")
                    lcd.cursor_pos=(1,0)
                    lcd.write_string(f"{nume} ?")
                    lcd.cursor_pos=(2,0)
                    lcd.write_string("B-Da C-Nu")
                    while True: 
                     key_press_confirm = readLine(L2, ["4", "5", "6", "B"]) or readLine(L3, ["7","8","9","C"])
                    
                     if key_press_confirm == "B": #Stergem user-ul
                        lcd.clear()
                        query_stergere(input_id)#apelam functia de stergere
                        lcd.write_string("Utilizator sters.")
                        time.sleep(2)
                        lcd.clear()
                        return stergere_user()
                     elif key_press_confirm == "C": #Anulam stergerea
                        lcd.clear()
                        return stergere_user()  
                else:# Daca ID-ul nu este gasit
                    lcd.write_string("ID invalid")
                    time.sleep(2)
                    lcd.clear()
                    return stergere_user()    
        if key_press == "#":# Daca gresim tastarea
         lcd.clear()
         return stergere_user()
def query_id(id_user): #functia pentru a returna utilizatorul cu id-ul id_user
    query="SELECT nume FROM utilizator WHERE id=%s" #query unde selectam numele
    cursor.execute(query, (id_user,)) #interogare SQL. Metoda execute primeste doua argumente, query-ul si tuplul cu un singur element care reprezinta id-ul.
    user= cursor.fetchone() #fetchone() returneaza un singur rezultat
    return user #returnam numele primit
def query_stergere(id_user): #functia pentru a sterge un utilizator cu id-ul id_user
    query="DELETE FROM utilizator WHERE id=%s"
    cursor.execute(query, (id_user,))
    db.commit()   
########################################START-FUNCTIE-EXPORT-USB#####################################################################  
def export_usb():
    data = datetime.now().strftime('%Y-%m-%d') #Obtinem data curenta

    nume_folder = f"Raport_{data}" #Numele folder-ului va fi sub forma Raport_ urmat de data curenta

    
    home = os.path.expanduser("~") # "~" reprezinta directorul principal in linux iar cu expanduser obtinem calea completa: /home/sebastian
    folder_raport = os.path.join(home, nume_folder) #concatenam directorul principal cu numele folder-ului unde stocam raportul
    os.makedirs(folder_raport, exist_ok=True) #crearea folder-ului

    #query pentru a obtine datele raportului
    cursor.execute("SELECT user_id, stare, nume, data_ora FROM condica WHERE DATE(data_ora) = %s", (data,))
    prezenta_data = cursor.fetchall()

    csv_file_path = os.path.join(folder_raport, "raport_prezenta.csv") # stocam fisierul .csv in folder-ul creat
    with open(csv_file_path, "w") as csv_file: #deschidem fisierul in write mode, "with" se foloseste pentru a inchide  fisierul dupa scriere 
        csv_file.write("User_ID,Stare,Nume,data_si_ora\n") #coloanele fisierului
        for item in prezenta_data:#iteram prin datele prezentei
            csv_file.write(f"{item[0]},{item[1]},{item[2]},{item[3]}\n") #scriem datele primite in fisier, se incepe o linie noua pentru a continua cu urmatorul set de date.

    # obtinem calea (daca exista) unde stocham folderul cu fisierul .csv 
    destinatie_usb = primul_folder_usb()

    if destinatie_usb: #daca am obtinut calea, fapt ce inseamna ca este un usb conectat
        destinatie_folder_usb = os.path.join(destinatie_usb, nume_folder) #unde folder_raport este calea catre usb iar nume_folder este numele fisierului raportului
        try:
         shutil.copytree(folder_raport, destinatie_folder_usb) #copiam folder-ul cu raportul pe usb
         lcd.write_string("Raport copiat pe USB")
        except FileExistsError: #daca raportul de astazi exista pe usb
         lcd.write_string("Raport existent")    
        time.sleep(2)
        return meniu_admin()
    else:# daca nu exista calea
        
        lcd.write_string("Nici un USB gasit")
        time.sleep(2)
        return meniu_admin()

def primul_folder_usb(): #functie care returneaza calea catre primul folder usb pe care il gasim
    directory_path = '/media/sebastian' #calea unde sunt montate stick-urile USB pe linux
    items = os.listdir(directory_path) #lista cu folderele (daca exista) din /media
    for item in items: #parcurgem lista cu foldere
        item_path = os.path.join(directory_path, item) #concatenam cu calea catre un posibil folder usb
        if os.path.isdir(item_path): #daca exista un folder returnam calea catre el
            return item_path

    # Nu exista un folder in /media
    return None

########################################END-FUNCTIE-EXPORT-USB##################################################################### 
########################################START-FUNCTIE-INROLARE#####################################################################           
def inrolare():
 lcd.write_string('*Apropiati cardul ptinregistrare*')     
 while True: #bucla pentru a interoga cititorul de intrare
  id1, text1 = nfc.read("reader1")
  key_press = readLine(L4, ["*","0","#","D"])
  if key_press == "D": #inapoi in meniul de administrator
      return meniu_admin()
  if id1 is not None: #daca primim un UID
   print("ID from reader 1:", str(id1))
   cursor.execute("SELECT id from utilizator WHERE rfid_uid="+str(id1)) #unde id1 este UID-ul primit de la cititor
   cursor.fetchone() #selectam primul rezultat din baza de date
   if cursor.rowcount >=1: #caz in care exista deja un utilizator inregistrat cu tag-ul respectiv
    buzzer(BUZZER_PIN, 970, 0.1) #feedback sonor
    buzzer(BUZZER_PIN, 1050, 0.1)
    lcd.clear()
    lcd.write_string("Card deja existent  in baza de date")
    lcd.cursor_pos = (3,0)
    lcd.write_string("Suprascrieti? (y/n)") 
    overwrite = input("Suprascriere ? (Y/N): ") # Pentru a suprascrie un utilizator deja inregistrat, putem tasta "y" pentru a-l suprascrie sau "n" pentru a anula folosind o tastatura usb conectata la raspberry.
    if overwrite[0] =='y':
     lcd.clear()
     lcd.write_string("Asteptati...")
     time.sleep(1) 
     sql_update = "UPDATE utilizator SET nume = %s WHERE rfid_uid = %s" #actualizarea utilizatorului utilizand un query. Elementele %s reprezinta datele noi
     lcd.clear()
     lcd.write_string('Introduceti un nume')
     nume_nou = input('Nume: ') #se introduce de la tastatura numele nou al utilizatorului
     cursor.execute(sql_update, (nume_nou, id1)) #query-ul anterior cu un tuplu care contine numele nou si UID-ul.
     db.commit() #salvare
     lcd.clear()
     lcd.write_string("Utilizatorul ")
     lcd.cursor_pos=(1,0)
     lcd.write_string(nume_nou)
     lcd.cursor_pos=(2,0)
     lcd.write_string("Actualizat")
     buzzer(BUZZER_PIN, 1050, 0.1)
     buzzer(BUZZER_PIN, 1100, 0.1)
     time.sleep(2)
     lcd.clear()
     return inrolare() 
    elif overwrite[0] =='n': #Anulare suprascriere
     lcd.clear()
     return inrolare()
   else: #utilizator nou
    sql_insert = "INSERT INTO utilizator (nume, rfid_uid) VALUES (%s, %s)"
    lcd.clear()
    lcd.write_string('Introduceti un nume')
    nume_nou = input('Nume: ')
    cursor.execute(sql_insert, (nume_nou, id1))
    db.commit()
    lcd.clear()
    lcd.write_string("Utilizatorul ")
    lcd.cursor_pos=(1,0)
    lcd.write_string(nume_nou)
    lcd.cursor_pos=(2,0)
    lcd.write_string("Salvat")
    buzzer(BUZZER_PIN, 1050, 0.1)
    buzzer(BUZZER_PIN, 1100, 0.1)
    time.sleep(2)
    lcd.clear()
    return inrolare()
########################################END-FUNCTIE-INROLARE#####################################################################  
########################################START-CLASA-RFID#####################################################################     
# Clasa destinata comunicarii si gestionarii cititoarelor RC522 conectate in paralel utilizand protocolul SPI: https://forums.raspberrypi.com/viewtopic.php?t=334477     
class NFC():
    
    def __init__(self, bus=0, device=0, spd=1000000): #spd=1MHz
        self.reader = SimpleMFRC522()
        self.close()
        self.boards = {}
        self.bus = bus
        self.device = device
        self.spd = spd

    def reinit(self): #reinitializeaza conexiunea SPI cu parametrii din constructor
        self.reader.READER.spi = spidev.SpiDev()
        self.reader.READER.spi.open(self.bus, self.device)
        self.reader.READER.spi.max_speed_hz = self.spd
        self.reader.READER.MFRC522_Init()

    def close(self): #Inchide conexiunea SPI
        self.reader.READER.spi.close()

    def addBoard(self, rid, pin): #initializam cititoarele, pin reprezinta GPIO la care este conectat pin-ul de RST al placutelor RC522
        GPIO.setup(pin,GPIO.OUT)
        self.boards[rid] = pin

    def selectBoard(self, rid):
        if not rid in self.boards:
            print("readerid " + rid + " not found")
            return False

        for loop_id in self.boards:
            GPIO.output(self.boards[loop_id], loop_id == rid)
        return True

    def read(self, rid): #citim UID dupa inchidem conexiunea
        if not self.selectBoard(rid):
            return None

        self.reinit()
        uid = self.reader.read_no_block()
        self.close()
        if uid:
         return uid
        else:
            print("No UID")
            return None
########################################END-CLASA-RFID#####################################################################
#initializare ecran LCD prin protocolul i2c  
lcd = CharLCD(i2c_expander='PCF8574', address=0x27, port=1, cols=20, rows=4, dotsize=8)
lcd.clear()
db=mysql.connector.connect( #datele conexiunii cu baza de date
 host="localhost",
 user="rfidreader",
 passwd="password",
 database="rfidtest"
)
cursor=db.cursor()
########################################START-BUCLA-PRINCIPALA#####################################################################  
try:
    nfc = NFC() #instanta clasei NFC
    nfc.addBoard("reader1", 18) #adaugam cele 2 placi utilizand metoda clasei nfc "addBoard" cu pinii de RST ale cititorilor 
    nfc.addBoard("reader2", 22)
    while True:
        keyboard() 
        lcd.cursor_pos = (0, 1)
        lcd.write_string('~Apropiati cardul~')
        lcd.cursor_pos = (3, 0)
        lcd.write_string(datetime.now().strftime('%d/%b/%Y %H:%M:%S'))
        ######### cititorul de intrare ################################################################################################
        id1, text1 = nfc.read("reader1") 
        if id1 is not None: #daca primim un UID
            buzzer(BUZZER_PIN, 1050, 0.1)  
            print("ID cititor 1:", str(id1)) 
            cursor.execute("SELECT id, nume FROM utilizator WHERE rfid_uid=%s", (str(id1),)) #selectam id-ul si numele utilizatorului care corespunde codului primit
            result1 = cursor.fetchone() #stocam id-ul si numele
            lcd.clear()
            if cursor.rowcount >= 1: #daca exista un utilizator cu codul respectiv
                GPIO.output(RELAY_PIN, GPIO.HIGH) #deschidem releul
                lcd.cursor_pos = (0, 5)
                lcd.write_string("~Bun venit~")
                lcd.cursor_pos = (1, 0)
                lcd.write_string(result1[1]) #afisam numele pe ecranul LCD
                lcd.cursor_pos = (3, 0)
                lcd.write_string(datetime.now().strftime('%d/%b/%Y %H:%M:%S')) 
                cursor.execute("INSERT INTO condica (user_id,nume,stare) VALUES (%s,%s,'intrare')", (result1[0],result1[1])) #inseram in tabela condica utilizatorul care a intrat
                db.commit()
            else: #daca nu exista un utilizator cu codul primit
                lcd.cursor_pos = (0, 3)
                lcd.write_string("~Card invalid~")
                buzzer(BUZZER_PIN, 760, 0.1)
                cursor.execute("INSERT INTO condica (user_id,nume,stare) VALUES ('0','*Incercare de intrare nereusita*','intrare')") #inseram in tabela condica incercarea de intrare nereusita
                db.commit()
            time.sleep(2) 
            GPIO.output(RELAY_PIN, GPIO.LOW) #inchidem releul dupa 2 secunde
            lcd.clear()
        ######### cititorul de iesire ################################################################################################
        id2, text2 = nfc.read("reader2")
        if id2 is not None: #daca primim un UID        
            print("ID cititor 2:", str(id2))
            cursor.execute("SELECT id, nume FROM utilizator WHERE rfid_uid=%s", (str(id2),)) #selectam id-ul si numele utilizatorului care corespunde cu UID-ul primit
            result2 = cursor.fetchone()#stocam id-ul si numele utilizatorului
            lcd.clear()
            if cursor.rowcount >= 1: #daca exista un utilizator cu codul respectiv  
             while True:   #Bucla pentru a inregistra o iesire sau o pauza
              key_press = readLine(L3, ["7","8","9","C"]) or readLine(L2, ["4","5","6","B"])
              lcd.cursor_pos = (0, 0)
              lcd.write_string('~Selectati o optiune~')
              lcd.cursor_pos = (1,0)
              lcd.write_string("B - Pauza")
              lcd.cursor_pos = (2,0)
              lcd.write_string("C - Plecare")
              if (key_press ==  "C"): #utilizatorul a iesit
               lcd.clear()
               buzzer(BUZZER_PIN, 900, 0.1)
               GPIO.output(RELAY_PIN, GPIO.HIGH)
               lcd.cursor_pos = (0, 3)
               lcd.write_string("~La revedere~")
               lcd.cursor_pos = (1, 0)
               lcd.write_string(result2[1])
               lcd.cursor_pos = (3, 0)
               lcd.write_string(datetime.now().strftime('%d/%b/%Y %H:%M:%S'))
               cursor.execute("INSERT INTO condica (user_id,nume,stare) VALUES (%s,%s,'iesire')", (result2[0],result2[1])) #inseram in tabela condica utilizatorul care a iesit
               db.commit()
               break
              elif (key_press == "B"): #utilizatorul a intrat in pauza
               lcd.clear()
               buzzer(BUZZER_PIN, 900, 0.1)
               GPIO.output(RELAY_PIN, GPIO.HIGH)
               lcd.cursor_pos = (0, 1)
               lcd.write_string("~Pauza inceputa !~")
               lcd.cursor_pos = (1, 0)
               lcd.write_string(result2[1])
               lcd.cursor_pos = (3, 0)
               lcd.write_string(datetime.now().strftime('%d/%b/%Y %H:%M:%S'))
               cursor.execute("INSERT INTO condica (user_id,nume,stare) VALUES (%s,%s,'pauza')", (result2[0],result2[1])) #inseram in tabela condica utilizatorul care a intrat in pauza
               db.commit()
               break    
            else:
                lcd.cursor_pos = (0, 3)
                lcd.write_string("~Card invalid~")
                buzzer(BUZZER_PIN, 760, 0.1)
                cursor.execute("INSERT INTO condica (user_id,nume,stare) VALUES ('0','*Incercare de iesire nereusita*','iesire')")  #inseram in tabela condica incercarea de iesire nereusita
                db.commit()
            time.sleep(2) 
            GPIO.output(RELAY_PIN, GPIO.LOW) 
            lcd.clear()
            
finally:
    GPIO.cleanup()
