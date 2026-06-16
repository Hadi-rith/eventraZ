const { query } = require('./db');
const { isGmail } = require('./auth');
const { sendEmail } = require('./gmail');
const { confirmationTemplate, reminderTemplate, manualTemplate } = require('./templates');

async function logEmail({ userId, registrationId, eventId, to, type, subject, status, providerMessageId, errorMessage }) {
  await query(
    `INSERT INTO email_logs
     (user_id, registration_id, event_id, recipient_email, type, subject, sent_date, status, provider_message_id, error_message)
     VALUES (:userId, :registrationId, :eventId, :to, :type, :subject, NOW(), :status, :providerMessageId, :errorMessage)`,
    { userId, registrationId, eventId, to, type, subject, status, providerMessageId, errorMessage }
  );
}

async function sendAndLog({ userId, registrationId = null, eventId = null, to, type, subject, html }) {
  if (!isGmail(to)) {
    await logEmail({ userId, registrationId, eventId, to, type, subject, status: 'skipped', providerMessageId: null, errorMessage: 'Recipient is not a Gmail address' });
    return { status: 'skipped' };
  }

  try {
    const sent = await sendEmail({ to, subject, html });
    await logEmail({ userId, registrationId, eventId, to, type, subject, status: 'sent', providerMessageId: sent.id || null, errorMessage: null });
    return { status: 'sent', id: sent.id };
  } catch (error) {
    await logEmail({ userId, registrationId, eventId, to, type, subject, status: 'failed', providerMessageId: null, errorMessage: error.message });
    return { status: 'failed', error: error.message };
  }
}

async function sendConfirmation(registrationId) {
  const rows = await query(
    `SELECT r.id registration_id, r.status, u.id user_id, u.name, u.email, e.id event_id, e.title, e.event_date
     FROM registrations r
     JOIN users u ON u.id = r.user_id
     JOIN events e ON e.id = r.event_id
     WHERE r.id = :registrationId`,
    { registrationId }
  );
  if (!rows.length) throw new Error('Registration not found');
  const row = rows[0];

  const already = await query(
    `SELECT id FROM email_logs
     WHERE registration_id = :registrationId AND type = 'confirmation' AND status = 'sent'
     LIMIT 1`,
    { registrationId }
  );
  if (already.length) return { status: 'already_sent' };

  return sendAndLog({
    userId: row.user_id,
    registrationId: row.registration_id,
    eventId: row.event_id,
    to: row.email,
    type: 'confirmation',
    subject: `EventraZ registration: ${row.title}`,
    html: confirmationTemplate({ name: row.name, eventTitle: row.title, eventDate: row.event_date, status: row.status }),
  });
}

async function sendReminder(row) {
  return sendAndLog({
    userId: row.user_id,
    registrationId: row.registration_id,
    eventId: row.event_id,
    to: row.email,
    type: 'reminder',
    subject: `Reminder: ${row.title}`,
    html: reminderTemplate({ name: row.name, eventTitle: row.title, eventDate: row.event_date }),
  });
}

async function sendManualNotification({ userId, registrationId = null, eventId = null, to, subject, message }) {
  return sendAndLog({
    userId,
    registrationId,
    eventId,
    to,
    type: 'confirmation',
    subject,
    html: manualTemplate({ title: subject, message }),
  });
}

module.exports = { sendConfirmation, sendReminder, sendManualNotification, sendAndLog };
