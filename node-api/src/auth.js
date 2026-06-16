const crypto = require('crypto');
const jwt = require('jsonwebtoken');
const { query } = require('./db');

const JWT_SECRET = process.env.JWT_SECRET || 'dev-secret-change-me';

function isGmail(email) {
  return /^[A-Za-z0-9._%+-]+@gmail\.com$/i.test(String(email || '').trim());
}

function signToken(user, req) {
  const jwtId = crypto.randomUUID();
  const expiresIn = '8h';
  const token = jwt.sign(
    { sub: user.id, role: user.role, email: user.email },
    JWT_SECRET,
    { expiresIn, jwtid: jwtId }
  );

  const expiresAt = new Date(Date.now() + 8 * 60 * 60 * 1000);
  const ip = req.ip || req.socket?.remoteAddress || null;
  const agent = req.get('user-agent') || null;

  return query(
    `INSERT INTO user_sessions (user_id, jwt_id, ip_address, user_agent, expires_at)
     VALUES (:userId, :jwtId, :ip, :agent, :expiresAt)`,
    { userId: user.id, jwtId, ip, agent, expiresAt }
  ).then(() => ({ token, expiresAt }));
}

async function authRequired(req, res, next) {
  try {
    const header = req.get('authorization') || '';
    const token = header.startsWith('Bearer ') ? header.slice(7) : null;
    if (!token) return res.status(401).json({ error: 'Missing bearer token' });

    const payload = jwt.verify(token, JWT_SECRET);
    const sessions = await query(
      `SELECT id FROM user_sessions
       WHERE jwt_id = :jwtId AND revoked_at IS NULL AND expires_at > NOW()`,
      { jwtId: payload.jti }
    );
    if (!sessions.length) return res.status(401).json({ error: 'Session expired or revoked' });

    req.user = { id: Number(payload.sub), role: payload.role, email: payload.email, jwtId: payload.jti };
    next();
  } catch (error) {
    res.status(401).json({ error: 'Invalid token' });
  }
}

function requireRole(...roles) {
  return (req, res, next) => {
    if (!req.user || !roles.includes(req.user.role)) {
      return res.status(403).json({ error: 'Forbidden' });
    }
    next();
  };
}

module.exports = { authRequired, requireRole, signToken, isGmail };
