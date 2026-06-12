# 🩸 Blood Donation Management System

A web-based Blood Donation Management System developed using PHP and MySQL. The platform connects blood donors, recipients, hospitals, and blood bank staff through a centralized system for managing blood donations, blood requests, inventory, and emergency blood requirements.

---

## 📖 Overview

The Blood Donation Management System is designed to streamline blood donation and blood bank operations. Donors can register and track donations, recipients can request blood units, hospitals can manage blood inventory, and administrators can monitor the entire system through a dedicated dashboard.

---

## ✨ Features

### 🩸 Donor Features

- Donor Registration
- Secure Login & Logout
- Donor Dashboard
- Profile Management
- Donation History Tracking
- Emergency Blood Match Notifications
- View Donation Records

### 🏥 Recipient Features

- Recipient Registration
- Blood Request Submission
- Request Status Tracking
- Profile Management
- Dashboard Access

### 🏨 Hospital Features

- Hospital Dashboard
- Blood Inventory Management
- Confirm Blood Donations
- Manage Blood Requests
- Donation Records Monitoring
- Blood Issue Tracking
- Expiring Blood Unit Alerts
- Staff Management

### 👨‍⚕️ Staff Features

- Staff Dashboard
- Donation Processing
- Blood Unit Issuance
- Leave Management
- Activity Monitoring

### 🔐 Admin Features

- Admin Dashboard
- Manage Donors
- Manage Recipients
- Manage Hospitals
- Manage Staff
- Global Blood Inventory Monitoring
- View All Blood Requests
- View All Donations
- Reports & Analytics

---

## 🩸 Supported Blood Groups

- A+
- A-
- B+
- B-
- AB+
- AB-
- O+
- O-

---

## 🛠️ Technologies Used

| Technology | Purpose |
|------------|----------|
| HTML5 | Frontend Structure |
| CSS3 | Styling |
| PHP | Backend Development |
| MySQL | Database Management |
| JavaScript | Client-Side Functionality |
| Apache | Web Server (XAMPP/WAMP) |

---

## 📂 Project Structure

```text
blood-donation-system/
│
├── admin/
│   ├── dashboard.php
│   ├── manage_donors.php
│   ├── manage_recipients.php
│   ├── manage_hospitals.php
│   ├── manage_staff.php
│   ├── all_requests.php
│   ├── all_donations.php
│   ├── global_inventory.php
│   └── reports.php
│
├── donor/
│   ├── dashboard.php
│   ├── profile.php
│   ├── my_donations.php
│   └── emergency_matches.php
│
├── recipient/
│   ├── dashboard.php
│   ├── profile.php
│   ├── new_request.php
│   └── my_requests.php
│
├── hospital/
│   ├── dashboard.php
│   ├── inventory.php
│   ├── blood_issues.php
│   ├── donation_records.php
│   ├── pending_requests.php
│   ├── confirm_donations.php
│   ├── expiring_units.php
│   ├── leave_appointments.php
│   └── staff.php
│
├── staff/
│   ├── dashboard.php
│   ├── process_donation.php
│   ├── issue_units.php
│   └── my_leaves.php
│
├── config/
│   ├── database.php
│   └── constants.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── sidebar_admin.php
│   ├── sidebar_donor.php
│   ├── sidebar_recipient.php
│   ├── sidebar_hospital.php
│   └── sidebar_staff.php
│
├── assets/
│   └── css/
│       └── style.css
│
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── login_action.php
├── logout.php
├── migrate.php
│
└── blood_donation_system.sql
```

---

## ⚙️ Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/blood-donation-management-system.git
```

### Step 2: Move Project Folder

For XAMPP:

```text
C:\xampp\htdocs\
```

For WAMP:

```text
C:\wamp64\www\
```

### Step 3: Create Database

```sql
CREATE DATABASE blood_donation_system;
```

### Step 4: Import Database

Import:

```text
blood_donation_system.sql
```

### Step 5: Configure Database Connection

Open:

```php
config/database.php
```

Update database credentials if necessary:

```php
<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "blood_donation_system";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### Step 6: Run Application

Open browser:

```text
http://localhost/blood-donation-system/
```

---

## 🔑 System Modules

### Donor Management

- Donor Registration
- Donation Tracking
- Donation History
- Emergency Match Alerts

### Blood Request Management

- Blood Requests
- Request Approval
- Request Monitoring
- Blood Allocation

### Inventory Management

- Blood Stock Tracking
- Blood Unit Availability
- Expiry Monitoring
- Global Inventory Dashboard

### Hospital Management

- Hospital Registration
- Staff Management
- Donation Confirmation
- Blood Issue Records

### Administration

- User Management
- Hospital Management
- Inventory Oversight
- Reports & Analytics

---

## 💾 Database

Database Name:

```text
blood_donation_system
```

Possible Main Tables:

```sql
donors
recipients
hospitals
staff
blood_inventory
blood_requests
donations
blood_issues
appointments
admins
```

---

## 🚀 Future Enhancements

- Blood Donation Appointment Booking
- SMS Notifications
- Email Alerts
- Blood Camp Management
- QR Code Donor Identification
- Mobile Application
- Real-Time Emergency Alerts
- Advanced Analytics Dashboard
- Online Blood Availability Search

---

## 🎓 Academic Purpose

This project was developed for educational purposes and demonstrates:

- PHP Web Development
- MySQL Database Integration
- Authentication & Authorization
- Role-Based Access Control
- Inventory Management Systems
- Healthcare Information Systems
- CRUD Operations
- Multi-User Web Applications

---

## 👨‍💻 Developer

### Aswin Sreenivas

Diploma in Computer Engineering

#### Connect

GitHub:
https://github.com/aswin-sreenivas
---

## 📜 License

This project is intended for educational and learning purposes.

---

⭐ If you found this project useful, consider giving it a star on GitHub.
