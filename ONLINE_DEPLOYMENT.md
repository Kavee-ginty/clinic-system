# Online Deployment

## Supabase

1. Create a Supabase project.
2. Open SQL Editor and run `supabase/schema.sql`.
3. Enable Authentication > Providers > Google.
4. Add production redirect URLs:
   - `https://your-vercel-domain.vercel.app/auth/callback`
   - any custom domain callback you use later
5. Add approved users:

```sql
insert into public.user_roles (email, role) values
  ('admin@gmail.com', 'admin'),
  ('doctor@gmail.com', 'doctor'),
  ('reception@gmail.com', 'receptionist')
on conflict do nothing;
```

One email can have multiple roles by inserting multiple rows.

## Data Migration

Run `supabase/schema.sql` first. Then export local MySQL/XAMPP data and convert table/column names to the lower-case Supabase schema. Use `supabase/data-import-template.sql` after explicit-ID imports to reset identity sequences.

## Vercel

Set these environment variables:

```text
NEXT_PUBLIC_SUPABASE_URL
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY
SUPABASE_SECRET_KEY
```

Deploy with Vercel's Next.js preset. The PHP/XAMPP files are not used by the online app.

## Local Check

```bash
npm install
npm run build
```
