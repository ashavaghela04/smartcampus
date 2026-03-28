<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=200&section=header&text=Smart%20Campus&fontSize=60&fontColor=ffffff&fontAlignY=38&desc=College%20Campus%20Management%20System&descSize=20&descAlignY=60&animation=fadeIn" width="100%"/>

<br/>

<img src="https://img.shields.io/badge/Status-Active-27AE60?style=for-the-badge&logo=checkmarx&logoColor=white"/>
&nbsp;
<img src="https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
&nbsp;
<img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
&nbsp;
<img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge"/>

<br/><br/>

<img src="https://img.shields.io/badge/Roles-Admin%20%7C%20Faculty%20%7C%20Student-534AB7?style=flat-square"/>
&nbsp;
<img src="https://img.shields.io/badge/Pages-95%2B%20PHP%20Files-FF2D20?style=flat-square"/>
&nbsp;
<img src="https://img.shields.io/badge/Email-PHPMailer-EA4335?style=flat-square"/>
&nbsp;
<img src="https://img.shields.io/badge/UI-Bootstrap%205-7952B3?style=flat-square"/>

</div>

---

## 📌 Overview

**Smart Campus** is a full-featured, role-based college campus management system built with PHP and MySQL. It streamlines the day-to-day operations of an educational institution — from student registration and attendance tracking to result management and leave approvals — all through a clean, responsive web interface.

> 💡 Built from scratch as a real-world project during BCA 6th Semester, this system demonstrates hands-on expertise in backend development, database design, and multi-role authentication.

---

## ✨ Key Features

<table>
<tr>
<td width="50%" valign="top">

### 🔐 Authentication System
- Secure role-based login (Admin / Faculty / Student)
- Session-based access control on every page
- Registration with email verification via PHPMailer
- Real-time duplicate check on registration (AJAX)

### 👨‍💼 Admin Panel
- Live dashboard with student, faculty & announcement stats
- Approve / reject student & faculty registrations
- Manage student and faculty profiles (edit, delete)
- Department-wise attendance analytics
- Manage and publish exam results
- Leave approval workflow

</td>
<td width="50%" valign="top">

### 👩‍🏫 Faculty Panel
- Personal dashboard with class schedule
- Mark and manage student attendance
- Upload & manage study materials
- Publish exam results per class
- Post and manage announcements
- Apply for leave

### 🎓 Student Panel
- Personal dashboard with attendance summary
- View month-wise attendance calendar
- Download study materials
- Check exam results
- Apply for leave & track status
- View timetable & notice board

</td>
</tr>
</table>

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white) |
| **Backend** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) `PDO` · `Singleton DB Pattern` · `Session Auth` · `PHPMailer` |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) `Relational Design` · `PDO Prepared Statements` · `Joins` |
| **Tools** | ![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white) ![VS Code](https://img.shields.io/badge/VS%20Code-007ACC?style=flat-square&logo=visualstudiocode&logoColor=white) ![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=flat-square&logo=xampp&logoColor=white) |
| **Architecture** | `MVC-inspired` · `Role-based Access Control` · `Singleton Pattern` · `Modular PHP includes` |

---

## 📁 Project Structure

```
smartcampus/
│
├── 📄 index.php                     # Landing page
├── 📄 home.php / about.php          # Public pages
├── 📄 login1.php                    # Unified login
├── 📄 courses.php / event.php       # Course & event listings
│
├── 📂 db/
│   └── db.php                       # Singleton PDO database class
│
├── 📂 forms/
│   ├── student-register.php         # Student registration
│   ├── faculty-register.php         # Faculty registration
│   └── check-duplicate.php          # AJAX duplicate checker
│
├── 📂 assets/
│   ├── auth.php                     # Auth helper
│   ├── function.php                 # Shared utility functions
│   ├── config/
│   │   └── mail-config.php          # PHPMailer config (⚠️ not in repo)
│   └── css / img /                  # Stylesheets & images
│
├── 📂 dashboard/
│   ├── 📂 admin/                    # Admin-only pages
│   │   ├── admin_dashboard.php      # Live stats & charts
│   │   ├── admin_students_manage    # Student CRUD
│   │   ├── admin_faculty_manage     # Faculty CRUD
│   │   ├── admin_results_manage     # Results management
│   │   ├── leave_approval.php       # Leave request approvals
│   │   └── admin_attendance_view    # Dept-wise attendance view
│   │
│   ├── 📂 faculty/                  # Faculty-only pages
│   │   ├── faculty_dashboard.php
│   │   ├── faculty_attendance.php   # Mark attendance
│   │   ├── faculty_results.php      # Upload results
│   │   ├── faculty_materials_*.php  # Study material upload/manage
│   │   └── faculty_announcements    # Post announcements
│   │
│   ├── 📂 students/                 # Student-only pages
│   │   ├── dashboard.php            # Attendance calendar & summary
│   │   ├── result.php               # View exam results
│   │   ├── study_materials.php      # Download materials
│   │   ├── apply_leave.php          # Leave application
│   │   └── timetable.php            # View timetable
│   │
│   ├── 📂 timetables/               # Timetable CRUD
│   ├── 📂 announcements/            # Announcements module
│   ├── 📂 attedance/                # Attendance logic & functions
│   └── 📂 includes/                 # Shared layouts (header, sidebar, footer)
│
└── 📂 phpmailer/                    # PHPMailer library
```

---

## ⚙️ Installation & Setup

Follow these steps to run the project locally using XAMPP.

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with PHP 8.0+, Apache & MySQL
- [Git](https://git-scm.com/)
- A modern web browser

---

### Step 1 — Clone the repository

```bash
git clone https://github.com/ashavaghela04/smartcampus.git
```

Move the cloned folder into your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\smartcampus
```

---

### Step 2 — Import the database

1. Start **XAMPP** → Start **Apache** and **MySQL**
2. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Create a new database named **`smartcampus_1`**
4. Click **Import** → select `smartcampus.sql` from the project root
5. Click **Go**

---

### Step 3 — Configure the database connection

Open `db/db.php` and update your credentials:

```php
$host     = "localhost";
$dbname   = "smartcampus_1";
$username = "root";
$password = "";        // Your MySQL password (empty by default in XAMPP)
```

---

### Step 4 — Configure email (optional)

Open `assets/config/mail-config.php` and add your SMTP details for email features:

```php
$mail->Host       = 'smtp.gmail.com';
$mail->Username   = 'your_email@gmail.com';
$mail->Password   = 'your_app_password';   // Use a Gmail App Password
```

> 💡 Generate a Gmail App Password at **Google Account → Security → App Passwords**

---

### Step 5 — Run the project

Open your browser and visit:

```
http://localhost/smartcampus
```

---

## 👤 Default Login Credentials

| Role | How to access |
|------|--------------|
| **Admin** | Seeded directly in the `admins` table — check DB after import |
| **Student** | Register at `/forms/student-register.php` → admin approves |
| **Faculty** | Register at `/forms/faculty-register.php` → admin approves |

---

## 🔒 Security Highlights

- ✅ All queries use **PDO Prepared Statements** — SQL Injection proof
- ✅ Role-based **session validation** on every protected route
- ✅ Passwords stored with **secure hashing**
- ✅ `mail-config.php` excluded from repository — never expose credentials
- ✅ AJAX duplicate check prevents duplicate registrations

---

## 🚀 Future Improvements

- [ ] REST API for mobile app integration
- [ ] Real-time notifications (WebSockets)
- [ ] PDF export for results & attendance reports
- [ ] Student fee management module
- [ ] Dark mode for the dashboard

---

## 👩‍💻 Developer

<table>
<tr>
<td align="center">
<b>Asha Vaghela</b><br/>
<i>BCA Graduate · Full Stack Developer (PHP & Laravel)</i><br/><br/>
<a href="https://www.linkedin.com/in/asha-vaghela-93a2b6333/">
  <img src="https://img.shields.io/badge/LinkedIn-Connect-0077B5?style=flat-square&logo=linkedin&logoColor=white"/>
</a>
&nbsp;
<a href="mailto:asha.work43@gmail.com">
  <img src="https://img.shields.io/badge/Gmail-Email-D14836?style=flat-square&logo=gmail&logoColor=white"/>
</a>
&nbsp;
<a href="https://github.com/ashavaghela04">
  <img src="https://img.shields.io/badge/GitHub-Profile-181717?style=flat-square&logo=github&logoColor=white"/>
</a>
</td>
</tr>
</table>

---

## 📄 License

This project is licensed under the **MIT License** — feel free to use it for learning or as a base for your own project.

---

<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&customColorList=6,11,20&height=100&section=footer&animation=fadeIn" width="100%"/>

⭐ **Found this project helpful? Give it a star!**

*Built with passion by [Asha Vaghela](https://github.com/ashavaghela04)*

</div>
