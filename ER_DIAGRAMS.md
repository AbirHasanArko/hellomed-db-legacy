# HelloMed Entity-Relationship Diagrams

This document contains the complete graphical entity-relationship (ER) models for the HelloMed Oracle Database. 
The system is divided into five logical modules for easier visualization.

---

## 1. Core & Authentication System
Handles user identities, sessions, extended profiles, and system-wide logging.

```mermaid
erDiagram
    USERS {
        NUMBER id PK
        VARCHAR2 name
        VARCHAR2 email
        VARCHAR2 password
        VARCHAR2 role
        NUMBER is_active
        TIMESTAMP created_at
    }
    SESSIONS {
        VARCHAR2 id PK
        NUMBER user_id FK
        VARCHAR2 ip_address
        VARCHAR2 user_agent
        TIMESTAMP last_activity
    }
    PATIENT_PROFILES {
        NUMBER id PK
        NUMBER user_id FK
        CLOB allergies
        CLOB medical_notes
    }
    AUDIT_LOGS {
        NUMBER id PK
        NUMBER actor_user_id FK
        VARCHAR2 action
        VARCHAR2 entity_type
        NUMBER entity_id
    }
    NOTIFICATION_LOGS {
        NUMBER id PK
        NUMBER user_id FK
        VARCHAR2 channel
        VARCHAR2 event_key
        VARCHAR2 status
    }

    USERS ||--o{ SESSIONS : "authenticates via"
    USERS ||--|| PATIENT_PROFILES : "has extended"
    USERS ||--o{ AUDIT_LOGS : "performs action"
    USERS ||--o{ NOTIFICATION_LOGS : "receives"
```

---

## 2. Medical Services & Appointments
The backbone of the hospital's operations, connecting doctors, patients, departments, and scheduling.

```mermaid
erDiagram
    DEPARTMENTS {
        NUMBER id PK
        VARCHAR2 name
        VARCHAR2 slug
        NUMBER is_active
    }
    DOCTORS {
        NUMBER id PK
        NUMBER department_id FK
        NUMBER user_id FK
        VARCHAR2 name
        VARCHAR2 specialty
        NUMBER consultation_fee
    }
    SERVICES {
        NUMBER id PK
        NUMBER department_id FK
        NUMBER doctor_id FK
        VARCHAR2 name
        NUMBER price
    }
    APPOINTMENTS {
        NUMBER id PK
        NUMBER user_id FK "Patient"
        NUMBER doctor_id FK
        NUMBER department_id FK
        NUMBER service_id FK
        VARCHAR2 status
        TIMESTAMP scheduled_for
    }
    PAYMENTS {
        NUMBER id PK
        NUMBER appointment_id FK
        NUMBER user_id FK
        NUMBER amount
        VARCHAR2 status
    }
    APPOINTMENT_PRESCRIPTIONS {
        NUMBER id PK
        NUMBER appointment_id FK
        NUMBER medicine_id FK
        VARCHAR2 dosage
    }
    DOCTOR_REVIEWS {
        NUMBER id PK
        NUMBER doctor_id FK
        NUMBER user_id FK
        NUMBER rating
        CLOB comment
    }

    DEPARTMENTS ||--o{ DOCTORS : "employs"
    DEPARTMENTS ||--o{ SERVICES : "offers"
    DOCTORS ||--o{ SERVICES : "performs"
    DOCTORS ||--o{ APPOINTMENTS : "conducts"
    USERS ||--o{ APPOINTMENTS : "books"
    APPOINTMENTS ||--o{ PAYMENTS : "billed via"
    APPOINTMENTS ||--o{ APPOINTMENT_PRESCRIPTIONS : "results in"
    USERS ||--o{ DOCTOR_REVIEWS : "writes"
    DOCTORS ||--o{ DOCTOR_REVIEWS : "receives"
```

---

## 3. Digital E-Pharmacy
Inventory management and digital medicine ordering.

```mermaid
erDiagram
    MEDICINES {
        NUMBER id PK
        VARCHAR2 name
        VARCHAR2 medicine_group
        NUMBER price
        NUMBER stock_quantity
        NUMBER requires_prescription
    }
    MEDICINE_ORDERS {
        NUMBER id PK
        NUMBER user_id FK
        VARCHAR2 order_number
        VARCHAR2 status
        NUMBER total_amount
    }
    MEDICINE_ORDER_ITEMS {
        NUMBER id PK
        NUMBER medicine_order_id FK
        NUMBER medicine_id FK
        NUMBER quantity
        NUMBER unit_price
    }

    USERS ||--o{ MEDICINE_ORDERS : "places"
    MEDICINE_ORDERS ||--o{ MEDICINE_ORDER_ITEMS : "contains"
    MEDICINES ||--o{ MEDICINE_ORDER_ITEMS : "added as"
```

---

## 4. Content CMS & Community Q&A
Public health articles authored by doctors and patient questions.

```mermaid
erDiagram
    ARTICLE_CATEGORIES {
        NUMBER id PK
        VARCHAR2 name
    }
    ARTICLES {
        NUMBER id PK
        NUMBER article_category_id FK
        NUMBER user_id FK "Doctor/Admin"
        VARCHAR2 title
        CLOB body
        NUMBER is_published
    }
    ARTICLE_COMMENTS {
        NUMBER id PK
        NUMBER article_id FK
        NUMBER user_id FK
        CLOB comment
    }
    QNA_QUESTIONS {
        NUMBER id PK
        NUMBER user_id FK
        VARCHAR2 title
        CLOB question
    }
    QNA_ANSWERS {
        NUMBER id PK
        NUMBER qna_question_id FK
        NUMBER user_id FK "Doctor"
        CLOB answer
    }

    ARTICLE_CATEGORIES ||--o{ ARTICLES : "categorizes"
    USERS ||--o{ ARTICLES : "authors"
    ARTICLES ||--o{ ARTICLE_COMMENTS : "has"
    USERS ||--o{ QNA_QUESTIONS : "asks"
    QNA_QUESTIONS ||--o{ QNA_ANSWERS : "receives"
    USERS ||--o{ QNA_ANSWERS : "provides"
```

---

## 5. Emergency Dispatch
Real-time tracking of ambulance requests.

```mermaid
erDiagram
    AMBULANCE_REQUESTS {
        NUMBER id PK
        NUMBER user_id FK "Nullable"
        VARCHAR2 patient_name
        VARCHAR2 patient_phone
        NUMBER latitude
        NUMBER longitude
        VARCHAR2 status
        NUMBER staff_id FK "Dispatcher"
    }

    USERS ||--o{ AMBULANCE_REQUESTS : "requests"
    USERS ||--o{ AMBULANCE_REQUESTS : "dispatches"
```
