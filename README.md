# Student Management System (PHP + MySQL + Bootstrap 5)

A professional, modular **Student Management System** built with core PHP
(no framework) following an **MVC-inspired architecture**, MySQL (PDO,
prepared statements), Bootstrap 5, and vanilla JavaScript. Designed to run
on **XAMPP** out of the box.

---

## 1. Features

### Student
- Register / Login (own portal)
- Dashboard with attendance %, pending assignments, recent marks
- View & edit profile + upload profile photo
- View attendance history & summary
- View marks / performance with subject-wise averages
- View assignments, upload/submit work, see Approved/Rejected/Pending status
- Download notes uploaded by teachers
- View notifications (marks published, assignment reviewed, announcements)
- Change password

### Admin / Teacher
- Secure separate login portal (no self-registration — admins are seeded via SQL or created by another admin)
- Dashboard with key stats and quick actions
- Manage Students: Add / Edit / Delete, Search & Filter by name/roll/email/class/section
- Mark Attendance per class per date
- Add / Edit / Delete Marks (performance) per student
- Create Assignments (with optional attachment, due date, target class)
- View Submitted Assignments, Approve / Reject with remarks
- Upload / Delete Notes (study material) per subject/class
- Post / Delete Announcements (auto-notifies all students)
- Reports: attendance %, class-wise average performance, assignment completion rates
- Change password

### Security
- Passwords hashed with `password_hash()` (bcrypt) — never stored in plain text
- 100% parameterized queries via PDO prepared statements (no string-concatenated SQL) — SQL-injection safe
- Server-side input validation & `htmlspecialchars()` output escaping (XSS safe)
- CSRF token verification on every state-changing POST request
- Session hardening (`httponly` cookies, `session_regenerate_id()` on login, strict mode)
- Role-based access control (`Session::requireRole()`) guarding every controller/page
- File upload validation (extension whitelist + size limit) and `.htaccess` blocking script execution inside `assets/uploads/`

---

## 2. Folder Structure (MVC)

```
sms/
├── action.php                 # Front controller for POST/GET actions (dispatches to Controllers)
├── index.php                  # Front controller/router for all page views (?page=xxx)
├── .htaccess                  # Apache security hardening
│
├── config/
│   ├── config.php             # App constants: BASE_URL, upload paths, limits
│   └── database.php           # DB credentials
│
├── core/                      # Framework-ish helper classes
│   ├── bootstrap.php          # Autoloader + session start, included by index.php/action.php
│   ├── Database.php           # PDO singleton connection
│   ├── Session.php            # Auth/session/flash/CSRF helper
│   └── Validator.php          # Input validation & sanitisation helpers
│
├── models/                    # Data-access layer (1 class per table, PDO prepared statements)
│   ├── User.php
│   ├── Student.php
│   ├── Attendance.php
│   ├── Marks.php
│   ├── Assignment.php
│   ├── Notes.php
│   ├── Announcement.php
│   └── Notification.php
│
├── controllers/                # Business logic, handles POST/GET actions
│   ├── AuthController.php      # login/register/logout/change password
│   ├── StudentController.php   # student-only actions
│   └── AdminController.php     # admin/teacher-only actions
│
├── views/                      # Presentation layer (plain PHP + Bootstrap 5 HTML)
│   ├── auth/                   # login_student.php, login_admin.php, register.php
│   ├── student/                # dashboard, profile, attendance, marks, assignments, notes, notifications, change_password
│   ├── admin/                  # dashboard, manage_students, attendance, marks, assignments, view_submissions, notes, announcements, reports
│   └── layouts/                # header, footer, topbar, alerts, sidebar_student, sidebar_admin (shared partials)
│
├── assets/
│   ├── css/style.css           # Custom styles on top of Bootstrap 5
│   ├── js/script.js            # Sidebar toggle, validation, confirm-delete
│   └── uploads/
│       ├── assignments/        # Teacher-uploaded assignment attachments
│       ├── submissions/        # Student-uploaded assignment submissions
│       ├── notes/              # Teacher-uploaded study notes
│       ├── profile/            # Student profile photos
│       └── .htaccess           # Blocks script execution in uploads
│
└── database/
    └── student_management.sql  # Full schema + relationships + sample data
```

**Why this structure works as MVC in plain PHP:**
- **Model** = `models/*.php` — all SQL lives here, nowhere else.
- **View** = `views/*.php` — pure presentation, receives data from the page script and prints HTML.
- **Controller** = `controllers/*.php` — handles form submissions from `action.php`, talks to Models, redirects back to a View via `index.php`.
- `index.php` and `action.php` act as the **front controllers/router**, keeping a single, auditable entry point instead of dozens of directly-accessible scripts.

---

## 3. Database Design (ER Summary)

| Table | Purpose | Key Relationships |
|---|---|---|
| `users` | Auth for both roles (student/admin) | — |
| `students` | Student profile, 1:1 with `users` | `user_id` → `users.id` |
| `attendance` | One row per student per date | `student_id` → `students.id`, `marked_by` → `users.id` |
| `marks` | Exam/performance records | `student_id` → `students.id`, `added_by` → `users.id` |
| `assignments` | Tasks created by teachers | `uploaded_by` → `users.id` |
| `assignment_submissions` | Student uploads per assignment | `assignment_id` → `assignments.id`, `student_id` → `students.id` |
| `notes` | Downloadable study material | `uploaded_by` → `users.id` |
| `announcements` | Broadcast messages | `created_by` → `users.id` |
| `notifications` | Per-user alerts | `user_id` → `users.id` |

All foreign keys use `ON DELETE CASCADE` so deleting a student (i.e. their
`users` row) automatically cleans up their attendance, marks, submissions,
and notifications — keeping the database normalized and consistent.

The schema is normalized to **3NF**: no repeating groups, every non-key
column depends only on its table's primary key (e.g. student contact info
lives only in `students`, not duplicated into `attendance` or `marks`).

---

## 4. Installation & Setup (XAMPP)

### Step 1 — Install XAMPP
Download and install XAMPP (PHP 8.x + MySQL/MariaDB + Apache) from
`https://www.apachefriends.org/`.

### Step 2 — Copy the project
Extract this project folder and place it inside your XAMPP `htdocs` directory, e.g.:
```
C:\xampp\htdocs\sms          (Windows)
/Applications/XAMPP/htdocs/sms   (macOS)
/opt/lampp/htdocs/sms            (Linux)
```

### Step 3 — Start services
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### Step 4 — Create the database
1. Open `http://localhost/phpmyadmin`
2. Click **Import** (or open the **SQL** tab)
3. Select/paste the contents of `database/student_management.sql`
4. Run it — this creates the `student_management_system` database, all
   tables, foreign keys, and inserts sample data (2 students + 1 admin).

Alternatively, from a terminal:
```bash
mysql -u root -p < database/student_management.sql
```

### Step 5 — Configure database credentials
Open `config/database.php` and confirm/update:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_management_system');
define('DB_USER', 'root');
define('DB_PASS', '');   // default XAMPP root password is blank
```

### Step 6 — Set the base URL
Open `config/config.php` and make sure `BASE_URL` matches your folder name:
```php
define('BASE_URL', 'http://localhost/sms/');
```

### Step 7 — Folder permissions (Linux/macOS only)
Make sure the web server can write to the upload folders:
```bash
chmod -R 755 assets/uploads
```

### Step 8 — Run it
Visit:
```
http://localhost/sms/
```
You will land on the **Student Login** page. Use the tab at the top to
switch to **Admin / Teacher Login**, or click **Create an account** to
register as a new student.

### Demo credentials (from sample data)
| Role | Email | Password |
|---|---|---|
| Admin/Teacher | admin@school.com | Password@123 |
| Student | john@student.com | Password@123 |
| Student | emily@student.com | Password@123 |

---

## 5. Deployment to a Live Server (cPanel / shared hosting / VPS)

1. **Upload files** via FTP/SFTP or a Git deploy to your `public_html` (or a
   subfolder if not the site root).
2. **Create a MySQL database** in your hosting control panel and import
   `database/student_management.sql` via phpMyAdmin (or `mysql` CLI).
3. Update `config/database.php` with your live DB host/name/user/password
   provided by your host.
4. Update `config/config.php`:
   ```php
   define('BASE_URL', 'https://yourdomain.com/');
   ```
5. In `config/config.php`, set `display_errors` to `0` for production:
   ```php
   ini_set('display_errors', 0);
   ```
6. Enable HTTPS and uncomment the secure-cookie line in `config/config.php`:
   ```php
   ini_set('session.cookie_secure', 1);
   ```
7. Ensure `assets/uploads/` is writable by the web server user
   (`chmod 755` or as required by your host) and confirm the `.htaccess`
   inside it is active (Apache `mod_rewrite`/`AllowOverride All` must be on).
8. Visit your domain — done!

---

## 6. Extending the Project
- Add new pages by registering a route in `index.php`'s page maps and
  creating a view under `views/`.
- Add new actions by adding a public method to the relevant Controller and
  calling it from a form: `action.php?controller=Admin&action=yourMethod`.
- Add new tables/fields by extending `database/student_management.sql` and
  the corresponding Model class — keep all SQL inside Models only.

---

## 7. Tech Stack
- **Backend:** PHP 8 (core, MVC-style, PDO)
- **Database:** MySQL / MariaDB (XAMPP default)
- **Frontend:** HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, vanilla JavaScript
- **Server:** Apache (via XAMPP)
