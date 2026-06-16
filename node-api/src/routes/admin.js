const express = require('express');
const bcrypt = require('bcryptjs');
const multer = require('multer');
const { query } = require('../db');
const { authRequired, requireRole, signToken, isGmail } = require('../auth');
const { sendConfirmation, sendManualNotification } = require('../mailService');

const router = express.Router();
const upload = multer({ dest: process.env.UPLOAD_DIR || 'uploads' });

function asyncHandler(fn) {
  return (req, res, next) => Promise.resolve(fn(req, res, next)).catch(next);
}

router.post('/login', asyncHandler(async (req, res) => {
  const { email, password } = req.body;
  const users = await query(`SELECT * FROM users WHERE email = :email AND role = 'admin'`, { email });
  if (!users.length || !(await bcrypt.compare(password, users[0].password_hash))) {
    return res.status(401).json({ error: 'Invalid credentials' });
  }
  const session = await signToken(users[0], req);
  res.json({ token: session.token, expiresAt: session.expiresAt, user: { id: users[0].id, role: users[0].role, email } });
}));

router.use(authRequired, requireRole('admin'));

router.get('/users', asyncHandler(async (req, res) => {
  const rows = await query(`SELECT id, name, email, role, phone, created_at FROM users ORDER BY created_at DESC`);
  res.json({ users: rows });
}));

router.post('/users', asyncHandler(async (req, res) => {
  const { name, email, password, role, phone } = req.body;
  if (!['public', 'school', 'admin'].includes(role)) return res.status(422).json({ error: 'Invalid role' });
  if (!isGmail(email)) return res.status(422).json({ error: 'A valid Gmail address is required' });
  const passwordHash = await bcrypt.hash(password, 12);
  const result = await query(
    `INSERT INTO users (name, email, password_hash, role, phone)
     VALUES (:name, :email, :passwordHash, :role, :phone)`,
    { name, email, passwordHash, role, phone: phone || null }
  );
  res.status(201).json({ id: result.insertId });
}));

router.put('/users/:id', asyncHandler(async (req, res) => {
  const { name, phone, role } = req.body;
  await query(
    `UPDATE users SET name = COALESCE(:name, name), phone = COALESCE(:phone, phone), role = COALESCE(:role, role)
     WHERE id = :id`,
    { id: req.params.id, name: name || null, phone: phone || null, role: role || null }
  );
  res.json({ success: true });
}));

router.delete('/users/:id', asyncHandler(async (req, res) => {
  await query(`DELETE FROM users WHERE id = :id`, { id: req.params.id });
  res.json({ success: true });
}));

router.get('/events', asyncHandler(async (req, res) => {
  const rows = await query(`SELECT * FROM events ORDER BY event_date DESC`);
  res.json({ events: rows });
}));

router.post('/events', asyncHandler(async (req, res) => {
  const { title, description, categoryId, subcategoryId, category, subcategory, eventDate, location, capacity, status } = req.body;
  const result = await query(
    `INSERT INTO events
     (title, description, category_id, subcategory_id, category, subcategory, event_date, location, capacity, status, created_by)
     VALUES (:title, :description, :categoryId, :subcategoryId, :category, :subcategory, :eventDate, :location, :capacity, :status, :createdBy)`,
    {
      title,
      description: description || null,
      categoryId: categoryId || null,
      subcategoryId: subcategoryId || null,
      category,
      subcategory: subcategory || null,
      eventDate,
      location: location || null,
      capacity: capacity || null,
      status: status || 'published',
      createdBy: req.user.id,
    }
  );
  res.status(201).json({ id: result.insertId });
}));

router.put('/events/:id', asyncHandler(async (req, res) => {
  const { title, description, category, subcategory, eventDate, location, capacity, status } = req.body;
  await query(
    `UPDATE events SET
      title = COALESCE(:title, title),
      description = COALESCE(:description, description),
      category = COALESCE(:category, category),
      subcategory = COALESCE(:subcategory, subcategory),
      event_date = COALESCE(:eventDate, event_date),
      location = COALESCE(:location, location),
      capacity = COALESCE(:capacity, capacity),
      status = COALESCE(:status, status)
     WHERE id = :id`,
    { id: req.params.id, title: title || null, description: description || null, category: category || null, subcategory: subcategory || null, eventDate: eventDate || null, location: location || null, capacity: capacity || null, status: status || null }
  );
  res.json({ success: true });
}));

router.delete('/events/:id', asyncHandler(async (req, res) => {
  await query(`DELETE FROM events WHERE id = :id`, { id: req.params.id });
  res.json({ success: true });
}));

router.post('/registrations/:id/approve', asyncHandler(async (req, res) => {
  const status = req.body.status || 'approved';
  if (!['approved', 'rejected', 'pending'].includes(status)) return res.status(422).json({ error: 'Invalid status' });
  await query(`UPDATE registrations SET status = :status WHERE id = :id`, { id: req.params.id, status });
  const email = status === 'approved' ? await sendConfirmation(req.params.id) : null;
  res.json({ success: true, status, email });
}));

router.get('/categories', asyncHandler(async (req, res) => {
  const rows = await query(
    `SELECT c.id, c.name, JSON_ARRAYAGG(JSON_OBJECT('id', s.id, 'name', s.name)) subcategories
     FROM categories c
     LEFT JOIN subcategories s ON s.category_id = c.id
     GROUP BY c.id
     ORDER BY c.name`
  );
  res.json({ categories: rows });
}));

router.post('/categories', asyncHandler(async (req, res) => {
  const result = await query(`INSERT INTO categories (name) VALUES (:name)`, { name: req.body.name });
  res.status(201).json({ id: result.insertId });
}));

router.put('/categories/:id', asyncHandler(async (req, res) => {
  await query(`UPDATE categories SET name = :name WHERE id = :id`, { id: req.params.id, name: req.body.name });
  res.json({ success: true });
}));

router.delete('/categories/:id', asyncHandler(async (req, res) => {
  await query(`DELETE FROM categories WHERE id = :id`, { id: req.params.id });
  res.json({ success: true });
}));

router.post('/categories/:id/subcategories', asyncHandler(async (req, res) => {
  const result = await query(`INSERT INTO subcategories (category_id, name) VALUES (:categoryId, :name)`, { categoryId: req.params.id, name: req.body.name });
  res.status(201).json({ id: result.insertId });
}));

router.put('/subcategories/:id', asyncHandler(async (req, res) => {
  await query(`UPDATE subcategories SET name = :name WHERE id = :id`, { id: req.params.id, name: req.body.name });
  res.json({ success: true });
}));

router.delete('/subcategories/:id', asyncHandler(async (req, res) => {
  await query(`DELETE FROM subcategories WHERE id = :id`, { id: req.params.id });
  res.json({ success: true });
}));

router.get('/reports', asyncHandler(async (req, res) => {
  const byEvent = await query(
    `SELECT e.id event_id, e.title, r.status, COUNT(r.id) total
     FROM events e LEFT JOIN registrations r ON r.event_id = e.id
     GROUP BY e.id, r.status ORDER BY e.event_date DESC`
  );
  const byRole = await query(`SELECT role, COUNT(*) total FROM users GROUP BY role`);
  res.json({ byEvent, byRole });
}));

router.post('/notifications/manual', asyncHandler(async (req, res) => {
  const { userId, registrationId, eventId, to, subject, message } = req.body;
  const result = await sendManualNotification({ userId, registrationId, eventId, to, subject, message });
  res.json({ success: true, email: result });
}));

router.post('/registrations/:id/certificate', upload.single('certificate'), asyncHandler(async (req, res) => {
  const certificateLink = req.body.certificateLink || req.file?.path;
  await query(`UPDATE registrations SET certificate_link = :certificateLink WHERE id = :id`, { id: req.params.id, certificateLink });
  res.json({ success: true, certificateLink });
}));

router.get('/statistics', asyncHandler(async (req, res) => {
  const eventCounts = await query(
    `SELECT e.id, e.title, COUNT(r.id) total_registrations
     FROM events e LEFT JOIN registrations r ON r.event_id = e.id
     GROUP BY e.id ORDER BY total_registrations DESC`
  );
  const statusCounts = await query(`SELECT status, COUNT(*) total FROM registrations GROUP BY status`);
  const upcoming = await query(`SELECT COUNT(*) total FROM events WHERE event_date >= CURDATE() AND status = 'published'`);
  res.json({ eventCounts, statusCounts, upcomingEvents: upcoming[0].total });
}));

module.exports = router;
