# UniStock Frontend

## Structure
- `src/`: Source code
  - `pages/`: React components for each page
  - `assets/`: Static files and styles
  - `api.js`: Axios configuration for backend communication
  - `supabaseClient.js`: Supabase JS Client configuration

## Set up
1. Install dependencies:
   npm install

2. Configure environment variables in `.env`:
   VITE_API_URL=https://<your-backend-url>/api/
   VITE_SUPABASE_URL=<your-supabase-url>
   VITE_SUPABASE_ANON_KEY=<your-supabase-key>

3. Run the development server:
   npm run dev

4. Build for production:
   npm run build
