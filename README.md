# User Nickname Generator - Backend Interju Feladat

Laravel 12 backend feladatmegoldas, ahol a rendszer percenkent minden felhasznalohoz uj becenevet general es elmenti.
A becenev alapertelmezetten a PokeAPI-bol erkezik, hiba vagy kikapcsolas eseten 8 karakteres random stringre valt fallback-kent.

## Funkcionalis osszefoglalo

- Userhez tobb nickname tarolhato (`users` 1:N `nicknames`).
- Utemezett job percenkent fut.
- Minden futasban minden felhasznalo uj nickname-et kap.
- API endpoint listazza az osszes usert a nickname-jeikkel.
- Seeder general tesztfelhasznalokat.

## Technologia

- PHP 8.2+
- Laravel 12
- SQLite
- Queue: database driver
- Scheduler: Laravel scheduler (`routes/console.php`)

## Projekt struktura (lenyeges fajlok)

- `app/Jobs/GenerateNicknameJob.php` - percenkenti nickname generalas minden userre
- `app/Services/NicknameGeneratorService.php` - PokeAPI + fallback logika
- `app/Models/User.php` - `nicknames()` kapcsolat
- `app/Models/Nickname.php` - `user()` kapcsolat
- `database/migrations/2026_02_25_135837_create_nicknames_table.php` - nicknames tabla
- `routes/console.php` - scheduler bejegyzes
- `routes/api.php` - `/api/users` endpoint
- `app/Http/Controllers/UserController.php` - user lista nickname-ekkel

## Kornyezeti valtozok

A `.env` fajlban:

- `POKEAPI_ENABLED=true` - ha `false`, mindig random 8 karakteres nickname keszul
- `POKEAPI_BASE_URL=https://pokeapi.co/api/v2` - Pokemon API alap URL
- `QUEUE_CONNECTION=database`
- `DB_CONNECTION=sqlite`

A `.env.example` mar tartalmazza a PokeAPI defaultokat.

## Telepites

### 1. Fuggosegek

```bash
composer install
```

### 2. Env letrehozas

Linux/macOS:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 3. App key

```bash
php artisan key:generate
```

### 4. SQLite adatbazis fajl

Linux/macOS:

```bash
touch database/database.sqlite
```

Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File -Force
```

### 5. Migration + seeding

```bash
php artisan migrate --seed
```

Seeder jelenleg 20 tesztfelhasznalot hoz letre.

## Inditas (lokalis)

Szukseges 3 folyamat:

1. HTTP szerver

```bash
php artisan serve
```

2. Queue worker (a queued jobok feldolgozasahoz)

```bash
php artisan queue:work --tries=1
```

3. Scheduler

Dev modban:

```bash
php artisan schedule:work
```

Alternativa: percenkent manualis trigger (pl. masik terminalbol):

```bash
php artisan schedule:run
```

## API

### GET `/api/users`

Visszaadja az osszes usert betoltott nickname-ekkel (`User::with('nicknames')`).

Pelda:

```json
[
  {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "nicknames": [
      {
        "id": 10,
        "user_id": 1,
        "nickname": "pikachu",
        "created_at": "2026-02-25T18:00:00.000000Z",
        "updated_at": "2026-02-25T18:00:00.000000Z"
      }
    ]
  }
]
```

## Mukodesi logika

1. A scheduler percenkent dispatch-eli a `GenerateNicknameJob`-ot.
2. A job chunkolva beolvassa a usereket.
3. Minden userhez ment egy uj nickname rekordot.
4. A nickname generalas:
   - ha `POKEAPI_ENABLED=true`, random Pokemon nevet probal lehuzni,
   - ha API hiba van vagy ervenytelen a valasz, fallback: random 8 karakter,
   - ha `POKEAPI_ENABLED=false`, kozvetlen random 8 karakter.
