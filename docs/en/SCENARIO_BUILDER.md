# Scenario Builder Guide

## 🎨 Visual Scenario Builder

The Scenario Builder is an interactive interface to create YAML scenarios without writing code.

**NEW:** 🔄 Bidirectional Editing - Edit YAML directly and sync back to visual builder!

## 🚀 Quick Start

1. Access: `http://localhost:8000/prism/create`
2. Or click "✨ Create New Scenario" from the main list
3. Build your scenario step-by-step OR edit YAML directly
4. Save to `prism/` directory

---

## 🔄 Bidirectional Workflow

The builder now supports **two-way synchronization** between visual form and YAML:

### Visual Builder → YAML
1. Fill in the visual form (imports, variables, load instructions, etc.)
2. Click "📝 YAML" floating button (bottom-right)
3. Click "🔄 Builder → YAML" to generate YAML from your form
4. YAML appears in the editor

### YAML → Visual Builder
1. Click "📝 YAML" floating button
2. Edit YAML directly in the textarea
3. Click "⬅️ YAML → Builder" to sync changes back to the form
4. Form is updated with your YAML content

**Use cases:**
- **Quick edits**: Prefer editing YAML directly for simple changes
- **Learning**: See how visual elements translate to YAML
- **Copy/Paste**: Import YAML from existing scenarios
- **Power users**: Write YAML faster than clicking through forms
- **Beginners**: Use visual builder, verify with YAML preview

**Tips:**
- The YAML editor supports full editing (multi-line, copy/paste, etc.)
- Changes are NOT auto-synced - you must click "YAML → Builder" to apply
- A confirmation dialog prevents accidental overwrites
- After applying YAML, the builder regenerates the YAML to confirm parsing

---

## 📋 Sections

### 1. Scenario Name

The name of your scenario file (without `.yaml` extension).

```
Example: my_test_users
Will create: prism/my_test_users.yaml
```

### 2. Imports (Optional)

Reuse existing scenarios as building blocks.

**Use case:** Build complex scenarios from simple modules.

```yaml
import:
  - base_users
  - base_acl
```

**How to:**
1. Click "+ Add Import"
2. Select from dropdown or type scenario name
3. Path is relative to `prism/` directory

**Example:**
- Import `prism/base_users.yaml` → Type `base_users`
- Import `prism/includes/users.yaml` → Type `includes/users`

### 3. Variables (Optional)

Define reusable values used throughout the scenario.

**Use case:** Avoid repeating same values, centralize configuration.

```yaml
vars:
  admin: "admin_{{ scope }}"
  email_domain: "example.test"
  api_key: "{{ env('API_KEY') }}"
```

**How to:**
1. Click "+ Add Variable"
2. **Name**: Variable name (without `$`)
3. **Value**: Can use placeholders

**Usage in data:**
- Declare: `admin: "admin_{{ scope }}"`
- Use: `username: "{{ $admin }}"`

**Supported placeholders in values:**
- `{{ scope }}` - Current scope
- `{{ uuid }}` - Generate UUID
- `{{ hash('password') }}` - Hash password
- `{{ now }}` - Current timestamp
- `{{ date('+7 days') }}` - Relative date
- `{{ env('VAR') }}` - Environment variable
- `{{ math(10*5) }}` - Math expression

### 4. Load Instructions (Required)

Define data to insert into database tables.

**Minimum:** At least one load instruction is required.

#### 4.1. Table Name

Database table to insert into.

```
Example: users, chat_messages, orders
```

#### 4.2. Database (Optional)

Target database name for multi-database setups.

```
Example: hexagonal_secondary
Default: Main database connection
```

**Use case:** Insert data into a different database than the default one.

```yaml
- table: audit_logs
  db: hexagonal_secondary
  data:
    action: "user_login"
```

#### 4.3. Data Fields

Target database name for multi-database setups.

```
Example: hexagonal_secondary
Default: Main database connection
```

**Use case:** Insert data into a different database than the default one.

```yaml
- table: audit_logs
  db: hexagonal_secondary
  data:
    action: "user_login"
```

#### 4.3. Data Fields

Two ways to define data:

##### Option A: JSON Format (Textarea)

Direct JSON entry for advanced users:

```json
{
  "username": "user_{{ scope }}",
  "email": "user@test.com",
  "password": "{{ hash('secret') }}"
}
```

##### Option B: Field Builder (Recommended)

Click "+ Add Field" for guided interface:

**Simple Value:**
```
Column Name: username
Value: user_{{ scope }}
```

**Lookup (FK Resolution):**
```
Column Name: user_id
Type: Lookup (FK)
Lookup Table: users
Lookup Database (Optional): hexagonal_secondary
Where Column: username
Where Value: admin_{{ scope }}
Return Column: id
```

Generates:
```yaml
user_id:
  table: users
  db: hexagonal_secondary
  where:
    username: "admin_{{ scope }}"
  return: id
```

#### 4.4. Types (Optional)

Convert values to specific PHP types.

```json
{
  "created_at": "datetime_immutable",
  "age": "int",
  "price": "float",
  "is_active": "bool"
}
```

**Available types:**
- `datetime_immutable` - DateTimeImmutable
- `datetime` - DateTime
- `int` - Integer
- `float` - Float/Decimal
- `bool` - Boolean
- `string` - String (default)

#### 4.5. Pivot Custom (Optional)

Track resources by a column other than `id`.

**Use case:** Tables with VARCHAR id but need to track by INT FK.

```json
{
  "id": 42,
  "column": "user_id"
}
```

Or with lookup:
```json
{
  "id": {
    "table": "users",
    "where": {"username": "admin_{{ scope }}"},
    "return": "id"
  },
  "column": "user_id"
}
```

### 5. Purge Instructions (Optional)

Custom cleanup logic executed before auto-purge.

**Use case:** Delete data created outside scenario tracking.

```yaml
purge:
  - table: posts
    db: hexagonal_secondary
    where:
      author: "user_{{ scope }}"
```

**How to:**
1. Click "+ Add Purge Instruction"
2. **Table**: Table to purge from
3. **Database (Optional)**: Target database name
4. **Where**: Conditions (JSON format)

**Example:**
```json
{
  "username": "admin_{{ scope }}",
  "status": "test"
}
```

---

## 👁️ Preview

Click **"🔄 Refresh Preview"** to see the generated YAML.

The preview shows exactly what will be saved to the file.

---

## 💾 Save

Click **"💾 Save Scenario"** to:
1. Validate the scenario
2. Generate YAML file
3. Save to `prism/{name}.yaml`
4. Redirect to scenario list

---

## 🎯 Complete Example

### Goal
Create 2 users and assign them ACL permissions.

### Steps

**1. Scenario Name:** `team_users`

**2. Variables:**
```
admin = "admin_{{ scope }}"
manager = "manager_{{ scope }}"
```

**3. Load Instruction #1 - Admin User:**
- Table: `users`
- Fields:
  - `username`: `{{ $admin }}`
  - `email`: `{{ $admin }}@test.com`
  - `password`: `{{ hash('admin123') }}`
  - `created_at`: `{{ now }}`
- Types:
  ```json
  {"created_at": "datetime_immutable"}
  ```

**4. Load Instruction #2 - Manager User:**
- Table: `users`
- Fields:
  - `username`: `{{ $manager }}`
  - `email`: `{{ $manager }}@test.com`
  - `password`: `{{ hash('manager123') }}`
  - `created_at`: `{{ now }}`
- Types:
  ```json
  {"created_at": "datetime_immutable"}
  ```

**5. Load Instruction #3 - Admin ACL:**
- Table: `users_acl`
- Fields:
  - `user_id`: **Lookup**
    - Table: `users`
    - Where Column: `username`
    - Where Value: `{{ $admin }}`
    - Return: `id`
  - `acl_id`: `1` (assuming ACL with id=1 exists)

**6. Save**

### Generated YAML

```yaml
# Scenario: team_users
#
# Created by PrismOffice on 2025-12-21 14:30:00
#
# Usage:
#   php bin/console app:prism:load team_users --scope=YOUR_SCOPE
#   php bin/console app:prism:purge team_users --scope=YOUR_SCOPE

vars:
  admin: "admin_{{ scope }}"
  manager: "manager_{{ scope }}"

load:
  - table: users
    data:
      username: "{{ $admin }}"
      email: "{{ $admin }}@test.com"
      password: "{{ hash('admin123') }}"
      created_at: "{{ now }}"
    types:
      created_at: datetime_immutable

  - table: users
    data:
      username: "{{ $manager }}"
      email: "{{ $manager }}@test.com"
      password: "{{ hash('manager123') }}"
      created_at: "{{ now }}"
    types:
      created_at: datetime_immutable

  - table: users_acl
    data:
      user_id:
        table: users
        where:
          username: "{{ $admin }}"
        return: id
      acl_id: "1"
```

---

## 💡 Tips

### Tip 1: Start Simple
Build incrementally:
1. Create minimal scenario with 1 table
2. Test with `php bin/console app:prism:load`
3. Add more complexity

### Tip 2: Use Imports
Create reusable modules:
- `prism/base_users.yaml` - Common users
- `prism/base_acl.yaml` - ACL structure
- Import both in your scenario

### Tip 3: Variables for DRY
Instead of repeating values:
```yaml
# ❌ Bad
data:
  username: "admin_{{ scope }}"
  email: "admin_{{ scope }}@test.com"

# ✅ Good
vars:
  admin: "admin_{{ scope }}"
data:
  username: "{{ $admin }}"
  email: "{{ $admin }}@test.com"
```

### Tip 4: Lookup for FK
Always use lookups for foreign keys:
```yaml
# ❌ Bad (hardcoded ID)
user_id: 1

# ✅ Good (dynamic lookup)
user_id:
  table: users
  where:
    username: "admin_{{ scope }}"
  return: id
```

### Tip 5: Use Fake Data
Generate realistic test data (55 types available):
```yaml
data:
  # Identity
  username: "{{ fake(user) }}"
  email: "{{ fake(email) }}"
  firstname: "{{ fake(firstname) }}"      # Rick, Morty, Linus...
  lastname: "{{ fake(lastname) }}"        # Sanchez, Torvalds...
  fullname: "{{ fake(fullname) }}"        # Rick Sanchez, Linus Torvalds...
  company: "{{ fake(company) }}"          # Aperture Science, Arasaka...
  gender: "{{ fake(gender) }}"            # male, female, other, non-binary
  age: "{{ fake(age) }}"                  # 18-99
  country: "{{ fake(country) }}"          # France, Germany, Japan...
  
  # Addresses (FR par défaut, support multi-pays)
  postcode: "{{ fake(postcode) }}"        # 75001, 69002... (France)
  postcode_us: "{{ fake(postcode, 'US') }}" # 90210 (US ZIP)
  postcode_uk: "{{ fake(postcode, 'GB') }}" # SW1A 1AA (UK)
  street: "{{ fake(street) }}"            # 42 Rue Victor Hugo (France)
  street_us: "{{ fake(street, 'US') }}"   # 123 Main Street
  city: "{{ fake(city) }}"                # Paris, Lyon, Marseille...
  city_de: "{{ fake(city, 'DE') }}"       # Berlin, München...
  address: "{{ fake(address) }}"          # 42 Rue Victor Hugo, 75001 Paris (sans pays)
  address_it: "{{ fake(address, 'IT') }}" # 7 Via Roma, 00100 Roma (sans pays)
  fulladdress: "{{ fake(fulladdress) }}"  # 42 Rue Victor Hugo, 75001 Paris, France (avec pays)
  fulladdress_gb: "{{ fake(fulladdress, 'GB') }}" # 15 High Street, SW1A 1AA London, United Kingdom
  
  # Codes & Finance
  isbn: "{{ fake(isbn) }}"                # 978-2-123-45678-9 (checksum)
  ean: "{{ fake(ean13) }}"                # 1234567890128 (checksum)
  vin: "{{ fake(vin) }}"                  # 1HGBH41JXMN109186
  ssn: "{{ fake(ssn) }}"                  # 123-45-6789 (US)
  nir: "{{ fake(nir) }}"                  # 1 89 12 75 123 456 89 (FR)
  iban: "{{ fake(iban, 'FR') }}"          # FR76 12345... (FR/DE/GB/ES/IT)
  siren: "{{ fake(siren) }}"              # 123456782 (Luhn)
  siret: "{{ fake(siret) }}"              # 12345678212345
  
  # Crypto
  btc_address: "{{ fake(crypto, 'btc') }}" # Bitcoin
  eth_address: "{{ fake(crypto, 'eth') }}" # Ethereum
  sol_address: "{{ fake(crypto, 'sol') }}" # Solana
  
  # Network
  phone: "{{ fake(tel, '+33') }}"
  ip: "{{ fake(ip) }}"
  ipv6: "{{ fake(ipv6) }}"
  mac: "{{ fake(mac) }}"
  url: "{{ fake(url) }}"
  user_agent: "{{ fake(useragent) }}"
  
  # Location
  gps: "{{ fake(gps) }}"                  # 48.856614, 2.352222
  latitude: "{{ fake(latitude) }}"
  longitude: "{{ fake(longitude) }}"
  country_code: "{{ fake(iso) }}"         # FR, US, DE...
  country_code3: "{{ fake(iso, 'alpha3') }}" # FRA, USA, DEU...
  
  # Dates & Timestamps
  random_date: "{{ fake(date) }}"         # 2015-03-21 (2000-2038)
  date_range: "{{ fake(date, 'Y-m-d', '2020-01-01', '2025-12-31') }}" # 2023-07-15
  datetime: "{{ fake(datetime) }}"        # 2015-03-21 14:32:18
  datetime_range: "{{ fake(datetime, 'Y-m-d H:i:s', '2024-01-01', '2024-12-31') }}" # 2024-06-15 09:23:45
  timestamp: "{{ fake(timestamp) }}"      # 1710334800 (int, 2000-2038)
  timestamp_range: "{{ fake(timestamp, '2024-01-01', '2024-12-31') }}" # 1710334800
  microtime: "{{ fake(microtime) }}"      # 1710334800.123456 (float)
  microtime_range: "{{ fake(microtime, '2024-01-01', '2024-12-31') }}" # 1710334800.987654
  
  # Text & Files
  slug: "{{ fake(slug) }}"                # rick-arasaka-742
  bio: "{{ fake(text, 200) }}"
  mime_type: "{{ fake(mime) }}"           # application/pdf, image/png...
  encoding: "{{ fake(charset) }}"         # UTF-8, ISO-8859-1...
  device: "{{ fake(device) }}"            # iPhone, Galaxy S23, MacBook...
  device_icon: "{{ fake(device, 'symbol') }}" # 📱, 💻, ⌚...
  full_device: "{{ fake(fulldevice) }}"   # Apple iPhone 15 Pro Max, Samsung Galaxy S24 Ultra...
  
  # Currencies
  currency: "{{ fake(currency) }}"         # EUR, USD, GBP, JPY... (aléatoire)
  currency_symbol: "{{ fake(currency, 'symbol') }}" # €, $, £, ¥... (aléatoire)
  currency_iso: "{{ fake(currency, 'eur') }}"  # EUR (code devise)
  currency_country: "{{ fake(currency, 'fr') }}"  # EUR (via code pays)
  currency_euro: "{{ fake(currency, 'eur', 'symbol') }}" # € (symbole devise)
  currency_us_symbol: "{{ fake(currency, 'us', 'symbol') }}" # $ (symbole via pays)
  currency_full: "{{ fake(fullcurrency) }}" # Euro, US Dollar... (aléatoire)
  currency_name: "{{ fake(fullcurrency, 'eur') }}" # Euro (code devise)
  currency_from_country: "{{ fake(fullcurrency, 'us') }}" # US Dollar (via code pays)
  
  # Colors
  color_name: "{{ fake(color) }}"         # aliceblue, crimson...
  color_hex: "{{ fake(hexcolor) }}"       # #A3F2B8
  color_rgb: "{{ fake(rgb) }}"            # rgb(255, 128, 0)
  color_rgba: "{{ fake(rgba) }}"          # rgba(255, 128, 0, 0.75)
  
  # JSON structures - TOUS les 57 types de faker supportés !
  simple_json: "{{ fake(json, 'id:int, name:string, active:boolean') }}"
  # {"id": 42, "name": "lorem", "active": true}
  
  nested_json: "{{ fake(json, 'user:{id:int, profile:{name:string, age:int}}') }}"
  # {"user": {"id": 42, "profile": {"name": "lorem", "age": 25}}}
  
  array_json: "{{ fake(json, 'int, int, int') }}"
  # [42, 57, 89]
  
  # Avec paramètres (syntaxe: type:param1:param2)
  user_complete: "{{ fake(json, 'user:user, fullname:fullname, age:age, phone:tel:+33') }}"
  # {"user": "user_a3f2b8", "fullname": "Rick Sanchez", "age": 42, "phone": "+33123456789"}
  
  product_data: "{{ fake(json, 'sku:ean13, color:hexcolor, price:float, stock:number:0:1000, description:text:200') }}"
  # {"sku": "1234567890128", "color": "#A3F2B8", "price": 234.56, "stock": 742, "description": "lorem ipsum..."}
  
  location_info: "{{ fake(json, 'address:address, city:city, fulladdress:fulladdress, country:country') }}"
  # {"address": "42 Rue Victor Hugo, 75001 Paris", "city": "Paris", "fulladdress": "..., France", "country": "France"}
  
  finance_data: "{{ fake(json, 'iban:iban:FR, siren:siren, siret:siret') }}"
  # {"iban": "FR76 12345...", "siren": "123456782", "siret": "12345678212345"}
  
  crypto_wallets: "{{ fake(json, 'btc:crypto:btc, eth:crypto:eth, sol:crypto:sol') }}"
  # {"btc": "1A1zP1eP5QGefi...", "eth": "0x742d35Cc663...", "sol": "7dHbWXmci3dT8U..."}
  
  # Serialize structures (même syntaxe que JSON !)
  simple_serialize: "{{ fake(serialize, 'id:int, name:string, active:boolean') }}"
  # a:3:{s:2:"id";i:42;s:4:"name";s:5:"lorem";s:6:"active";b:1;}
  
  user_settings: "{{ fake(serialize, 'theme:string, lang:string, notifications:boolean, data:{font:string, size:int}') }}"
  # a:4:{s:5:"theme";s:5:"lorem";s:4:"lang";s:5:"ipsum";s:13:"notifications";b:1;s:4:"data";a:2:{...}}
  
  preferences: "{{ fake(serialize, 'colors:hexcolor, timezone:string, currency:string') }}"
  # a:3:{s:6:"colors";s:7:"#A3F2B8";s:8:"timezone";s:5:"lorem";s:8:"currency";s:5:"ipsum";}
