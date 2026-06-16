require('dotenv').config();

const express = require('express');
const publicRoutes = require('./routes/public');
const schoolRoutes = require('./routes/school');
const adminRoutes = require('./routes/admin');
const { startReminderScheduler, runReminderCheck } = require('./scheduler');
const { authRequired, requireRole } = require('./auth');

const app = express();
app.use(express.json({ limit: '2mb' }));
app.use(express.urlencoded({ extended: true }));

app.get('/health', (req, res) => res.json({ ok: true, service: 'eventraz-registration-api' }));
app.use('/api/public', publicRoutes);
app.use('/api/school', schoolRoutes);
app.use('/api/admin', adminRoutes);

app.post('/api/admin/reminders/run-now', authRequired, requireRole('admin'), async (req, res, next) => {
  try {
    res.json(await runReminderCheck());
  } catch (error) {
    next(error);
  }
});

app.use((error, req, res, next) => {
  console.error(error);
  res.status(error.status || 500).json({ error: error.message || 'Internal server error' });
});

const port = Number(process.env.PORT || 3000);
if (require.main === module) {
  app.listen(port, () => {
    startReminderScheduler();
    console.log(`EventraZ API listening on port ${port}`);
  });
}

module.exports = app;
