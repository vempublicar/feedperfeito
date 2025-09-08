-- Supabase schema for FeedPerfeito

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  credits INTEGER DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- User sessions table
CREATE TABLE IF NOT EXISTS user_sessions (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  session_token VARCHAR(255) UNIQUE NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Credit packages table
CREATE TABLE IF NOT EXISTS credit_packages (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  credits INTEGER NOT NULL,
  bonus_credits INTEGER DEFAULT 0,
  price DECIMAL(10, 2) NOT NULL,
  tag VARCHAR(50),
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- User credit transactions table
CREATE TABLE IF NOT EXISTS credit_transactions (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  transaction_type VARCHAR(50) NOT NULL, -- 'purchase', 'spend', 'bonus'
  credits INTEGER NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Content templates table
CREATE TABLE IF NOT EXISTS content_templates (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  credits_required INTEGER NOT NULL,
  preview_url TEXT,
  thumbnail_url TEXT,
  is_featured BOOLEAN DEFAULT false,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- User orders table
CREATE TABLE IF NOT EXISTS user_orders (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  template_id INTEGER REFERENCES content_templates(id),
  status VARCHAR(50) NOT NULL, -- 'confirmed', 'in_production', 'in_approval', 'download_available', 'completed', 'cancelled'
  title VARCHAR(255) NOT NULL,
  description TEXT,
  credits_used INTEGER,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Order approval requests table
CREATE TABLE IF NOT EXISTS order_approvals (
  id SERIAL PRIMARY KEY,
  order_id INTEGER REFERENCES user_orders(id),
  feedback TEXT,
  is_approved BOOLEAN,
  approved_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  thumbnail_url TEXT,
  delivery_time VARCHAR(100),
  price_type VARCHAR(50) NOT NULL, -- 'BRL', 'CREDITS', 'QUOTE'
  price DECIMAL(10, 2),
  credits_required INTEGER,
  tag VARCHAR(50),
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Service requests table
CREATE TABLE IF NOT EXISTS service_requests (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  service_id INTEGER REFERENCES services(id),
  urgency VARCHAR(50), -- 'Normal', 'Rapid', 'Priority'
  objective TEXT,
  details TEXT,
  status VARCHAR(50) NOT NULL, -- 'pending', 'in_progress', 'completed', 'cancelled'
  credits_used INTEGER,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Promotions table
CREATE TABLE IF NOT EXISTS promotions (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  template_id INTEGER REFERENCES content_templates(id),
  original_credits INTEGER,
  discounted_credits INTEGER,
  preview_url TEXT,
  thumbnail_url TEXT,
  tag VARCHAR(50),
  expires_at TIMESTAMP,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Vouchers table
CREATE TABLE IF NOT EXISTS vouchers (
  id SERIAL PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL,
  credits INTEGER NOT NULL,
  is_used BOOLEAN DEFAULT false,
  user_id INTEGER REFERENCES users(id),
  used_at TIMESTAMP,
  expires_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT NOW()
);

-- User content files table
CREATE TABLE IF NOT EXISTS user_content_files (
  id SERIAL PRIMARY KEY,
  order_id INTEGER REFERENCES user_orders(id),
  file_url TEXT NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_type VARCHAR(50),
  is_watermarked BOOLEAN DEFAULT true,
  is_downloadable BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL, -- 'admin', 'editor', 'support'
  is_active BOOLEAN DEFAULT true,
  last_login TIMESTAMP,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);