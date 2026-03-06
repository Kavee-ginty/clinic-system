# Clinic System Installation Guide

This system is designed for high-performance use on a local WiFi network (LAN) using a lightweight XAMPP server stack.

## Installation Steps (Doctor's PC / Server)

1. **Install XAMPP & Configure MySQL Port**
   - Download and install XAMPP for Windows/Mac/Linux.
   - Change MySQL port to **3307**:
     1. Open XAMPP Control Panel > Config (MySQL) > `my.ini`.
     2. Find all instances of `port=3306` and change them to `port=3307`. Save and close.
   - Start **Apache** and **MySQL** from the XAMPP Control Panel.

2. **Fix phpMyAdmin Configuration**
   - Go to `C:\xampp\phpMyAdmin` and open `config.inc.php`.
   - Find the line: `$cfg['Servers'][$i]['host'] = '127.0.0.1';` (or `localhost`).
   - Add this line right below it: `$cfg['Servers'][$i]['port'] = '3307';`
   - Restart Apache and MySQL in XAMPP.

3. **Setup Database**
   - Open your browser and go to `http://localhost/phpmyadmin`
   - Create a new database named `clinic_system`.
   - Select the `clinic_system` database, go to the **Import** tab, and upload the `database.sql` file provided in this folder.
   - Click "Go" to create the tables.

4. **Deploy the Project**
   - Copy the entire `clinic-system` folder into your XAMPP `htdocs` directory.
   - Default Windows path: `C:\xampp\htdocs\clinic-system`

5. **Verify Database Configuration**
   - Open `config/db.php` in a text editor to ensure the credentials match your XAMPP setup:
     - Host: `127.0.0.1`
     - Port: `3307`
     - Username: `root`
     - Password: `''` (Blank by default in XAMPP)

## Running the System Local & Over LAN

**On the Doctor's PC (Server):**
- You can access the system at: `http://localhost/clinic-system`

**On the Receptionist's PC (Over WiFi/LAN):**
1. On the Doctor's PC (Server), go to `http://localhost/clinic-system`
2. Click the **"Link a Receptionist PC"** button on the home screen.
3. The system will automatically generate a Permanent Computer Name Link (e.g., `http://DOCTORS-PC/clinic-system`).
4. On the Receptionist's PC, type this exact link into the browser and bookmark it. This link permanently handles router reboots.

## Architecture Highlights
- **Performance:** 1-Second AJAX Polling on `<100ms` LAN connection enables real-time queue synchronization.
- **Security:** PDO Prepared Statements used on all APIs preventing SQL Injections.
- **Frontend Stack:** TailwindCSS on HTML Structure with highly optimized Vanilla JavaScript APIs endpoints.

## Directory Structure Overview
- `/api` - Independent PHP endpoints outputting JSON
- `/admin` - Database overview & historical tools
- `/doctor` - Consultation, queue management & history
- `/receptionist` - Patient registration & queue dispatch
- `/config` - Database connections

*System tailored for local environments.*
