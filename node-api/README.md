# EventraZ Registration API

Node.js + Express REST API for a multi-user event registration system using MySQL, JWT auth, and Gmail API OAuth2 emails.

## Setup

```bash
cd node-api
cp .env.example .env
npm install
mysql -u root -p < schema.sql
npm start
```

Create at least one admin user in `users` with `role='admin'` and a bcrypt password hash, or use `POST /api/admin/users` after bootstrapping an admin manually.

## Gmail API

Set these in `.env`:

```env
GMAIL_CLIENT_ID=
GMAIL_CLIENT_SECRET=
GMAIL_REDIRECT_URI=https://developers.google.com/oauthplayground
GMAIL_REFRESH_TOKEN=
GMAIL_FROM=yourgmail@gmail.com
```

The API only sends to addresses matching `*@gmail.com`. Invalid non-Gmail recipients are logged as `skipped`.

## Auth Header

```http
Authorization: Bearer <jwt>
```

## Public Endpoints

### Register Account

`POST /api/public/register`

```json
{ "name": "Ali", "email": "ali@gmail.com", "password": "secret123", "phone": "0123456789" }
```

Response:

```json
{ "id": 1, "role": "public", "email": "ali@gmail.com" }
```

### Login

`POST /api/public/login`

```json
{ "email": "ali@gmail.com", "password": "secret123" }
```

Response:

```json
{ "token": "jwt", "expiresAt": "2026-06-15T18:00:00.000Z", "user": { "id": 1, "role": "public", "email": "ali@gmail.com" } }
```

### Update Profile

`PUT /api/public/profile`

```json
{ "name": "Ali Ahmad", "phone": "0199999999", "profile": { "ic": "010101010101" } }
```

Response:

```json
{ "success": true }
```

### View Events

`GET /api/public/events`

Response:

```json
{ "events": [{ "id": 10, "title": "Robotics Camp", "category": "STEM", "subcategory": "Workshop", "event_date": "2026-07-01" }] }
```

### Register For Event

`POST /api/public/events/10/register`

Response:

```json
{ "registrationId": 22, "status": "pending", "email": { "status": "sent", "id": "gmail-message-id" } }
```

### Upload Required Document

`POST /api/public/registrations/22/document`

Form-data:

```json
{ "document": "<file>" }
```

Response:

```json
{ "success": true, "documentPath": "uploads/abc123" }
```

### Check Registration Status

`GET /api/public/registrations/22/status`

Response:

```json
{ "id": 22, "event_id": 10, "status": "approved", "document_path": "uploads/abc123", "certificate_link": "https://..." }
```

### Get Notifications

`GET /api/public/notifications`

Response:

```json
{ "notifications": [{ "type": "confirmation", "sent_date": "2026-06-15T10:00:00.000Z", "status": "sent" }] }
```

### Download Certificate

`GET /api/public/registrations/22/certificate`

Response:

```json
{ "certificateLink": "https://example.com/certificates/22.pdf" }
```

## School Endpoints

### Register School Account

`POST /api/school/register`

```json
{
  "name": "Teacher Admin",
  "email": "schooladmin@gmail.com",
  "password": "secret123",
  "phone": "0199999999",
  "schoolName": "SMK EventraZ",
  "schoolCode": "TBA1001",
  "address": "Terengganu",
  "district": "Kuala Terengganu",
  "contactPerson": "Pn. Sara"
}
```

Response:

```json
{ "userId": 2, "schoolId": 1 }
```

### Login

`POST /api/school/login`

```json
{ "email": "schooladmin@gmail.com", "password": "secret123" }
```

### Update School Profile

`PUT /api/school/profile`

```json
{ "schoolName": "SMK EventraZ Baru", "district": "Besut", "contactPerson": "En. Amir" }
```

### Add Participant

`POST /api/school/participants`

```json
{
  "participants": [
    { "name": "Student One", "identityNo": "010101010101", "email": "studentone@gmail.com" },
    { "name": "Student Two", "identityNo": "020202020202" }
  ]
}
```

Response:

```json
{ "participantIds": [5, 6] }
```

### Register Participants For Event

`POST /api/school/events/10/register-participants`

```json
{ "participantIds": [5, 6] }
```

Response:

```json
{ "registrations": [{ "registrationId": 30, "participantId": 5, "email": { "status": "sent" } }] }
```

### Check Participant Status

`GET /api/school/participants/status`

Response:

```json
{ "participants": [{ "participant_name": "Student One", "event_title": "Robotics Camp", "status": "approved" }] }
```

### Download Participation Report

`GET /api/school/reports/participation`

Response:

```json
{ "report": [{ "title": "Robotics Camp", "event_date": "2026-07-01", "status": "approved", "total": 2 }] }
```

### Download Participant Certificates

`GET /api/school/certificates`

Response:

```json
{ "certificates": [{ "participant_name": "Student One", "event_title": "Robotics Camp", "certificate_link": "https://..." }] }
```

## Admin Endpoints

All admin endpoints require admin JWT except `POST /api/admin/login`.

### Manage Users

`GET /api/admin/users`

`POST /api/admin/users`

```json
{ "name": "Organizer", "email": "organizer@gmail.com", "password": "secret123", "role": "admin" }
```

`PUT /api/admin/users/1`

```json
{ "name": "Updated Name", "phone": "0111111111", "role": "public" }
```

`DELETE /api/admin/users/1`

### Manage Events

`GET /api/admin/events`

`POST /api/admin/events`

```json
{
  "title": "Robotics Camp",
  "description": "STEM workshop",
  "category": "STEM",
  "subcategory": "Workshop",
  "eventDate": "2026-07-01",
  "location": "Hall A",
  "capacity": 100,
  "status": "published"
}
```

`PUT /api/admin/events/10`

```json
{ "title": "Robotics Camp 2026", "status": "published" }
```

`DELETE /api/admin/events/10`

### Approve Registrations

`POST /api/admin/registrations/22/approve`

```json
{ "status": "approved" }
```

Response:

```json
{ "success": true, "status": "approved", "email": { "status": "sent" } }
```

### Manage Categories/Subcategories

`GET /api/admin/categories`

`POST /api/admin/categories`

```json
{ "name": "STEM" }
```

`PUT /api/admin/categories/1`

```json
{ "name": "Technology" }
```

`DELETE /api/admin/categories/1`

`POST /api/admin/categories/1/subcategories`

```json
{ "name": "Workshop" }
```

`PUT /api/admin/subcategories/1`

```json
{ "name": "Competition" }
```

`DELETE /api/admin/subcategories/1`

### Generate Reports

`GET /api/admin/reports`

Response:

```json
{ "byEvent": [{ "event_id": 10, "title": "Robotics Camp", "status": "approved", "total": 30 }], "byRole": [{ "role": "public", "total": 200 }] }
```

### Send Manual Notifications

`POST /api/admin/notifications/manual`

```json
{
  "userId": 1,
  "registrationId": 22,
  "eventId": 10,
  "to": "ali@gmail.com",
  "subject": "EventraZ Update",
  "message": "Please upload your document."
}
```

### Manage Certificates

`POST /api/admin/registrations/22/certificate`

```json
{ "certificateLink": "https://example.com/certificates/22.pdf" }
```

Or upload form-data field `certificate`.

### View Statistics

`GET /api/admin/statistics`

Response:

```json
{ "eventCounts": [{ "id": 10, "title": "Robotics Camp", "total_registrations": 30 }], "statusCounts": [{ "status": "approved", "total": 20 }], "upcomingEvents": 4 }
```

### Run Reminder Checker Manually

`POST /api/admin/reminders/run-now`

Response:

```json
{ "target": "2026-07-01", "processed": 10, "results": [{ "registrationId": 22, "email": { "status": "sent" } }] }
```

## Reminder Rules

The scheduler runs daily at `08:00`.

- Finds approved registrations only.
- Sends when the adjusted reminder date is due.
- Does not run on Friday or Saturday.
- The normal reminder date is `event_date - 3 days`.
- If that reminder date falls on Friday/Saturday, it is moved to the next allowed day, Sunday through Thursday.
- It will not send another reminder if a sent reminder already exists for that registration and event.

## Email Templates

Templates live in [src/templates.js](src/templates.js):

- `confirmationTemplate`
- `reminderTemplate`
- `manualTemplate`
