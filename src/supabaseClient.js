import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL || 'https://tu_proyecto.supabase.co';
const supabaseKey = import.meta.env.VITE_SUPABASE_ANON_KEY || 'tu_anon_key';

export const supabase = createClient(supabaseUrl, supabaseKey);
