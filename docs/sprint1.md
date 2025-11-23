
# Sprint 1 Software Specification Requirements (SSR)

## Objective
Establish the foundation of the Billstack-Laravel system including authentication, business onboarding, dashboard, and multi-tenant isolation.

## Functional Requirements

### A. User Authentication
- Laravel Breeze (Blade-based)
- Features: Register, Login, Logout, Forgot Password, Reset Password
- Optional: Email verification
- Validation:
  - Unique email
  - Password minimum 6 characters

### B. Business Onboarding Wizard

#### Trigger
On first login: if `business_id == NULL`, redirect user to onboarding.

#### Step 1: Business Information
Fields:
- Business Name (required)
- Owner Name
- Email (required)
- Phone
- GST / Tax ID
- Address (street, city, state, country, pincode)
- Business Logo upload (optional)
- Invoice Prefix (default: INV-)
- Invoice Start Number (default: 1)

#### Step 2: Business Preferences
Fields:
- Currency (default: INR)
- Date Format (`d-m-Y`, `Y-m-d`, etc.)
- Timezone
- Invoice Template selection
- Terms & Conditions
- Default notes

#### Step 3: Review & Complete
Action:
- Review details
- Save business record
- Link to user
- Redirect to dashboard

## Non-Functional Requirements
- Security: Hash passwords, CSRF protection.
- Performance: Page loads < 250 ms locally.
- Maintainability: Follow Laravel MVC, use Form Requests.
- Tenant Isolation: Every model tied to `business_id`.

## Technical Requirements

### Database Schema

#### users table
Add column:
```
business_id (nullable unsigned bigInteger)
```

#### businesses table
Columns:
- id
- name
- email
- phone
- gst_number
- address
- logo
- invoice_prefix
- invoice_start_no
- currency
- date_format
- timezone
- terms
- notes
- timestamps

### Controllers
- OnboardingController
- DashboardController

### Middleware
`CheckBusinessSetup`:
- If `auth()->user()->business_id == null`, redirect to onboarding.

### Routes
```
/register
/login
/onboarding/step-1
/onboarding/step-2
/onboarding/complete
/dashboard
/logout
```

## UI Requirements

### Onboarding UI
- Step progress indicator
- Clean form layout
- Logo uploader
- Autosave options (future)

### Dashboard
- Basic business overview
- Links to customers, items, invoices, recurring profiles

## Deliverables
- Migrations
- Models updated
- Controllers
- Middleware
- Routes
- Views (Blade templates)
- Documentation

