-- Supabase schema for FeedPerfeito

-- Users table (replaced by profiles and auth.users)
-- CREATE TABLE IF NOT EXISTS users (
--   id SERIAL PRIMARY KEY,
--   name VARCHAR(255) NOT NULL,
--   email VARCHAR(255) UNIQUE NOT NULL,
--   password VARCHAR(255) NOT NULL,
--   credits INTEGER DEFAULT 0,
--   created_at TIMESTAMP DEFAULT NOW(),
--   updated_at TIMESTAMP DEFAULT NOW()
-- );

-- Profiles table (complement to auth.users)
CREATE TABLE IF NOT EXISTS public.profiles (
  id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  
  -- Additional data
  name TEXT,
  avatar_url TEXT,         -- user photo
  phone TEXT,
  cpf TEXT,
  address TEXT,
  role VARCHAR(50) NOT NULL DEFAULT 'user',
  credits INTEGER NOT NULL DEFAULT 0,
  
  -- Audit
  created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

-- Trigger to update updated_at automatically
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS set_profiles_updated_at ON public.profiles;
CREATE TRIGGER set_profiles_updated_at
BEFORE UPDATE ON public.profiles
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

-- Function that automatically creates the profile
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO public.profiles (id, name, phone, role, credits)
  VALUES (
    NEW.id,
    NEW.raw_user_meta_data->>'name',
    NEW.raw_user_meta_data->>'phone',
    'user',
    0
  );
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Trigger on auth.users
DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
AFTER INSERT ON auth.users
FOR EACH ROW
EXECUTE FUNCTION public.handle_new_user();

-- User sessions table
CREATE TABLE IF NOT EXISTS user_sessions (
  id SERIAL PRIMARY KEY,
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
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
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
  type VARCHAR(20) NOT NULL, -- 'credit', 'debit', 'purchase', 'spend', 'bonus'
  amount DECIMAL(10, 2) NOT NULL,
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
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
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
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
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
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
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

-- Admin users table (replaced by profiles with role 'admin')
-- CREATE TABLE IF NOT EXISTS admin_users (
--   id SERIAL PRIMARY KEY,
--   name VARCHAR(255) NOT NULL,
--   email VARCHAR(255) UNIQUE NOT NULL,
--   password VARCHAR(255) NOT NULL,
--   role VARCHAR(50) NOT NULL, -- 'admin', 'editor', 'support'
--   is_active BOOLEAN DEFAULT true,
--   last_login TIMESTAMP,
--   created_at TIMESTAMP DEFAULT NOW(),
--   updated_at TIMESTAMP DEFAULT NOW()
-- );

-- Carousel products table
CREATE TABLE IF NOT EXISTS carousel_products (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  theme VARCHAR(255),
  category VARCHAR(100),
  type VARCHAR(50), -- e.g., 'Destaque', 'Novidade', 'Promocao'
  credits INTEGER NOT NULL,
  sold_quantity INTEGER DEFAULT 0,
  customization_types TEXT, -- Stores JSON array of customization options (e.g., '["arte", "cores", "imagem", "texto"]')
  description TEXT,
  page_count INTEGER,
  status VARCHAR(50) DEFAULT 'active', -- e.g., 'active', 'inactive', 'draft'
  unique_code VARCHAR(100) UNIQUE, -- Auto-generated
  images TEXT, -- Stores JSON array of image URLs (up to 10)
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Feed products table
CREATE TABLE IF NOT EXISTS feed_products (
  id SERIAL PRIMARY KEY, -- Usando UUID como PK
  name VARCHAR(255) NOT NULL,
  theme VARCHAR(255),
  category VARCHAR(100),
  type VARCHAR(50), -- e.g., 'Destaque', 'Novidade', 'Promocao'
  utilization VARCHAR(50), -- 'Feed', 'Stories', 'Capa'
  credits INTEGER NOT NULL,
  sold_quantity INTEGER DEFAULT 0,
  customization_types TEXT, -- Stores JSON array of customization options (e.g., '["arte", "cores", "imagem", "texto"]')
  description TEXT,
  page_count INTEGER,
  status VARCHAR(50) DEFAULT 'active', -- e.g., 'active', 'inactive', 'draft'
  unique_code VARCHAR(100) UNIQUE, -- Auto-generated
  images TEXT, -- Stores JSON array of image URLs (up to 10)
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);

-- Purchases table
CREATE TABLE IF NOT EXISTS purchases (
  id SERIAL PRIMARY KEY,
  user_id UUID REFERENCES public.profiles(id) ON DELETE CASCADE,
  product_id INTEGER, -- Refers to carousel_products.id
  product_name VARCHAR(255) NOT NULL,
  unique_code VARCHAR(100) NOT NULL,
  credits_used INTEGER NOT NULL,
  observacoes TEXT,
  customization_options JSONB, -- Stores JSON object of customization options
  status VARCHAR(50) DEFAULT 'pending', -- e.g., 'pending', 'completed', 'cancelled'
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);