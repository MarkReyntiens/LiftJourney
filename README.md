# LiftJourney

Eerste stap van de app:
- `backend`: PHP API (zonder framework) met PostgreSQL
- `frontend`: React + TypeScript

## Architectuur

De backend gebruikt een eenvoudige clean architecture opdeling:
- `Domain`: entiteiten + repository interfaces
- `Application`: use cases
- `Infrastructure`: PDO implementaties
- `Http`: controllers + middleware

Zo houden we verantwoordelijkheden gescheiden (SOLID).

## Snel starten

1. Maak een PostgreSQL database aan (bijv. `lift_journey`)
2. Voer `backend/database/schema.sql` uit
3. Kopieer `backend/.env.example` naar `backend/.env` en vul je DB settings
4. Start backend:
   - `php -S localhost:8080 -t backend`
5. Start frontend:
   - `cd frontend`
   - `npm install`
   - `npm run dev`

Frontend gebruikt standaard `http://localhost:8080`.
Je kan dit overschrijven via `VITE_API_BASE_URL` in je frontend environment.

## Deploy op shared hosting

- `backend/index.php` staat in de root van de API (vereiste op jouw host)
- `backend/.htaccess` rewritet alle niet-bestaande paden naar `index.php`

## Test endpoint

- `GET /api/health`
- Checkt service + database connectie (`SELECT 1`)
- `200` bij volledig OK
- `503` als service draait maar database niet bereikbaar is
- `GET /api/health/live`
- Liveness endpoint zonder database-check (altijd `200` als API container draait)

## Meertalige API fouten

- Backend kiest taal via `X-Locale` header (`nl` of `en`)
- Zonder `X-Locale` gebruikt de API `Accept-Language`

## Deploy op Render

1. Push deze repository naar GitHub.
2. Maak in Render een nieuwe Blueprint met `render.yaml` in de repo-root.
3. Blueprint maakt 2 services:
   - `liftjourney-api` (PHP API, Docker)
   - `liftjourney-frontend` (React static site)
4. Vul de vereiste env vars in:

Voor `liftjourney-api`:
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_SSLMODE` (`require` voor Supabase)
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`

Voor `liftjourney-frontend`:
   - `VITE_API_BASE_URL` = URL van `liftjourney-api`

5. Deploy en controleer:
   - `https://<jouw-render-service>/api/health/live`
   - `https://<jouw-render-service>/api/health`

Belangrijk:
- `backend/Dockerfile` gebruikt Apache + PHP 8.3 + `pdo_pgsql`.
- Frontend moet wijzen naar je Render API URL via `VITE_API_BASE_URL`.

## Supabase tip

- Supabase werkt extern, maar gebruik op Render bij voorkeur de pooler (IPv4) connectie.
- Typische pooler instellingen:
  - `DB_HOST=<project>.pooler.supabase.com`
  - `DB_PORT=6543`
  - `DB_SSLMODE=require`


