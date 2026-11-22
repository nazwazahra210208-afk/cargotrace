# CargoTrace — Global Shipment Visibility & Tracking Platform

CargoTrace is a web-based maritime and cargo shipment visibility platform built with **Laravel**, **MySQL**, **Tailwind CSS**, **Leaflet.js**, and **REST API** integration. It enables logistics operators, administrators, and customers to track shipments, monitor real-time route coordinates, evaluate deterministic ETA delays, manage operational exceptions, and inspect system audit logs.

---

## Problem & Solution
- **Problem:** Shipment status, route progress, and delay exceptions are often fragmented across carriers, leaving customers and operators without a single source of truth.
- **Solution:** CargoTrace centralizes shipment lifecycle management (`Created` → `In Transit` → `Delivered`), chronological tracking events, interactive maritime maps, destination port weather forecasts, exception reporting, role-based access control, and JSON REST APIs.

---

## Core Features
1. **Role-Based Access Control (RBAC):**
   - **Administrator:** Manage users, carriers, locations, shipments, and inspect system audit logs.
   - **Logistics Operator:** Create shipments, enforce forward-only lifecycle transitions, record chronological tracking events, and report operational exceptions.
   - **Customer:** Filter and inspect owned shipments, view interactive maritime route maps, weather forecasts, timelines, and active exceptions.
   - **Public Tracking (`/track`):** Instant shipment lookup by tracking number without requiring login.
2. **Deterministic Business Rules:**
   - **Terminal Status Lock:** Shipments marked as `Delivered` cannot be reverted to earlier states.
   - **ETA & Delay Evaluation:** Automatically compares `expected_arrival_date` against actual/current timestamps and classifies delays (`0–6h: LOW`, `>6–24h: MEDIUM`, `>24h: HIGH`).
3. **Interactive Map & External Weather Integration:**
   - **Leaflet.js + OpenStreetMap** plotting origin and destination port coordinates with route polylines.
   - **WeatherService** via Laravel HTTP Client with graceful fallback handling.
4. **RESTful JSON API (`/api/*`):**
   - Bearer token authentication (`/api/register`, `/api/login`, `/api/logout`, `/api/user`) and role-protected CRUD endpoints for shipments, tracking events, carriers, locations, and exceptions.

---

## Tech Stack
- **Backend:** PHP 8.2+, Laravel 12, Eloquent ORM, MySQL 8+
- **Frontend:** Blade Templates, Tailwind CSS, Vanilla JavaScript
- **Maps & External Services:** Leaflet.js, OpenStreetMap Tiles, Open-Meteo / Weather HTTP Client
- **Testing:** PHPUnit Feature Tests

---

## Installation & Setup

1. **Clone the repository:**
   
```bash
   git clone [https://github.com/nazwazahra210208-afk/cargotrace.git](https://github.com/nazwazahra210208-afk/cargotrace.git)
   cd cargotrace
   
```

2. **Install PHP dependencies:**
   
```bash
   composer install
   
```

3. **Configure environment variables:**
   
```bash
   cp .env.example .env
   php artisan key:generate
   
```
   Update `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match your local MySQL configuration.

4. **Run database migrations and seeders:**
   
```bash
   php artisan migrate --seed
   
```

5. **Start the local development server:**
   
```bash
   php artisan serve
   
```
   Open `http://127.0.0.1:8000` in your browser.

---

## Demo Accounts
| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | `password` |
| **Logistics Operator** | `operator@cargotrace.com` | `password` |
| **Customer** | `customer@cargotrace.com` | `password` |

*(Adjust credentials if customized in `DatabaseSeeder.php`.)*

---

## REST API Endpoints Summary
- `POST /api/register` — Register a new customer account and receive a Bearer token
- `POST /api/login` — Authenticate user and issue a Bearer token
- `POST /api/logout` — Revoke active Bearer token
- `GET /api/user` — Retrieve authenticated user profile
- `GET /api/shipments` & `POST /api/shipments` — List (paginated & filtered) or create shipments
- `GET /api/shipments/{id}`, `PUT/PATCH /api/shipments/{id}`, `DELETE /api/shipments/{id}` — Inspect, update, or delete shipment
- `GET /api/shipments/{id}/tracking` & `POST /api/shipments/{id}/tracking` — View or append tracking events
- `GET /api/carriers` & `POST /api/carriers` — List or create carriers
- `GET /api/locations` & `POST /api/locations` — List or create port locations
- `GET /api/shipments/{id}/exceptions`, `POST /api/shipments/{id}/exceptions`, `PATCH /api/exceptions/{id}` — Manage operational exceptions

---

## Running Automated Tests
Execute the PHPUnit feature test suite using:
```bash
php artisan test --filter=CargoTraceTest
```

---

## Third-Party Libraries & Attributions
- **Laravel Framework** (MIT License)
- **Tailwind CSS** (MIT License)
- **Leaflet.js** (BSD-2-Clause License) & **OpenStreetMap** contributors
- **Open-Meteo Weather API** for destination port weather data