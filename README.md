<div align="center">
  <h1>HelloMed Database Architecture</h1>
  <p><b>A Comprehensive Oracle PL/SQL Schema for Hospital Management</b></p>

  [![Oracle](https://img.shields.io/badge/Oracle-19c-F80000?style=for-the-badge&logo=oracle&logoColor=white)](https://oracle.com)
  [![PL/SQL](https://img.shields.io/badge/PL/SQL-Packages-00758F?style=for-the-badge)](https://oracle.com)
</div>

<br/>

## 🏥 About the Database

This repository contains the complete **Oracle PL/SQL database schema** that powers the HelloMed digital health platform. Instead of relying on a web framework's ORM or migration system (like Laravel/SQLite), this project is entirely driven by a robust, deeply relational Oracle database architecture.

All core logic, tables, sequences, triggers, and data manipulation rules are defined directly within Oracle using raw SQL and PL/SQL Packages.

*(Note: The frontend web application is located in the `hellomed-laravel/` subdirectory, but it relies on this database as its single source of truth).*

---

## 🏗 Schema Architecture

The database is structured to handle all daily operations of a hospital, split into several key domains:

1. **Authentication & Core Users:** Manages system users (Admins, Doctors, Patients, Staff, Pharmacists) and their sessions.
2. **Medical Services:** Tracks hospital departments, specific services offered, and doctor profiles.
3. **Appointments & Telemedicine:** Handles scheduling, digital prescriptions, online meeting links, and post-consultation doctor reviews.
4. **E-Pharmacy System:** A complete inventory and ordering system for medicines.
5. **Community & Content:** A health article CMS and Q&A system.
6. **Emergency Dispatch:** Real-time logging of ambulance requests.

---

## 💻 Software & Environment Setup (Windows)

To connect a modern PHP application to an Oracle Database on Windows, you must configure the correct drivers and environment variables. The `pdo_oci` extension is frequently missing or unstable on modern PHP Windows builds, so this project connects via the **OCI8** extension.

### 1. Oracle Instant Client Installation
PHP's OCI8 extension requires the Oracle Instant Client libraries to communicate with the database.
1. Download the **Oracle Instant Client 19c (Basic Package)** for Windows x64 from the official Oracle website.
2. Extract the downloaded ZIP file to a permanent location on your drive (e.g., `C:\oracle\instantclient_19_30`).
3. Add this exact folder path to your Windows **System Environment Variables**:
   - Search for "Environment Variables" in the Windows Start Menu.
   - Edit the system `PATH` variable.
   - Add a new entry pointing to your extracted folder (e.g., `C:\oracle\instantclient_19_30`).

### 2. PHP OCI8 Extension Setup
You must download the exact `oci8` DLL that matches your PHP version, architecture (x64), and Thread Safety (TS/NTS).
1. Run `php -i | findstr "Thread"` in your terminal to determine if your PHP is **Thread Safe (TS)** or **Non-Thread Safe (NTS)**.
2. Download the appropriate `php_oci8_19.dll` from the PECL OCI8 repository.
3. Place `php_oci8_19.dll` inside your PHP installation's `ext` folder (e.g., `C:\php\ext\`).
4. Open your `php.ini` file and add the following line:
   ```ini
   extension=oci8_19
   ```
5. Restart your terminal. Run `php -m | findstr oci8` to verify it loads without throwing "module could not be found" errors.

### 3. Laravel Configuration
Because Laravel uses standard PDO by default, this project utilizes the `yajra/laravel-pdo-via-oci8` package to bridge Laravel to the OCI8 driver seamlessly. Ensure your `.env` reflects this:
```ini
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=xe
DB_USERNAME=hellomed
DB_PASSWORD=password123
```

---

## 🚀 Database Setup & Execution

Follow these steps to build the entire HelloMed database on your local Oracle instance. All commands should be run from inside the `oracle_plsql/` directory.

### 1. Create the Database User
First, log into Oracle as an administrator (`sysdba`) to create the application user:
```bash
sqlplus / as sysdba
```
```sql
CREATE USER hellomed IDENTIFIED BY password123;
GRANT CONNECT, RESOURCE, DBA TO hellomed;
exit
```

### 2. Execute the Schema Scripts
Next, log in as the newly created `hellomed` user and execute the master setup script. This will sequentially build all tables, sequences, triggers, packages, and seed data.

```bash
sqlplus -s hellomed/password123 @run_all.sql
```

**What `run_all.sql` executes:**
1. `01_schema.sql` - Creates all relational tables.
2. `02_sequences_triggers.sql` - Creates auto-incrementing IDs via sequences and triggers.
3. `03_pkg_users.sql` - PL/SQL Package for user management.
4. `04_pkg_appointments.sql` - PL/SQL Package for appointment booking logic.
5. `05_pkg_pharmacy.sql` - PL/SQL Package for inventory control and ordering.
6. `06_pkg_ambulance.sql` - PL/SQL Package for emergency dispatch.
7. `07_seed_data.sql` - Inserts default demo data (Admins, Doctors, Medicines).

### 3. Verify the Installation
If you encounter an `ORA-01017: invalid username/password; logon denied` error when trying to log in directly, ensure you have successfully created the user via the `sysdba` account first (as shown in Step 1).

Once the setup script is complete, you can verify your tables and seeded data directly from the terminal:

```bash
# Log in to the newly created schema
sqlplus hellomed/password123@xe

# Verify that all 22 tables were created successfully
SQL> SELECT table_name FROM user_tables;

# Query the seeded data (e.g., viewing inserted departments)
SQL> SELECT * FROM departments;
```

---

## 📊 Database Management & ER Diagrams

The database schema is highly relational and divided into five logical modules for easier visualization:
1. **Core & Authentication System**
2. **Medical Services & Appointments**
3. **Digital E-Pharmacy**
4. **Content CMS & Community Q&A**
5. **Emergency Dispatch**

👉 **[View the Complete Entity-Relationship (ER) Diagrams Here](ER_DIAGRAMS.md)**

---

## ⚙️ PL/SQL Packages in Detail

The database is designed to offload heavy business logic to the database layer itself via Oracle PL/SQL Packages. This ensures data integrity, reduces application round trips, and encapsulates critical workflows inside the database engine.

The following packages are defined and utilized by the system:

### 1. `pkg_users` (`03_pkg_users.sql`)
Manages secure access and core user lifecycles.
- **`register_user`**: Securely inserts a new user record, defaulting the role to 'patient' if none is provided, and returns the newly generated auto-incrementing ID using `RETURNING INTO`.
- **`update_role`**: Allows administrators to elevate a user's privileges (e.g., from 'patient' to 'staff' or 'doctor').
- **`deactivate_user`**: Performs a soft-delete by setting the `is_active` flag to `0` instead of physically removing the record.

### 2. `pkg_appointments` (`04_pkg_appointments.sql`)
Encapsulates the workflow of scheduling and managing patient-doctor consultations.
- **`book_appointment`**: Inserts a new pending appointment record, linking the patient, doctor, department, and service in a single transaction.
- **`update_status`**: Transitions an appointment through its lifecycle (`pending` -> `confirmed` -> `completed` -> `cancelled`).
- **`attach_meeting_link`**: Updates an existing online appointment with a secure meeting URL prior to the consultation.

### 3. `pkg_pharmacy` (`05_pkg_pharmacy.sql`)
A robust e-commerce and inventory engine built entirely in PL/SQL.
- **`create_order`**: Generates a unique, timestamped `order_number` (e.g., `ORD-20231024145532-123`) and initializes a pending order.
- **`add_order_item`**: A highly transactional procedure that:
  1. Fetches the current price of a medicine from the catalog.
  2. Calculates the `line_total` (price * quantity).
  3. Inserts the line item into `medicine_order_items`.
  4. Automatically rolls up and updates the `total_amount` in the parent `medicine_orders` table.
  5. Safely decrements the `stock_quantity` in the `medicines` inventory table to prevent overselling.
- **`update_order_status`**: Transitions the order through fulfillment stages.

### 4. `pkg_ambulance` (`06_pkg_ambulance.sql`)
Handles the real-time emergency dispatch workflow.
- **`request_ambulance`**: Logs an incoming public emergency request with patient details and location.
- **`dispatch_ambulance`**: Updates the request status to `dispatched`, assigns a specific internal `staff_id`, and timestamps the dispatch event (`SYSTIMESTAMP`).
- **`resolve_request`**: Closes the loop by marking the incident as `resolved` with a final timestamp.

---

## 🔐 Legacy Database Design Patterns

Because this schema was originally designed around Oracle 11g compatibility (where `IDENTITY` columns were not natively supported), it relies heavily on classic, battle-tested Oracle design patterns:

- **Auto-Incrementing Primary Keys:** Every table is paired with a corresponding `SEQUENCE` (e.g., `users_seq`) and a `BEFORE INSERT` trigger (`users_trg`). The trigger automatically fetches `NEXTVAL` if no ID is provided, seamlessly simulating `AUTO_INCREMENT`.
- **Timestamp Management:** The same triggers automatically inject `SYSTIMESTAMP` into the `created_at` and `updated_at` columns on row insertion, and update `updated_at` during row modification.
- **Large Objects (LOBs):** Data that naturally exceeds standard string lengths—such as blog article bodies, deep JSON notification payloads, and rich medical prescriptions—are stored as `CLOB` (Character Large Object) types.
- **Soft Deletes & State Constraints:** Instead of physically deleting vital records, tables like `users` and `departments` rely on an `is_active NUMBER(1)` column enforced by `CHECK (is_active IN (0, 1))` constraints.
- **Referential Integrity:** Strict parent-child relationships are managed via `ON DELETE CASCADE` (e.g., deleting a doctor removes their reviews) and `ON DELETE SET NULL` (e.g., deleting a doctor keeps the appointment record but sets the assigned doctor to `NULL`).

---

## 🛠 Troubleshooting

- **ORA-00955: name is already used by an existing object**
  - *Cause*: You are trying to run `01_schema.sql` when the tables already exist. 
  - *Fix*: You must drop the existing tables or completely drop and recreate the `hellomed` user before re-running the setup scripts.

- **Missing Tables in Web App**
  - Ensure your web application is pointing to `127.0.0.1:1521/xe` (or your specific Oracle SID) in its `.env` file, and that you have installed the correct OCI8/PDO_OCI drivers for your language.

---
<div align="center">
  <p><i>Database Designed & Developed by Abir Hasan Arko</i></p>
</div>