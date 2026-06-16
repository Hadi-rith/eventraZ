const { google } = require('googleapis');

function getOAuthClient() {
  const client = new google.auth.OAuth2(
    process.env.GMAIL_CLIENT_ID,
    process.env.GMAIL_CLIENT_SECRET,
    process.env.GMAIL_REDIRECT_URI
  );

  client.setCredentials({ refresh_token: process.env.GMAIL_REFRESH_TOKEN });
  return client;
}

function encodeMessage({ to, subject, html }) {
  const from = process.env.GMAIL_FROM;
  const message = [
    `From: EventraZ <${from}>`,
    `To: ${to}`,
    `Subject: ${subject}`,
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    '',
    html,
  ].join('\n');

  return Buffer.from(message)
    .toString('base64')
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '');
}

async function sendEmail({ to, subject, html }) {
  const gmail = google.gmail({ version: 'v1', auth: getOAuthClient() });
  const raw = encodeMessage({ to, subject, html });
  const result = await gmail.users.messages.send({
    userId: 'me',
    requestBody: { raw },
  });

  return result.data;
}

module.exports = { sendEmail };
