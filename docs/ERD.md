# ERD Eventku

```mermaid
erDiagram
    USERS ||--o{ EVENTS : organizes
    USERS ||--o{ REGISTRATIONS : makes
    USERS ||--o{ EVENTS : verifies
    USERS ||--o{ CHECKINS : performs
    USERS ||--o{ ACTIVITY_LOGS : creates
    CATEGORIES ||--o{ EVENTS : classifies
    EVENTS ||--o{ TICKET_TYPES : offers
    EVENTS ||--o{ REGISTRATIONS : receives
    TICKET_TYPES ||--o{ REGISTRATIONS : selected_for
    REGISTRATIONS ||--|| DIGITAL_TICKETS : issues
    DIGITAL_TICKETS ||--o{ CHECKINS : records

    USERS {
      bigint id PK
      string name
      string email UK
      string role
      string status
    }
    CATEGORIES {
      bigint id PK
      string name
      string slug UK
      boolean is_active
    }
    EVENTS {
      bigint id PK
      bigint organizer_id FK
      bigint category_id FK
      string name
      string slug UK
      datetime start_at
      datetime end_at
      datetime registration_start_at
      datetime registration_end_at
      string status
      bigint verified_by FK
    }
    TICKET_TYPES {
      bigint id PK
      bigint event_id FK
      string name
      unsignedInteger quota
      unsignedInteger registration_limit_per_user
    }
    REGISTRATIONS {
      bigint id PK
      bigint user_id FK
      bigint event_id FK
      bigint ticket_type_id FK
      string registration_code UK
      string status
      datetime registered_at
    }
    DIGITAL_TICKETS {
      bigint id PK
      bigint registration_id FK UK
      string ticket_code UK
      string qr_token_hash UK
      string status
      datetime used_at
    }
    CHECKINS {
      bigint id PK
      bigint digital_ticket_id FK
      bigint successful_ticket_id FK UK nullable
      bigint checked_in_by FK
      string method
      string result
      datetime checked_in_at
    }
    ACTIVITY_LOGS {
      bigint id PK
      bigint user_id FK
      string action
      string entity_type
      bigint entity_id
    }
```

Registrasi memakai unique constraint `user_id + event_id`; pembatalan tidak menghapus baris sehingga skema ini memilih riwayat yang konsisten daripada pendaftaran ulang pada versi awal.

