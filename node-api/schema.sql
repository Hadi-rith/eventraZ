CREATE DATABASE IF NOT EXISTS eventraz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventraz;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('public', 'school', 'admin') NOT NULL DEFAULT 'public',
  phone VARCHAR(40) NULL,
  profile_json JSON NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_email (email),
  KEY idx_users_role (role),
  CONSTRAINT chk_users_gmail CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@gmail\\.com$')
) ENGINE=InnoDB;

CREATE TABLE schools (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  school_name VARCHAR(190) NOT NULL,
  school_code VARCHAR(60) NOT NULL,
  address TEXT NULL,
  district VARCHAR(120) NULL,
  contact_person VARCHAR(160) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_schools_user_id (user_id),
  UNIQUE KEY uq_schools_code (school_code),
  CONSTRAINT fk_schools_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE subcategories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_subcategories_category_name (category_id, name),
  CONSTRAINT fk_subcategories_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(190) NOT NULL,
  description TEXT NULL,
  category_id BIGINT UNSIGNED NULL,
  subcategory_id BIGINT UNSIGNED NULL,
  category VARCHAR(120) NOT NULL,
  subcategory VARCHAR(120) NULL,
  event_date DATE NOT NULL,
  location VARCHAR(190) NULL,
  capacity INT UNSIGNED NULL,
  status ENUM('draft', 'published', 'closed') NOT NULL DEFAULT 'published',
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_events_event_date (event_date),
  KEY idx_events_status_date (status, event_date),
  CONSTRAINT fk_events_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_events_subcategory FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE SET NULL,
  CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE participants (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  school_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  identity_no VARCHAR(80) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_participants_school_id (school_id),
  CONSTRAINT fk_participants_school FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
  CONSTRAINT chk_participants_gmail CHECK (email IS NULL OR email REGEXP '^[A-Za-z0-9._%+-]+@gmail\\.com$')
) ENGINE=InnoDB;

CREATE TABLE registrations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  event_id BIGINT UNSIGNED NOT NULL,
  participant_id BIGINT UNSIGNED NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  document_path VARCHAR(255) NULL,
  certificate_link VARCHAR(255) NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_registrations_user_event_participant (user_id, event_id, participant_id),
  KEY idx_registrations_event_status (event_id, status),
  KEY idx_registrations_user_status (user_id, status),
  CONSTRAINT fk_registrations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_registrations_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  CONSTRAINT fk_registrations_participant FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE email_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  registration_id BIGINT UNSIGNED NULL,
  event_id BIGINT UNSIGNED NULL,
  recipient_email VARCHAR(190) NOT NULL,
  type ENUM('confirmation', 'reminder') NOT NULL,
  subject VARCHAR(190) NOT NULL,
  sent_date DATETIME NOT NULL,
  status ENUM('sent', 'failed', 'skipped') NOT NULL,
  provider_message_id VARCHAR(255) NULL,
  error_message TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_email_logs_user_id (user_id),
  KEY idx_email_logs_registration_type (registration_id, type),
  KEY idx_email_logs_event_type (event_id, type),
  KEY idx_email_logs_sent_date (sent_date),
  KEY idx_email_logs_recipient_email (recipient_email),
  CONSTRAINT fk_email_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_email_logs_registration FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE SET NULL,
  CONSTRAINT fk_email_logs_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE user_sessions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  jwt_id VARCHAR(80) NOT NULL,
  ip_address VARCHAR(80) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  revoked_at DATETIME NULL,
  UNIQUE KEY uq_user_sessions_jwt_id (jwt_id),
  KEY idx_user_sessions_user_id (user_id),
  CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
