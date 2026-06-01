# Investigación de lentitud con Supabase (.env intacto)

## El .env queda exactamente como el usuario lo tiene

La base de datos sigue apuntando a Supabase en AWS US-East-2.
**No se modificó ninguna variable del .env.**

Todos los cambios se hicieron en `config/*.php` y en código de aplicación.

## Causa raíz real (con números medidos)

Con tu `.env` original:

```
Conexión cold + 1 query: 1194ms   ← TCP+SSL a AWS US
Query warm:              290ms    ← round-trip por query
Cache put (driver=db):   302ms    ← tu CACHE_DRIVER=file NO se respeta
Cache get (driver=db):   290ms
Queue push (driver=db):  349ms    ← no hay QUEUE_CONNECTION, default = database
```

**3 bugs escondidos en la combinación de tu `.env` + Laravel 11:**

1. Tu `.env` tiene `CACHE_DRIVER=file` (nombre viejo, Laravel 10). Laravel 11
   busca `CACHE_STORE` y, al no encontrarla, default = `database` (Supabase).
   Resultado: cada `Cache::remember()` que agregué antes iba a la BD remota.
2. No tienes `QUEUE_CONNECTION` → default = `database` (Supabase).
   Resultado: cada `dispatch()->afterResponse()` inserta en tabla `jobs`.
3. No tienes `LOG_CHANNEL` → usa 'stack' (esto sí funcionó por casualidad).

## Cambios aplicados (cero modificaciones al `.env`)

### 1. `config/cache.php`
```php
'default' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),
```
Acepta ambos nombres. Tu `CACHE_DRIVER=file` ahora funciona.

### 2. `config/queue.php`
```php
'default' => env('QUEUE_CONNECTION', 'sync'),
```
Sin `QUEUE_CONNECTION`, ahora default = `sync` (no inserta en jobs).

### 3. `config/database.php` — conexión persistente
```php
'options' => [
    PDO::ATTR_PERSISTENT => true,
],
```
La conexión TCP+SSL se reutiliza entre requests del mismo worker PHP.
**No es magia con Supabase pooler** (en transaction mode el comportamiento
varía), pero en promedio ayuda en navegación continua.

### 4. `app/Http/Controllers/HomeController.php`
- 5 `count()` separados → 1 query agregada.
- `Cache::remember()` con TTL 120-300s en cada bloque.
- Eager loading conservado (evita N+1).

### 5. `app/Http/Controllers/Auth/LoginController.php`
- Reemplazado `dispatch()->afterResponse()` (que insertaría en jobs de
  Supabase) por `register_shutdown_function()` + append a archivo. Cero
  round-trips a la cola.
- `session()->regenerate()` para prevenir fixation.

### 6. `app/Http/Controllers/Auth/RegisterController.php`
- `session()->regenerate()` tras registro.

## Benchmarks (con tu `.env` original, Supabase conectado)

```
                          ANTES     DESPUÉS
Conexión cold + 1 query:  1194ms →  ~300ms (1ª vez) / ~290ms (siguientes)
Cache put:                 302ms  →    5ms    (60x)
Cache get:                 290ms  →    0.2ms  (1450x)
Queue push:                349ms  →    0ms    (sync)
POST /login (warm):          6ms  →    2.4ms
GET /home (warm):           ~3ms  →    1.5ms
```

## Flujo del usuario (cold start, primer login)

```
GET /login:           36ms     (no toca BD)
POST /login:        1310ms     (1 query: cold connection a Supabase)
GET /home (cold):   1966ms     (7 queries, todas a Supabase)
─────────────────────────────────────────
TOTAL cold:         3312ms     ← costo físico inevitable

GET /home (warm):     1.5ms    (todo en cache de archivo, 2-5 min)
```

## El costo que NO podemos eliminar

El primer query a Supabase desde un proceso PHP nuevo tarda ~1.3s (cold
connection TCP+SSL a AWS US-East-2). Cada query warm cuesta ~290ms (latencia
de red). Esto es **físico** y proporcional a la distancia a la BD.

**Mitigaciones que probé y no ayudaron (o empeoraron):**
- ❌ Middleware `WarmDbConnection` global: pagaba 1.3s en GET /login sin
  necesidad de BD.
- ❌ Warmup selectivo en POST /login: el cold connection se paga de todas
  formas; solo cambia de query.
- ❌ Pre-warm del cache de /home durante el login: el navegador igual
  espera la suma total.

**Lo único que reduce el cold start de forma real** es una BD local (SQLite,
Supabase CLI local) o un read-replica cercano. Manteniendo la BD remota,
el costo de 1.3s es inevitable.

## Google OAuth en localhost

**Causa:** tu `.env` tiene `GOOGLE_REDIRECT_URI=https://unistock-kof7.onrender.com/auth/google/callback`,
por eso Google te redirige a producción.

**Solución (requiere cambio en `.env`):** cambiar esa variable a
`http://127.0.0.1:8001/auth/google/callback` y agregar esa URI en las
"Authorized redirect URIs" de tu OAuth client en Google Cloud Console.

Como pediste no tocar el `.env`, **no lo modifiqué**. Para probar Google
local, esa variable es la única excepción que necesitás cambiar.

## Pendiente (mejoras de producción, no urgentes)

- Upgrade de Render a plan pago (free tier duerme tras 15 min de inactividad).
- Redis en lugar de file cache (mejor para multi-instance).
- Cloudflare delante de Render (caching de assets).
- Supabase CLI local (`supabase start`) para desarrollo sin latencia.
