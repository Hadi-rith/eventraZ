const express = require('express');
const bcrypt = require('bcryptjs');
const multer = require('multer');
const { query, transaction } = require('../db');
const { authRequired, requireRole, signToken, isGmail } = require('../auth');
const { sendConfirmation } = require('../mailService');

const router = express.Router();
const upload = multer({ dest: process.env.UPLOAD_DIR || 'uploads' });

function asyncHandler(fn) {
  return (req, res, next) => Promise.resolve(fn(req, res, next)).catch(next);
}

router.post('/register', asyncHandler(async (req, res) => {
  const { name, email, password, phone } = req.body;
  if (!isGmail(email)) return res.status(422).json({ error: 'A valid Gmail address is required' });

  const passwordHash = await bcrypt.hash(password, 12);
  const result = await query(
    `INSERT INTO users (name, email, password_hash, role, phone)
     VALUES (:name, :email, :passwordHash, 'public', :phone)`,
    { name, email, passwordHash, phone: phone || null }
  );
  res.status(201).json({ id: result.insertId, role: 'public', email });
}));

router.post('/login', asyncHandler(async (req, res) => {
  const { email, password } = req.body;
  const users = await query(`SELECT * FROM users WHERE email = :email AND role = 'public'`, { email });
  if (!users.length || !(await bcrypt.compare(password, users[0].password_hash))) {
    return res.status(401).json({ error: 'Invalid credentials' });
  }
  const session = await signToken(users[0], req);
  res.json({ token: session.token, expiresAt: session.expiresAt, user: { id: users[0].id, role: users[0].role, email } });
}));

router.put('/profile', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const { name, phone, profile } = req.body;
  await query(
    `UPDATE users SET name = COALESCE(:name, name), phone = COALESCE(:phone, phone), profile_json = COALESCE(:profile, profile_json)
     WHERE id = :userId`,
    { userId: req.user.id, name: name || null, phone: phone || null, profile: profile ? JSON.stringify(profile) : null }
  );
  res.json({ success: true });
}));

router.get('/events', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const rows = await query(`SELECT * FROM events WHERE status = 'published' ORDER BY event_date ASC`);
  res.json({ events: rows });
}));

router.post('/events/:eventId/register', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const eventId = Number(req.params.eventId);
  const result = await query(
    `INSERT INTO registrations (user_id, event_id, status)
     VALUES (:userId, :eventId, 'pending')`,
    { userId: req.user.id, eventId }
  );
  const email = await sendConfirmation(result.insertId);
  res.status(201).json({ registrationId: result.insertId, status: 'pending', email });
}));

router.post('/registrations/:id/document', authRequired, requireRole('public'), upload.single('document'), asyncHandler(async (req, res) => {
  await query(
    `UPDATE registrations SET document_path = :path WHERE id = :id AND user_id = :userId`,
    { id: req.params.id, userId: req.user.id, path: req.file.path }
  );
  res.json({ success: true, documentPath: req.file.path });
}));

router.get('/registrations/:id/status', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const rows = await query(
    `SELECT id, event_id, status, document_path, certificate_link, registered_at
     FROM registrations WHERE id = :id AND user_id = :userId`,
    { id: req.params.id, userId: req.user.id }
  );
  if (!rows.length) return res.status(404).json({ error: 'Registration not found' });
  res.json(rows[0]);
}));

router.get('/notifications', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const rows = await query(
    `SELECT * FROM email_logs WHERE user_id = :userId ORDER BY sent_date DESC`,
    { userId: req.user.id }
  );
  res.json({ notifications: rows });
}));

router.get('/registrations/:id/certificate', authRequired, requireRole('public'), asyncHandler(async (req, res) => {
  const rows = await query(
    `SELECT certificate_link FROM registrations WHERE id = :id AND user_id = :userId AND status = 'approved'`,
    { id: req.params.id, userId: req.user.id }
  );
  if (!rows.length || !rows[0].certificate_link) return res.status(404).json({ error: 'Certificate not available' });
  res.json({ certificateLink: rows[0].certificate_link });
}));

module.exports = router;
