-- Make sure backup folder is already exists in Server Cdrive and the pvk, cer and .bak is already in inside

1) Restore CERT and PVK
USE master;
GO

CREATE CERTIFICATE TDECert
FROM FILE = 'C:\Backup\TDECert.cer'
WITH PRIVATE KEY (
    FILE = 'C:\Backup\TDECert.pvk',
    DECRYPTION BY PASSWORD = 'Pa$$w0rd'
);
GO

2) Check the cert already imported or not
SELECT * FROM sys.certificates;

3) Restore DB
RESTORE DATABASE food_ordering
FROM DISK = 'C:\backup\food_ordering.bak'
WITH 
MOVE 'food_ordering' TO 'C:\backup\food_ordering.mdf',
MOVE 'food_ordering_log' TO 'C:\backup\food_ordering_log.ldf',
REPLACE;
GO

4) Change your VM's server adapter to bridged adapter

5) Get Server IP Address

6) UPDATE the IP Address in db.php and dbadmin.php 

7) Ensure ODBC driver is downloaded
https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server?view=sql-server-ver17

8) RUN Any Apache Server (Xampp/Laragon/WAMP)

9) Make sure the code folder is in correct path 
For xampp: C:\Xampp\htdocs

10) System Testing by Logging in Accessing database features

11) Website admin login credentials is:
username: admin
pw : admin123

**************************************************************************************************************************************************
The admin credentials are for testing purposes only and use a weak password for demonstration purposes;
**************************************************************************************************************************************************



