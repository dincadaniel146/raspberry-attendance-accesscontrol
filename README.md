# Sistem de control al accesului si condica electronica bazat pe Raspberry Pi 5
## Introducere

Scopul principal al acestui proiect este să ofere un sistem conceput pentru a eficientiza operațiile de acces si monitorizare a prezenței și a timpului de lucru in cadrul unei întreprinderi mici cât și în alte domenii.

Prin plasarea a două cititoare de proximitate în locații diferite, sistemul va interoga baza de date care conține numărul de identificare al utilizatorilor cu numărul furnizat de cititoare iar pe baza acestor informații poate interzice sau permite accesul într-o incintă activând un releu.

Folosind portul Ethernet furnizat de către placă, aceasta se conectează în rețeaua locală de calculatoare iar administratorul are posibilitatea de a accesa de pe orice dispozitiv cu un browser aplicația web destinată vizualizării datelor condicii cu funcțiile specifice acesteia precum rapoarte privind prezența angajaților, exportul rapoartelor către un fișier Excel, administrarea utilizatorilor existenți sau înrolarea unui utilizator nou.

Deși terminalul principal este menit să fie conectat in rețeaua locala de calculatoare pentru vizualizarea datelor corespunzătoare, acesta poate funcționa și local prin intermediul meniului text destinat administratorului afișat pe ecranul LCD.

Sistemul cuprinde terminalul principal reprezentat de Raspberry Pi 5 alături de o tastatură matriciala cu 16 taste, un ecran LCD care oferă diverse informații, un buzer care semnalizează sonor statusul intrării sau ieșirii, releul electromagnetic și cele două cititoare de proximitate RC522.

Cititoarele de proximitate deservesc funcții diferite, astfel, primul cititor, cel responsabil de acțiunea de intrare este menit a fi pus în afara încăperii unde se controlează accesul iar cel de ieșire în interiorul încăperii respective.

## Componente Hardware utilizate
Partea fizică a sistemului propus este alcătuită din:

•	***Două cititoare de proximitate RC522***

•	***Raspberry Pi 5 alături de un card microSD de 32GB***

•	***Ecran LCD 2004 cu 20 de linii și 4 coloane***

•	***Buzer piezoelectric***

•	***Tastatură matriciala cu 4 linii și 4 coloane***

•	***Carduri de proximitate MIFARE Classic 1K***

•	***28 de cabluri Dupont cu lungimea de 15 cm***

•	***Adaptor I2C pentru ecran LCD***

•	***Releu electromagnetic 5V***
## Schema bloc

[![Captur-de-ecran-2024-08-23-174838.png](https://i.postimg.cc/sfP0GjVR/Captur-de-ecran-2024-08-23-174838.png)](https://postimg.cc/LhXVrMYy)
## Modul de functionare

Odată cu pornirea sistemului, ecranul LCD va afișa informații relevante utilizatorului astfel, când sistemul este în așteptarea unui card de proximitate acesta va afișa “Așteptare card” cu ora curentă afișată în partea de jos.

În momentul validării unui card folosind cititorul destinat intrării, sistemul va produce deschiderea releului pe un timp limitat și va oferi atât feedback vizual cât și auditiv prin intermediul ecranului LCD și al buzerului.

Odată cu validarea unui card folosind cititorul respectiv ieșirii, angajatul poate să aleagă utilizând tastatura numerică daca acesta dorește să părăsească temporar încăperea respectivă, începând astfel o pauză, sau să încheie programul de muncă. 

În ambele cazuri, sistemul va produce deschiderea releului și va oferi feedback atât auditiv cât și sonor.

Toate datele de intrare, ieșire sau pauză sunt introduse în baza de date indicând numele și datele aferente angajatului precum și ora împreună cu statusul acțiunii.

Pentru accesarea unui meniu destinat administratorului, este necesară introducerea unui cod de acces special folosind tastatura numerica.

Prin acest meniu administratorul dispune de funcționalități precum vizualizarea sau ștergerea unui utilizator, înrolarea unui utilizator nou utilizând o tastatură USB sau exportul datelor prin conectarea unei memorii USB la placă.

## Script-ul Python destinat interfatarii modulelor conectate

Pentru realizarea acestui script am utilizat diverse librării Python care mi-au permis să mă concentrez mai mult pe logica aplicației. Aceste librării Python reprezintă cod prescris conceput pentru modulele care au fost conectate la placă.

Pentru instalarea pachetelor Python a fost folosit sistemul de gestionare a pachetelor Python, iar acestea au fost instalate la nivel global în sistemul de operare.

Librăriile utilizate in cadrul programului Python sunt:

•	***time***: Librăria standard Python care este necesară pentru a utiliza întârzieri în program.

•	***RPi-LGPIO***: Deoarece Raspberry Pi 5 are un nou cip utilizat pentru controlul pinilor GPIO, biblioteca RPi-GPIO nu mai este compatibilă cu acesta fiind necesară renunțarea la librăriile proiectate pentru modelele de Raspberry mai vechi. 
Pentru a rezolva această problemă, am folosit librăria RPi-LGPIO care reprezintă un strat de compatibilitate pentru librăria RPi-GPIO și ne ajută sa controlăm pinii de uz general de pe placa într-un mod similar cum se puteau controla înainte de lansarea cipului nou. 
Pentru a instala RPi-LGPIO este necesară dezinstalarea librăriei vechi.

•	***mfrc522***: Este o librărie destinată citirii și scrierii etichetelor de proximitate prin intermediul cititoarelor RC522. 

•	***mysql.connector***: Reprezintă o librărie pentru conectarea și interacționarea cu baza de date locală MySQL din programul Python. 

•	***RPLCD.i2c***: Reprezintă librăria utilizată pentru interfața cu ecranele LCD utilizând protocolul I2C.
 
•	***datetime***: Reprezintă librăria standard Python pentru obținerea și formatarea datei și a orei curente.

•	***spidev***: Utilizata pentru interfața cu dispozitivele SPI.

•	***os***: Reprezintă librăria standard Python pentru interacțiunea cu sistemul de fișiere și sistemul de operare. 
Aceasta este utilizata pentru manipularea fișierelor individuale. 

•	***shutil***: Reprezintă o librărie standard Python destinată operațiunilor de gestionare a fișierelor la un nivel mai înalt precum operațiunile de copiere sau mutare a fișierelor sau directoarelor multiple.

## Implementarea modulelor RC522

O componenta esențială a acestui proiect o reprezintă cititoarele de proximitate RC522. 
Prin intermediul acestor cititoare se pot înrola utilizatori noi sau se poate permite accesul în incinta restricționata.

Cele două cititoare deservesc roluri diferite, primul fiind destinat intrării în spațiul controlat iar al doilea fiind destinat ieșirii. 
Pe baza codului unic de identificare al etichetelor de proximitate se poate permite accesul în încăpere doar dacă codul furnizat corespunde unui utilizator prezent în baza de date. 

Pentru conectarea celor două module am utilizat protocolul de comunicare SPI datorită vitezei sporite pe distanțe scurte. 

Pentru conectarea a două module RC522 utilizând aceeași interfață SPI am procedat prin conectarea în paralel a pinilor **SDA, SCK, MOSI și MISO** ai modulului către pinii interfeței SPI corespunzători plăcii iar pinii **RST** al celor două module se vor conecta la pini de intrare/ieșire diferiți.
Astfel, selecția modulului se va face prin setarea nivelului logic HIGH a pinului RST corespunzător cititorului cu care dorim să interacționăm.

Pentru utilizarea protocolului SPI pe placă, este necesară activarea acestuia în nucleul hardware folosind instrumentul de configurare a plăcii

## Implementarea bazei de date

Pentru a gestiona baza de date din acest proiect a fost utilizat sistemul de gestionare a bazelor de date relaționale MariaDB datorită avantajelor privind eficiența și simplitatea în utilizare și configurare.
Instalarea și configurarea MariaDB pe placa Raspbery este un proces relativ simplu care se poate realiza prin următoarele etape:

```bash
sudo apt update
sudo apt install mariadb-server
sudo mysql_secure_installation
```
Ulterior, putem verifica starea serverului folosind comanda:
```bash
sudo systemctl status mariadb.service
```
Acum putem să încărcăm instrumentul de linie de comandă MySQL rulând următoarea comandă: 
```bash
sudo mysql -u root -p
```
Deoarece MariaDB folosește extensia UNIX_SOCKET ca metodă de autentificare în mod implicit, trebuie să ne conectăm utilizând utilizatorul root cu prefixul sudo.
După rularea acestei comenzi, va fi solicitată parola setată în timpul inițializării scriptului de securitate. Odată ce autentificarea s-a realizat cu succes, se poate crea o bază de date:
```bash
CREATE DATABASE test;
```
Datorită faptului că utilizatorul root MariaDB este setat să se autentifice folosind extensia UNIX_SOCKET, procesul de acordare a drepturilor administrative unui program extern este relativ complicată. Această problemă poate fi remediată prin crearea unui cont administrativ separat pentru accesul bazat pe parolă folosind următoarea comandă SQL:
```bash
CREATE USER `nume_utilizator`@’localhost’ IDENTIFIED BY `parola_utilizator`;
```
Acest cont va fi folosit în programele externe, spre exemplu scriptul Python, pentru a citi datele din baza de date. După crearea contului, folosim următoarea comanda SQL pentru a acorda drepturi administrative acestuia:
```bash
GRANT ALL PRIVILEGES ON test.* TO `nume_utilizator`@`localhost`;
```
Această comanda acordă utilizatorului nume_utilizator toate privilegiile asupra bazei de date numită în cazul acesta „test”. 
Pentru a crea tabele în baza de date este necesară folosirea comenzii „use” pentru a interacționa direct cu aceasta.

Am considerat două tabele necesare pentru această bază de date: tabela destinată utilizatorilor, pentru stocarea datelor precum numele sau codul de identificare al etichetei de proximitate și tabela destinată evenimentelor precum cel de intrare sau de ieșire, corelate unui utilizator.

Pentru tabela utilizatorilor se vor stoca următoarele tipuri de date pentru fiecare utilizator care este adăugat:
 
•	**ID**: Reprezintă numărul de identificare a utilizatorului respectiv. Este un tip de date de tip întreg, incrementat automat, care poate lua doar valori pozitive și nu poate fi nul. 

•	**rfid_uid**: Reprezintă numărul de identificare al etichetei de proximitate. Această coloană va conține un tip de date destinat șirurilor de caractere cu lungime variabilă (VARCHAR) și nu poate fi nulă.

•	**nume**: Reprezintă numele utilizatorului înrolat. Conține tipul de date VARCHAR și nu poate fi nulă.

•	**created**: Reprezintă data înrolării utilizatorului la baza de date. 
Această coloană conține tipul de date „TIMESTAMP” care stochează valori temporale precum data și ora iar valoarea implicita este data și ora curentă la momentul înregistrării.

•	**departament**: Reprezintă departamentul utilizatorului. Este o coloană de tip șir de caractere variabil și poate fi nulă.

•	**timp_de_lucru**: Reprezintă norma de lucru a utilizatorului. Este o coloană cu tipul de date întreg si are valoarea prestabilită la 8.

•	**email**: Reprezintă adresa de mail a utilizatorului. Este o coloană cu tipul de date întreg, nulabilă.

•	Cheia Primara **„PRIMARY KEY(id)**: Aceasta constrângere definește coloana „id” ca fiind cheia primară a tabelei, fiind utilă pentru identificarea unică a fiecărei înregistrări în tabelă.

Pentru tabela destinată evenimentelor tipurile de date sunt:

•	**ID**: Reprezintă numărul de identificare al evenimentului respectiv. Asemănător cu coloana din cealaltă tabelă, este de tip întreg, incrementat automat care poate lua doar valori pozitive și are constrângerea de tip NOT_NULL.

•	**User_id**: Reprezintă numărul de identificare al utilizatorului căruia îi este asociat un eveniment.

•	**Stare**: Reprezintă acțiunea realizată de către utilizator precum intrare, ieșire sau pauză. Această coloană ia ca valoare un șir doar dintr-o listă predefinită folosind obiectul ENUM.  

•	**Data_ora**: Reprezintă coloana care stochează datele temporale ale evenimentului și are tipul de date TIMESTAMP.

•	**nume**: Reprezintă coloana cu numele utilizatorului. 

## Implementarea ecranului LCD2004
 
Ecranul este conectat la placă prin intermediul protocolului I2C. 
Prin lipirea adaptorului I2C la ecran, acesta poate fi conectat direct la pinii dedicați protocolului I2C de pe placă, micșorând astfel numărul de pini necesari pentru controlul ecranului.

Similar cu activarea protocolului SPI, I2C se poate activa utilizând instrumentul de configurare ```raspi-config ``` și repornirea plăcii după activarea acestuia.

## Meniul destinat administratorului

Meniul destinat administratorului permite vizualizarea datelor corespunzătoare utilizatorilor și facilitează operațiuni de baza în scopul gestionării sistemului oferind un mod de operare „offline” în cazul în care este infezabilă dintr-un oarecare motiv conectarea la rețea a sistemului și accesarea aplicației web. 

Se asigură astfel înregistrarea și monitorizarea neîntreruptă a prezenței într-o varietate de scenarii de utilizare.
Caracteristicile și funcționalitățile principale ale acestui meniu sunt:

•	***Modalitate de autentificare bazată pe cod pin***: 

Codul pin asigură autentificarea sigură a utilizatorilor autorizați. 
Pentru tastarea codului pin se utilizează tastatura matriciala, oferind feedback vizual în cazul codurilor incorecte iar în cazul în care autentificarea s-a realizat cu succes utilizatorul este întâmpinat de meniul principal. 

•	***Posibilitatea înrolării unui utilizator nou***: 

Metoda de înrolare facilitează adăugarea utilizatorilor noi în sistem prin apropierea etichetei de proximitate de cititorul corespunzător ieșirii. În momentul în care eticheta este recunoscută de către cititor, ecranul LCD va indica faptul că este necesară introducerea unui nume pentru a finaliza înrolarea. Pentru introducerea numelui este necesară conectarea unei tastaturi USB la oricare dintre porturile USB ale plăcii.

•	***Exportul raportului de prezență pe ziua curenta***: 

Această metodă permite exportul datelor de prezență pe ziua curentă folosind o memorie USB conectată la oricare slot USB al plăcii. Placa va detecta automat introducerea unei memorii USB iar odată cu rularea funcționalității se va genera raportul de prezență în formatul CSV(Comma Separated Values), un format ușor de citit și de procesat de către alte programe. 

•	***Posibilitatea de vizualizare și de ștergere a unui utilizator***: 

Metoda destinată vizualizării utilizatorilor va afișa pe ecranul LCD lista utilizatorilor înrolați la baza de date alături de numărul de identificare al acestora. Este utilizat un mecanism de derulare pentru a parcurge liste potențial lungi de utilizatori. 

Folosind metoda de ștergere, se poate introduce numărul de identificare al unui utilizator pentru a fi șters din baza de date. Odată cu tastarea numărului respectiv folosind tastatura matriciala, este afișat un mesaj de confirmare care ne indică numele utilizatorului care va fi șters, așteptând confirmarea administratorului.

## Implementarea Aplicatiei Web

Obiectivul aplicației web este de a oferi administratorului o modalitate eficientă de a accesa date suplimentare despre prezența utilizatorilor precum timpul lucrat pe un interval selectabil, prezențele acumulate lunar sau jurnalul activităților, oferind în același timp funcționalități care permit gestionarea angajaților sau exportul datelor relevante.

Aplicația a fost realizată utilizând framework-ul Laravel datorită avantajelor privind interacționarea facilă cu bazele de date și a modelului arhitectural MVC care organizează eficient părțile componente ale aplicației.

Laravel Breeze este un pachet oficial al framework-ului Laravel, conceput pentru a oferi un punct de plecare în construirea unei aplicații Laravel cu o implementare simplă și minimalistă a autentificării. 
Acest pachet include toate funcționalitățile necesare pentru autentificarea utilizatorilor, precum și o metodă de înregistrare sau resetare a parolei fără a fi necesară o implementare de la zero, oferind o fundație solidă în construirea aplicației propuse.
  
Fiind necesară integrarea codului PHP în paginile HTML ale aplicației, am utilizat șabloanele Blade care mi-au permis o modalitate simplă și eficientă de a integra codul PHP pentru a realiza anumite condiții sau operațiuni în scopul afișării datelor.

Pentru stilizarea interfeței am folosit framework-ul CSS Tailwind, oferind clase predefinite de stil care eficientizează stilizarea tabelelor sau a altor elemente vizuale de pe paginile aplicației.

O alta componentă importantă a aplicației o reprezintă limbajul JavaScript alături de librăria Jquery pentru facilitarea cererilor AJAX, o tehnică utilizată pe toate paginile aplicației pentru actualizarea dinamică a conținutului tabelelor.


*Aplicația web este compusă din următoarele pagini*:

---

•	***Pagina de start***: Reprezintă pagina vizualizata inițial la accesarea URL-ului aplicației. Aceasta conține un logo reprezentativ alături de indicații privind autentificarea sau înregistrarea pentru accesarea aplicației.

•	***Pagina de autentificare/înregistrare***: Pagina de autentificare reprezintă modalitatea prin care un utilizator se poate autentifica la platformă pentru a vizualiza datele prezenței. 

•	***Pagina panoului de bord***: Pagina panoului de bord are rolul de a oferi o vedere generală a informațiilor relevante administratorului sistemului. 

•	***Pagina de gestionare a utilizatorilor***: Reprezintă pagina prin care se pot vizualiza datele utilizatorilor înrolați la sistem și se pot efectua modificări asupra informațiilor acestora.

•	***Pagina jurnalului de activități***: Pagina jurnalului este reprezentată de un tabel unde vor fi afișate toate activitățile de intrare sau ieșire.

•	***Pagina destinata prezențelor într-o anume zi selectabilă***

•	***Pagina destinata prezențelor într-o anume luna selectabilă***

•	***Pagina destinata afișării calcului orelor lucrate pe o anume zi selectabilă***

•	***Pagina destinata afișării calcului orelor lucrate pe o anume luna selectabilă***

---

**Există posibilitatea de a exporta datele de prezenta într-un fișier cu formatul Excel.**

Această funcționalitate are rolul de a transpune datele orelor lucrate de către fiecare utilizator selectând o dată de început și o dată de final. 

Fișierul Excel exportat va cuprinde câte o foaie de lucru pentru fiecare utilizator în parte, oferind astfel o organizare structurata și o gestionare eficientă a datelor fiecărui utilizator. 

Pentru deschiderea unui astfel de fișier poate fi utilizată orice aplicație software de calcul tabelar, precum Microsoft Excel sau Google Sheets.

## Configurarea aplicatiei web

Este necesară instalarea PHP în sistem alături de câteva extensii ale acestuia pentru a asigura dependențele necesare framework-ului Laravel. 
Versiunea de PHP utilizata in acest proiect este 8.2:

```bash
sudo apt install php8.2-fpm php8.2-mbstring php8.2-mysql php8.2-curl php8.2-gd php8.2-zip php8.2-xml -y
```
Pentru a gestiona pachetele și dependențele proiectului se utilizeaza instrumentul Composer:

```bash
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename =composer
```
Folosind Composer, se poate creea un proiect Laravel instalând global programul de instalare Laravel.

Structura unui proiect Laravel conține diverse componente și fișiere cu roluri specifice:

•	**Directorul app/Http/Controllers**: Aici vor fi stocate controlerele care gestionează cererile HTTP și returnează răspunsuri. 
În cazul pachetului Breeze, vor fi adăugate controlere care gestionează autentificarea și înregistrarea utilizatorilor.

•	**Directorul app/Models**: Aici vor fi stocate modelele Eloquent care reprezintă tabelele din baza de date.
 
•	**Directorul resources/views**: Acest director va conține toate „vizualizările” Blade, utilizate pentru a genera conținut HTML. 


•	**Directorul routes/web.php**: Aici vor fi stocate rutele aplicației. Laravel Breeze va adăuga inițial rute pentru autentificare sau înregistrare.

•	**Directorul database/migrations**: Acest director conține fișiere de migrare pentru baza de date. Laravel Breeze adaugă migrații pentru tabelele utilizatorilor.

•	**Fișierul .env**: Acesta este prezent în directorul rădăcina al proiectului și are rolul de configurare a variabilelor de mediu, precum setările pentru conexiunea la baza de date sau alte variabile specifice.

•	**Fișierul composer.json**: Reprezintă fișierul de configurare pentru Instrumentul Composer iar acesta include lista de dependențe ale proiectului precum și scripturi specifice.

•	**Fișierul package.json**: Fișierul de configurare pentru managerul de pachete JavaScript npm. Aici va fi inclusă lista de dependențe pentru partea de front-end.

Pentru a configura baza de date creată să funcționeze în cadrul aplicației Laravel, este necesară editarea fișierului .env din directorul rădăcina folosind orice editor de text disponibil. 
În acest fișier vor fi prezente setările conexiunii cu baza de date precum numele bazei de date sau numele utilizatorului alături de parola acestuia. 

Pentru a rula aplicația web locala putem introduce comanda:

```bash
php artisan serve
```

## Configurarea Serverului NGINX

Înainte de a putea găzdui o aplicație Laravel prin intermediul serverului NGINX, trebuie realizată o serie de modificări atât în fișierul de configurare al serverului cât și în fișierul de configurare a mediului aplicației Laravel.

Pentru început, trebuie modificat fișierul .env al aplicației pentru a configura URL-ul sau opțiunile de depanare.

Următorul pas este mutarea fișierului proiectului în locația obișnuită pentru aplicațiile web care rulează pe NGINX. Pentru acest lucru, este necesară mutarea proiectului utilizând drepturi administrative:
```bash
sudo mv ~/Proiect /var/www/Aplicatie
```
Pentru a configura NGINX să ruleze aplicația Laravel, trebuie creat un nou fișier de configurare în directorul “sites-available” utilizând un editor de text cu drepturi elevate:
```bash
sudo nano /etc/nginx/sites-available/aplicatie
```
Fișierul de configurare trebuie să cuprindă diverse setări recomandate pentru aplicațiile Laravel, spre exemplu blocul de configurare pentru PHP in cadrul NGINX:

```bash
location ~ \.php${
 fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
 fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME $realpath-root$fastcgi_script_name;
 include fastcgi-params;
}
```
Acest bloc de configurare se va aplica tuturor fișierelor PHP și specifică direcționarea cererilor PHP către socket-ul Unix utilizat de PHP-FPM (8.2 în acest caz) precum și alte setări implicite pentru FastCGI, un protocol binar pentru interfațarea serverelor web cu aplicațiile web.

După editarea fișierului de configurare, acesta trebuie salvat și activat prin crearea unei legături simbolice către aplicație în „sites-enabled”:
```bash
sudo ln -s /etc/nginx/sites-availabe/aplicatie /etc/nginx/sites-enabled/ 
```
Pentru a confirma faptul că acest fișier nu conține erori de sintaxă putem rula:
```bash
sudo nginx -t
```
În final, aplicăm modificările prin reîncărcarea serviciului Nginx:
```bash
sudo systemctl reload nginx
```



## Sistemul in varianta finala

### Sistemul fizic

[![IMG20240525144647.jpg](https://i.postimg.cc/Y0wXhV4R/IMG20240525144647.jpg)](https://postimg.cc/34njqtF0)

[![IMG20240525144807.jpg](https://i.postimg.cc/gjqgRD8W/IMG20240525144807.jpg)](https://postimg.cc/2LydNn0c)



### Aplicatia web



Pagina de start:
[![20240525-15h04m26s-grim.png](https://i.postimg.cc/C1pCN8FL/20240525-15h04m26s-grim.png)](https://postimg.cc/wtFsx7yS)


Pagina de login:
[![20240525-15h04m33s-grim.png](https://i.postimg.cc/VvYqycrx/20240525-15h04m33s-grim.png)](https://postimg.cc/N9z9TSxD)


Pagina de inregistrare:
[![20240525-15h04m38s-grim.png](https://i.postimg.cc/G2PFSp2P/20240525-15h04m38s-grim.png)](https://postimg.cc/9RMwDCyz)


Pagina panoului de bord:
[![20240525-15h05m05s-grim.png](https://i.postimg.cc/MHdyqr1v/20240525-15h05m05s-grim.png)](https://postimg.cc/FkkfxpW4)


Gestionarea utilizatorilor:
[![20240525-15h05m10s-grim.png](https://i.postimg.cc/c1m7R6P0/20240525-15h05m10s-grim.png)](https://postimg.cc/PNC8hftR)


Jurnalul activitatilor:
[![20240525-15h05m18s-grim.png](https://i.postimg.cc/Kv8PJxyL/20240525-15h05m18s-grim.png)](https://postimg.cc/4njH3CyN)


Prezente zilnice/lunare:
[![20240525-15h05m43s-grim.png](https://i.postimg.cc/YC41BR0C/20240525-15h05m43s-grim.png)](https://postimg.cc/tsj1hhFK)


Raport timp de prezenta zilnic:
[![20240525-15h06m04s-grim.png](https://i.postimg.cc/DZhssKgS/20240525-15h06m04s-grim.png)](https://postimg.cc/SXZJF3H4)


Raport timp de prezenta lunar:
[![20240525-15h06m16s-grim.png](https://i.postimg.cc/R0KKN0cr/20240525-15h06m16s-grim.png)](https://postimg.cc/nsVCWpRT)


Profil administrator:
[![20240525-15h06m32s-grim.png](https://i.postimg.cc/gJKhHBSN/20240525-15h06m32s-grim.png)](https://postimg.cc/gXnrY4PZ)




