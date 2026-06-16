const schedule = require('node-schedule');
const { query } = require('./db');
const { sendReminder } = require('./mailService');

function isFridayOrSaturday(date) {
  const day = date.getDay();
  return day === 5 || day === 6;
}

function addDays(date, days) {
  const next = new Date(date);
  next.setDate(next.getDate() + days);
  return next;
}

function toDateOnly(date) {
  return date.toISOString().slice(0, 10);
}

function parseDateOnly(value) {
  if (value instanceof Date) return new Date(value.getFullYear(), value.getMonth(), value.getDate());
  const [year, month, day] = String(value).slice(0, 10).split('-').map(Number);
  return new Date(year, month - 1, day);
}

function adjustedReminderDate(eventDate) {
  let reminderDate = addDays(parseDateOnly(eventDate), -3);

  while (isFridayOrSaturday(reminderDate)) {
    reminderDate = addDays(reminderDate, 1);
  }

  return toDateOnly(reminderDate);
}

async function runReminderCheck() {
  const today = new Date();
  if (isFridayOrSaturday(today)) return { skipped: true, reason: 'No reminders on Friday or Saturday' };

  const todayOnly = toDateOnly(today);
  const rows = await query(
    `SELECT r.id registration_id, r.user_id, r.event_id, u.name, u.email, e.title, e.event_date
     FROM registrations r
     JOIN users u ON u.id = r.user_id
     JOIN events e ON e.id = r.event_id
     WHERE r.status = 'approved'
       AND e.event_date >= CURDATE()
       AND DATE_SUB(e.event_date, INTERVAL 3 DAY) <= CURDATE()
       AND NOT EXISTS (
         SELECT 1 FROM email_logs l
         WHERE l.registration_id = r.id AND l.event_id = e.id AND l.type = 'reminder' AND l.status = 'sent'
       )`,
    {}
  );

  const results = [];
  for (const row of rows) {
    if (adjustedReminderDate(row.event_date) !== todayOnly) continue;
    results.push({ registrationId: row.registration_id, email: await sendReminder(row) });
  }
  return { reminderDate: todayOnly, processed: results.length, results };
}

function startReminderScheduler() {
  schedule.scheduleJob('0 8 * * *', () => {
    runReminderCheck().catch((error) => {
      console.error('Reminder scheduler failed:', error);
    });
  });
}

module.exports = { startReminderScheduler, runReminderCheck, adjustedReminderDate };
