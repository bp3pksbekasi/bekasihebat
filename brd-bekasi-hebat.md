# Business Requirements Document — Bekasi Hebat

**Version:** 1.0
**Date:** May 2026
**Status:** Approved for Phase 1 Development
**Target Delivery:** 2 weeks (Phase 1)

---

## 1. Project Overview

### 1.1 Background

Bekasi Hebat is a digital platform for a political party community in Kabupaten Bekasi, Indonesia. The platform serves two primary purposes:

1. **Public engagement** — community outreach, member registration, event publication, and digital membership cards
2. **Internal operations** — program management, multi-level approval workflows, and party structure administration

### 1.2 Goals

- Provide a centralized public-facing website for community members and prospective members
- Streamline internal program management with built-in approval workflows
- Enable affiliate-based member recruitment with tracking
- Provide digital membership infrastructure (membership cards, event attendance via QR)

### 1.3 Out of Scope (Phase 1)

The following are explicitly **NOT** part of Phase 1 and must not be implemented:

- RAB (Rencana Anggaran Biaya / Budget Plan) detailed forms and realization tracking
- Post-event reports with file attachments
- Mapping wilayah / bedah dapil (electoral district mapping & strategy)
- Affiliate analytics dashboard with charts
- Automated email/WhatsApp notifications for approvals
- Mobile native applications
- Public REST API

These will be addressed in Phase 2 onwards.

---

## 2. Tech Stack (FIXED)

### 2.1 Core

| Component | Choice | Version |
|-----------|--------|---------|
| Language | PHP | 8.2+ (8.4 recommended) |
| Framework | Laravel | 12.x |
| Database | MySQL | 8.x |
| ORM | Eloquent (Laravel built-in) | — |

### 2.2 Frontend per Domain

| Domain | Stack | Notes |
|--------|-------|-------|
| `kabupatenbekasihebat.com` (public) | Livewire 3 + Flux UI Free + Tailwind CSS 4 | Public-facing website |
| `app.kabupatenbekasihebat.com` (backend) | Filament 5.x (latest stable) | Internal admin panel |

In local development, both apps live in the same Laravel application. Admin panel uses path prefix `/app` instead of subdomain (controlled via `.env`).

### 2.3 Required Packages

| Package | Purpose |
|---------|---------|
| `laravel/fortify` | Authentication (login, register, password reset) |
| `livewire/livewire` | Public-facing interactivity |
| `livewire/flux` (free tier) | UI components for public site |
| `filament/filament` ^5.0 | Admin panel framework |
| `spatie/laravel-permission` | Role & permission management |
| `spatie/laravel-activitylog` | Audit log for sensitive actions |
| `simplesoftwareio/simple-qrcode` | QR code generation for membership cards |
| `laravolt/indonesia` | Indonesian administrative regions data |

### 2.4 Development Environment

- **OS:** Windows
- **Editor:** Trae
- **Web server:** `php artisan serve` (built-in)
- **MySQL:** XAMPP or standalone MySQL Server
- **DB client:** HeidiSQL
- **No Docker, no Herd**

---

## 3. Architecture

### 3.1 Single Application, Multi-Domain

One Laravel application serves both the public website and the admin panel. Domain routing is configured via:

- **Production:** Subdomain (`app.kabupatenbekasihebat.com`)
- **Local:** Path prefix (`/app`)

The switch is controlled by `ADMIN_HOST` and `ADMIN_PATH_PREFIX` environment variables.

### 3.2 No REST API in Phase 1

All interactions are server-rendered via Livewire (public) and Filament (admin). REST API may be added in Phase 2 for mobile clients.

### 3.3 Shared Components

- **Models, Services, Policies, Enums** — shared across public and admin
- **Database** — single MySQL database
- **Session** — shared via cookie domain `.kabupatenbekasihebat.com` in production
- **Auth** — same `users` table; role determines access to admin panel

### 3.4 Folder Structure

```
app/
├── Enums/                          # Shared PHP enums (EventStatus, UserGender, etc.)
├── Filament/                       # Filament resources, pages, widgets
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Http/
│   └── Controllers/
│       └── Public/                 # Public-facing controllers
├── Livewire/
│   └── Public/                     # Livewire components for public site
├── Models/                         # Eloquent models (shared)
├── Policies/                       # Authorization policies (shared)
├── Providers/
│   └── Filament/
│       └── AdminPanelProvider.php  # Filament panel configuration
└── Services/                       # Business logic (shared)
    ├── ApprovalService.php
    ├── AffiliateService.php
    └── MembershipCardService.php
resources/
├── views/
│   ├── public/                     # Blade views for public site
│   ├── livewire/                   # Livewire component views
│   └── components/                 # Shared Blade components
routes/
└── web.php                         # Public routes only (admin handled by Filament)
```

---

## 4. Domain Model

### 4.1 Core Entities

#### `users`

The single authentication table. Used by both members (public) and party officials (admin).

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| name | string | |
| email | string, unique | Required at registration |
| phone | string(20), unique | Required at registration |
| password | string | |
| member_number | string(32), unique, nullable | Format: `BH-2026-000001`, generated on registration |
| nik | string(16), unique, nullable | Filled later during profile completion |
| birth_date | date, nullable | |
| gender | enum('L','P'), nullable | |
| address | text, nullable | |
| kelurahan_code | string(13), nullable | FK to `indonesia_villages.code` (laravolt/indonesia) |
| affiliate_code | string(16), unique, nullable | Generated when affiliate role is assigned |
| email_verified_at | timestamp, nullable | |
| phone_verified_at | timestamp, nullable | |
| profile_completed_at | timestamp, nullable | Set when NIK and address are filled |
| created_at, updated_at, deleted_at | | Soft delete enabled |

#### Party Structure (3 tables)

These tables represent the party hierarchy mapped to administrative regions.

**`dpd`** (Dewan Pengurus Daerah — kabupaten/kota level)

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| name | string | e.g. "DPD Kabupaten Bekasi" |
| kabupaten_code | string(4) | FK to `indonesia_cities.code` |
| sk_number | string, nullable | Surat Keputusan number |
| created_at, updated_at | | |

**`dpc`** (Dewan Pengurus Cabang — kecamatan level)

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| name | string | e.g. "DPC Tambun Selatan" |
| kecamatan_code | string(7) | FK to `indonesia_districts.code` |
| dpd_id | bigint FK | Parent DPD |
| sk_number | string, nullable | |
| created_at, updated_at | | |

**`dpra`** (Dewan Pengurus Ranting — kelurahan level)

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| name | string | e.g. "DPRa Mekarsari" |
| kelurahan_code | string(13) | FK to `indonesia_villages.code` |
| dpc_id | bigint FK | Parent DPC |
| sk_number | string, nullable | |
| created_at, updated_at | | |

#### `user_party_assignments`

Maps users to their party structure assignment (for officials/pengurus).

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| user_id | bigint FK | |
| assignable_type | string | `App\Models\Dpd`, `App\Models\Dpc`, or `App\Models\Dpra` |
| assignable_id | bigint | Polymorphic ID |
| position | string | e.g. "Ketua", "Sekretaris", "Bendahara" |
| started_at | date | |
| ended_at | date, nullable | |
| created_at, updated_at | | |

A user can have multiple assignments. The active one is where `ended_at IS NULL`.

#### `events`

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| title | string | |
| slug | string, unique | |
| description | text | |
| starts_at | datetime | |
| ends_at | datetime, nullable | |
| location_name | string | |
| location_address | text, nullable | |
| location_kelurahan_code | string(13), nullable | FK to `indonesia_villages.code` |
| organizer_dpra_id | bigint FK | DPRa that organizes the event |
| visibility | enum('public','internal') | |
| status | enum('draft','pending_approval','approved','rejected','completed','cancelled') | |
| max_participants | int, nullable | |
| created_by | bigint FK | User who created the event |
| created_at, updated_at | | |

#### `event_registrations`

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| event_id | bigint FK | |
| user_id | bigint FK | |
| ticket_code | string(32), unique | Generated on registration |
| status | enum('registered','confirmed','cancelled','attended') | |
| checked_in_at | timestamp, nullable | Set when QR is scanned at event |
| checked_in_by | bigint FK, nullable | Panitia who scanned |
| created_at, updated_at | | |

UNIQUE constraint on (`event_id`, `user_id`).

#### Approval System

**`approval_chains`** — templates for approval workflows

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| name | string | e.g. "Standard Event Approval" |
| applies_to | string | Eloquent model class, e.g. `App\Models\Event` |
| description | text, nullable | |
| is_active | boolean | |
| created_at, updated_at | | |

**`approval_chain_steps`** — steps within a chain

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| chain_id | bigint FK | |
| step_order | int | 1, 2, 3, ... |
| required_role | string | e.g. "pengurus_dpc", "pengurus_dpd" |
| scope_level | enum('organizer_dpra','organizer_dpc','organizer_dpd') | Whose approval is needed, relative to organizer |
| created_at, updated_at | | |

**`approvals`** — actual approval instance for an entity

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| approvable_type | string | Polymorphic |
| approvable_id | bigint | |
| chain_id | bigint FK | |
| current_step | int | Current step pointer |
| status | enum('pending','approved','rejected') | |
| created_at, updated_at | | |

**`approval_logs`** — full audit trail

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| approval_id | bigint FK | |
| step_order | int | Which step this log refers to |
| actor_id | bigint FK | User who performed the action |
| action | enum('approved','rejected','reassigned') | |
| notes | text, nullable | |
| acted_at | timestamp | |
| created_at, updated_at | | |

#### `affiliate_referrals`

| Field | Type | Notes |
|-------|------|-------|
| id | bigint PK | |
| affiliate_user_id | bigint FK | The affiliate who recruited |
| referred_user_id | bigint FK | The user who registered via referral link |
| referral_code | string(16) | Snapshot of affiliate_code used |
| registered_at | timestamp | |
| created_at, updated_at | | |

UNIQUE constraint on `referred_user_id` (one user can only be referred once).

### 4.2 Roles

Managed via `spatie/laravel-permission`. Roles to create:

| Role Name | Description |
|-----------|-------------|
| `super_admin` | Full system access, bypass all policies |
| `member` | Default role for registered users |
| `affiliate` | Has affiliate dashboard access, generates referral links |
| `panitia` | Can scan QR codes for event check-in |
| `pengurus_dpra` | Can create events, submit for approval |
| `pengurus_dpc` | Can approve events from DPRa under their DPC |
| `pengurus_dpd` | Final approval for events |

A user can have multiple roles simultaneously.

---

## 5. Functional Requirements

### 5.1 Public Website (`kabupatenbekasihebat.com`)

#### 5.1.1 Pages

- **Home** — hero section, featured events, brief about
- **Tentang Kami (About)** — organization profile, vision/mission, tokoh Bekasi section (list of notable figures)
- **Kegiatan / Events** — list of public events with filter by date and category
- **Event Detail** — full event info with registration button
- **Sign In / Register** — authentication forms

#### 5.1.2 Authentication

- **Registration fields:** name, email, phone, password (minimal required)
- **Login methods:** email or phone (user's choice)
- **Profile completion:** prompted via banner on dashboard until NIK + address filled
- **Referral tracking:** if URL contains `?ref=XYZ`, store affiliate code and link to `affiliate_referrals` table on registration

#### 5.1.3 User Dashboard

- View digital membership card with QR code (static, contains `member_number`)
- View list of events the user is registered for
- View profile completion status with prompt to complete data
- **Affiliate section** (only visible if user has `affiliate` role): referral link, list of users they've referred, count of registrations

#### 5.1.4 Event Registration

- User must be logged in to register for an event
- Event must have status `approved` and visibility `public` to be visible publicly
- Internal events (`visibility = internal`) are only listed inside the admin panel

### 5.2 Admin Panel (Filament)

#### 5.2.1 Event Management

- CRUD events
- Set visibility (public/internal)
- Submit for approval (triggers approval chain)
- View approval status & history
- View registered participants

#### 5.2.2 Approval Queue

- Filtered view of events pending approval where current user is the assigned approver
- Approve or reject with notes
- Audit log automatically recorded

#### 5.2.3 User Management

- List all users with filters (role, profile completion, registration date)
- Assign/revoke roles (especially `affiliate`)
- View user's event registrations
- Soft delete user (preserves audit trail)

#### 5.2.4 Party Structure Management

- CRUD DPD, DPC, DPRa
- Assign users to party positions

#### 5.2.5 Event Check-in

- Custom Filament page for QR scanning at event location
- Scans user's membership QR (containing `member_number`)
- Validates user has active registration for the selected event
- Marks `checked_in_at` and `checked_in_by`

#### 5.2.6 Affiliate Management

- List users with `affiliate` role
- View each affiliate's referral stats
- Assign/revoke affiliate role

---

## 6. Non-Functional Requirements

### 6.1 Security

- Use Laravel's built-in CSRF protection
- All form inputs validated via Form Request classes
- Sensitive actions logged via `spatie/laravel-activitylog`
- Soft delete users (no hard delete to preserve audit trail)
- Rate limiting on auth endpoints
- HTTPS enforced in production

### 6.2 Performance

- Eager loading enforced via `Model::shouldBeStrict()` in non-production
- Indexes on foreign keys and frequently queried columns (member_number, email, phone, nik)
- Pagination on all list views

### 6.3 Code Quality

- PSR-12 coding style (enforced via Laravel Pint)
- Strict types where applicable
- PHP enums for status fields (not string constants)
- Form Request classes for validation
- Service classes for complex business logic
- Avoid fat controllers and fat models

---

## 7. Naming Conventions

- **Models:** singular PascalCase (e.g. `Event`, `EventRegistration`)
- **Tables:** plural snake_case (e.g. `events`, `event_registrations`)
- **Controllers:** singular + Controller suffix (e.g. `EventController`)
- **Livewire components:** PascalCase, namespaced under `App\Livewire\Public\`
- **Filament resources:** under `App\Filament\Resources\` (Filament v5 default)
- **Migrations:** descriptive, timestamped (Laravel default)
- **Routes:** kebab-case URLs (e.g. `/event-registration`)

---

## 8. Phase 1 Delivery Checklist

### Setup (Day 1-2)
- [ ] Project initialized with Laravel 12 + Livewire starter kit + Flux Free
- [ ] All required packages installed
- [ ] Database created and configured
- [ ] Filament installed and configured for path prefix `/app` in local
- [ ] Environment variables documented in `.env.example`

### Database (Day 3-4)
- [ ] All migrations created
- [ ] All models with relationships and casts
- [ ] Seeders for roles, wilayah Bekasi, party structure
- [ ] Test data seeded successfully

### Public Site (Day 5-8)
- [ ] Home page, About page (with tokoh Bekasi section)
- [ ] Event list & detail pages
- [ ] Registration & login (email or phone)
- [ ] User dashboard with membership card (QR code)
- [ ] Event registration flow
- [ ] Affiliate referral tracking

### Admin Panel (Day 9-13)
- [ ] Event resource with visibility toggle
- [ ] Approval workflow (DPRa → DPC → DPD)
- [ ] User management with role assignment
- [ ] Party structure CRUD
- [ ] QR check-in custom page
- [ ] Affiliate management

### Polish & Deploy (Day 14)
- [ ] Testing pass
- [ ] Production environment variables documented
- [ ] Deployment to staging server
- [ ] DNS configuration for two domains
- [ ] Smoke test in production

---

## 9. Glossary

- **DPD:** Dewan Pengurus Daerah — party leadership at kabupaten/kota level
- **DPC:** Dewan Pengurus Cabang — party leadership at kecamatan level
- **DPRa:** Dewan Pengurus Ranting — party leadership at kelurahan/desa level
- **RAB:** Rencana Anggaran Biaya — budget plan (out of scope Phase 1)
- **Dapil:** Daerah Pemilihan — electoral district (out of scope Phase 1)
- **Kabupaten/Kota:** Regency/City (Indonesian administrative level)
- **Kecamatan:** Subdistrict
- **Kelurahan/Desa:** Village
- **NIK:** Nomor Induk Kependudukan — Indonesian national ID number
- **SK:** Surat Keputusan — official appointment letter

---

**End of BRD v1.0**
