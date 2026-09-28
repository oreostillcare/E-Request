# E-Request

E-Request is a PHP student document-request portal backed exclusively by Supabase.

## Environment

Set these server-side variables locally and in Vercel:

- `SUPABASE_URL`
- `SUPABASE_PUBLISHABLE_KEY`
- `SUPABASE_SECRET_KEY`
- `SUPABASE_JWKS_URL`

Never commit `.env`; the application loads it only for local development. Production reads the same variables from Vercel.

## Database

The PostgreSQL schema and migrated seed records are in `supabase/migrations/20260929000000_initial_schema.sql`.
All application CRUD uses the Supabase Data REST API. Row Level Security is enabled and direct anonymous access is revoked; only the server-side secret key can access application and session data.

## Deployment

`vercel.json` sends PHP routes through one serverless function while static assets remain CDN-served. Sessions are stored in Supabase so authentication persists across serverless invocations.

