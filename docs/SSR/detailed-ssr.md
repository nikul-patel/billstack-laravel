
# Billstack-Laravel — Detailed Project SSR (Mid-Level)

## 1. System Overview
Billstack is a multi-tenant invoice and customer management platform with recurring billing automation.

## 2. Modules Specification
### 2.1 Authentication
- Laravel Breeze
- Login, Register, Forgot Password

### 2.2 Business Module
- Business profile
- Preferences (currency, timezone, invoice format)
- Logo management

### 2.3 Customers Module
- Add/Edit/Delete
- Contact details
- Customer activity logs

### 2.4 Items Module
- Products & Services catalog
- Pricing rules
- Tax settings

### 2.5 Invoices
- Manual invoice generator
- Classic detailed template
- PDF export
- Email invoice delivery

### 2.6 Recurring Profiles
- Monthly schedules
- Auto invoice creation
- Bulk processing/batch overview

### 2.7 Payments
- Add payments
- Payment history
- Outstanding balance tracking

## 3. Database ERD
- Users → Business (1:1)
- Business → Customers (1:N)
- Business → Items (1:N)
- Customer → Invoices (1:N)
- Invoice → Items (1:N)
- Invoice → Payments (1:N)

## 4. API Endpoints
- /customers (CRUD)
- /items (CRUD)
- /invoices (CRUD + PDF)
- /payments
- /reports

## 5. UI/UX Flow
- Onboarding wizard
- Dashboard analytics
- Invoice preview modal
- Recurring billing status

## 6. Non-Functional Requirements
- Security: CSRF, Hashing, Validation
- Performance: optimized queries
- Scalability: business isolation via business_id
- Logging: user actions
