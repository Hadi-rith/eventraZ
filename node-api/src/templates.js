function baseLayout(title, body) {
  return `
    <div style="font-family:Arial,sans-serif;background:#fff8df;padding:28px;color:#231f20">
      <div style="max-width:640px;margin:auto;background:#ffffff;border-radius:24px;padding:28px;border:1px solid #f3d98a">
        <h1 style="margin:0 0 12px;color:#8a0028">EventraZ</h1>
        <h2 style="margin:0 0 18px;color:#520018">${title}</h2>
        ${body}
        <p style="font-size:12px;color:#777;margin-top:28px">Event Tracking, Registration & Engagement Zone</p>
      </div>
    </div>`;
}

function confirmationTemplate({ name, eventTitle, eventDate, status }) {
  return baseLayout(
    'Registration Confirmation',
    `<p>Hello ${name},</p>
     <p>Your registration for <strong>${eventTitle}</strong> has been received.</p>
     <p><strong>Date:</strong> ${eventDate}</p>
     <p><strong>Status:</strong> ${status}</p>`
  );
}

function reminderTemplate({ name, eventTitle, eventDate }) {
  return baseLayout(
    'Event Reminder',
    `<p>Hello ${name},</p>
     <p>This is a reminder that <strong>${eventTitle}</strong> is coming soon.</p>
     <p><strong>Event date:</strong> ${eventDate}</p>
     <p>Please make sure your documents and attendance details are ready.</p>`
  );
}

function manualTemplate({ title, message }) {
  return baseLayout(title, `<p>${message}</p>`);
}

module.exports = { confirmationTemplate, reminderTemplate, manualTemplate };
