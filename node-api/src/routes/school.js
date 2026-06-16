const express = require('express');
const bcrypt = require('bcryptjs');
const { query, transaction } = require('../db');
const { authRequired, requireRole, signToken, isGmail } = require('../auth');
const { sendConfirmation } = require('../mailService');

const router = express.Router();

function asyncHandler(fn) {
  return (req, res, next) => Promise.resolve(fn(req, res, next)).catch(next);
}

async function getSchool(userId) {
  const rows = await query(`SELECT * FROM schools WHERE user_id = :userId`, { userId });
  return rows[0];
}

router.post('/register', asyncHandler(async (req, res) => {
  const { name, email, password, phone, schoolName, schoolCode, address, district, contactPerson } = req.body;
  if (!isGmail(email)) return res.status(422).json({ error: 'A valid Gmail address is required' });

  const passwordHash = await bcrypt.hash(password, 12);
  const result = await transaction(async (conn) => {
    const [userResult] = await conn.execute(
      `INSERT INTO users (name, email, password_hash, role, phone) VALUES (?, ?, ?, 'school', ?)`,
      [name, email, passwordHash, phone || null]
    );
    const [schoolResult] = await conn.execute(
      `INSERT INTO schools (user_id, school_name, school_code, address, district, contact_person)
       VALUES (?, ?, ?, ?, ?, ?)`,
      [userResult.insertId, schoolName, schoolCode, address || null, district || null, contactPerson || null]
    );
    return { userId: userResult.insertId, schoolId: schoolResult.insertId };
  });
  res.status(201).json(result);
}));

router.post('/login', asyncHandler(async (req, res) => {
  const { email, password } = req.body;
  const users = await query(`SELECT * FROM users WHERE email = :email AND role = 'school'`, { email });
  if (!users.length || !(await bcrypt.compare(password, users[0].password_hash))) {
    return res.status(401).json({ error: 'Invalid credentials' });
  }
  const session = await signToken(users[0], req);
  res.json({ token: session.token, expiresAt: session.expiresAt, user: { id: users[0].id, role: users[0].role, email } });
}));

router.put('/profile', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const { schoolName, address, district, contactPerson } = req.body;
  await query(
    `UPDATE schools SET
      school_name = COALESCE(:schoolName, school_name),
      address = COALESCE(:address, address),
      district = COALESCE(:district, district),
      contact_person = COALESCE(:contactPerson, contact_person)
     WHERE user_id = :userId`,
    { userId: req.user.id, schoolName: schoolName || null, address: address || null, district: district || null, contactPerson: contactPerson || null }
  );
  res.json({ success: true });
}));

router.post('/participants', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const school = await getSchool(req.user.id);
  const participants = Array.isArray(req.body.participants) ? req.body.participants : [req.body];
  const inserted = [];

  for (const p of participants) {
    if (p.email && !isGmail(p.email)) return res.status(422).json({ error: 'Participant email must be Gmail if provided' });
    const result = await query(
      `INSERT INTO participants (school_id, name, identity_no, email, phone)
       VALUES (:schoolId, :name, :identityNo, :email, :phone)`,
      { schoolId: school.id, name: p.name, identityNo: p.identityNo || null, email: p.email || null, phone: p.phone || null }
    );
    inserted.push(result.insertId);
  }
  res.status(201).json({ participantIds: inserted });
}));

router.post('/events/:eventId/register-participants', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const eventId = Number(req.params.eventId);
  const participantIds = req.body.participantIds || [];
  const created = [];

  for (const participantId of participantIds) {
    const result = await query(
      `INSERT INTO registrations (user_id, event_id, participant_id, status)
       VALUES (:userId, :eventId, :participantId, 'pending')`,
      { userId: req.user.id, eventId, participantId }
    );
    const email = await sendConfirmation(result.insertId);
    created.push({ registrationId: result.insertId, participantId, email });
  }
  res.status(201).json({ registrations: created });
}));

router.get('/participants/status', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const school = await getSchool(req.user.id);
  const rows = await query(
    `SELECT p.id participant_id, p.name participant_name, e.title event_title, r.id registration_id, r.status, r.certificate_link
     FROM participants p
     LEFT JOIN registrations r ON r.participant_id = p.id
     LEFT JOIN events e ON e.id = r.event_id
     WHERE p.school_id = :schoolId
     ORDER BY p.name, e.event_date`,
    { schoolId: school.id }
  );
  res.json({ participants: rows });
}));

router.get('/reports/participation', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const school = await getSchool(req.user.id);
  const rows = await query(
    `SELECT e.title, e.event_date, r.status, COUNT(*) total
     FROM registrations r
     JOIN participants p ON p.id = r.participant_id
     JOIN events e ON e.id = r.event_id
     WHERE p.school_id = :schoolId
     GROUP BY e.id, r.status
     ORDER BY e.event_date`,
    { schoolId: school.id }
  );
  res.json({ report: rows });
}));

router.get('/certificates', authRequired, requireRole('school'), asyncHandler(async (req, res) => {
  const school = await getSchool(req.user.id);
  const rows = await query(
    `SELECT p.name participant_name, e.title event_title, r.certificate_link
     FROM registrations r
     JOIN participants p ON p.id = r.participant_id
     JOIN events e ON e.id = r.event_id
     WHERE p.school_id = :schoolId AND r.status = 'approved' AND r.certificate_link IS NOT NULL`,
    { schoolId: school.id }
  );
  res.json({ certificates: rows });
}));

module.exports = router;
