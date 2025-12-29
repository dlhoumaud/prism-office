# Guide du Constructeur de Scénarios

## 🎨 Constructeur de Scénarios Visuel

Le Constructeur de Scénarios est une interface interactive pour créer des scénarios YAML sans écrire de code.

**NOUVEAU :** 🔄 Édition Bidirectionnelle - Éditez directement le YAML et synchronisez avec le constructeur visuel !

## 🚀 Démarrage Rapide

1. Accédez à : `http://localhost:8000/prism/create`
2. Ou cliquez sur "✨ Créer un Nouveau Scénario" depuis la liste principale
3. Construisez votre scénario étape par étape OU éditez directement le YAML
4. Sauvegardez dans le répertoire `prism/`

---

## 🔄 Flux de Travail Bidirectionnel

Le constructeur supporte maintenant la **synchronisation bidirectionnelle** entre le formulaire visuel et le YAML :

### Constructeur Visuel → YAML
1. Remplissez le formulaire visuel (imports, variables, instructions de chargement, etc.)
2. Cliquez sur le bouton flottant "📝 YAML" (en bas à droite)
3. Cliquez sur "🔄 Constructeur → YAML" pour générer le YAML depuis votre formulaire
4. Le YAML apparaît dans l'éditeur

### YAML → Constructeur Visuel
1. Cliquez sur le bouton flottant "📝 YAML"
2. Éditez directement le YAML dans la zone de texte
3. Cliquez sur "⬅️ YAML → Constructeur" pour synchroniser les changements vers le formulaire
4. Le formulaire est mis à jour avec le contenu de votre YAML

**Cas d'usage :**
- **Modifications rapides** : Préférez l'édition directe du YAML pour les changements simples
- **Apprentissage** : Voyez comment les éléments visuels se traduisent en YAML
- **Copier/Coller** : Importez du YAML depuis des scénarios existants
- **Utilisateurs avancés** : Écrivez le YAML plus rapidement qu'en cliquant dans les formulaires
- **Débutants** : Utilisez le constructeur visuel, vérifiez avec la prévisualisation YAML

**Conseils :**
- L'éditeur YAML supporte l'édition complète (multi-lignes, copier/coller, etc.)
- Les changements ne sont PAS synchronisés automatiquement - vous devez cliquer sur "YAML → Constructeur" pour les appliquer
- Une boîte de dialogue de confirmation empêche les écrasements accidentels
- Après avoir appliqué le YAML, le constructeur régénère le YAML pour confirmer l'analyse

---

## 📋 Sections

### 1. Nom du Scénario

Le nom de votre fichier de scénario (sans l'extension `.yaml`).

```
Exemple : my_test_users
Va créer : prism/my_test_users.yaml
```

### 2. Imports (Optionnel)

Réutilisez des scénarios existants comme blocs de construction.

**Cas d'usage :** Construire des scénarios complexes à partir de modules simples.

```yaml
import:
  - base_users
  - base_acl
```

**Comment faire :**
1. Cliquez sur "+ Ajouter Import"
2. Sélectionnez depuis la liste déroulante ou tapez le nom du scénario
3. Le chemin est relatif au répertoire `prism/`

**Exemple :**
- Importer `prism/base_users.yaml` → Tapez `base_users`
- Importer `prism/includes/users.yaml` → Tapez `includes/users`

### 3. Variables (Optionnel)

Définissez des valeurs réutilisables utilisées dans tout le scénario.

**Cas d'usage :** Éviter de répéter les mêmes valeurs, centraliser la configuration.

```yaml
vars:
  admin: "admin_{{ scope }}"
  email_domain: "example.test"
  api_key: "{{ env('API_KEY') }}"
```

**Comment faire :**
1. Cliquez sur "+ Ajouter Variable"
2. **Nom** : Nom de la variable (sans `$`)
3. **Valeur** : Peut utiliser des placeholders

**Utilisation dans les données :**
- Déclaration : `admin: "admin_{{ scope }}"`
- Utilisation : `username: "{{ $admin }}"`

**Placeholders supportés dans les valeurs :**
- `{{ scope }}` - Scope actuel
- `{{ uuid }}` - Générer un UUID
- `{{ hash('password') }}` - Hasher un mot de passe
- `{{ now }}` - Timestamp actuel
- `{{ date('+7 days') }}` - Date relative
- `{{ env('VAR') }}` - Variable d'environnement
- `{{ math(10*5) }}` - Expression mathématique

### 4. Instructions de Chargement (Requis)

Définissez les données à insérer dans les tables de base de données.

**Minimum :** Au moins une instruction de chargement est requise.

#### 4.1. Nom de Table

Table de base de données dans laquelle insérer.

```
Exemple : users, chat_messages, orders
```

#### 4.2. Base de Données (Optionnel)

Nom de la base de données cible pour les configurations multi-bases.

```
Exemple : hexagonal_secondary
Par défaut : Connexion de base de données principale
```

**Cas d'usage :** Insérer des données dans une base de données différente de celle par défaut.

```yaml
- table: audit_logs
  db: hexagonal_secondary
  data:
    action: "user_login"
```

#### 4.3. Champs de Données

Deux façons de définir les données :

##### Option A : Format JSON (Zone de Texte)

Saisie JSON directe pour les utilisateurs avancés :

```json
{
  "username": "user_{{ scope }}",
  "email": "user@test.com",
  "password": "{{ hash('secret') }}"
}
```

##### Option B : Constructeur de Champs (Recommandé)

Cliquez sur "+ Ajouter Champ" pour une interface guidée :

**Valeur Simple :**
```
Nom de Colonne : username
Valeur : user_{{ scope }}
```

**Lookup (Résolution FK) :**
```
Nom de Colonne : user_id
Type : Lookup (FK)
Table de Lookup : users
Base de Données de Lookup (Optionnel) : hexagonal_secondary
Colonne Where : username
Valeur Where : admin_{{ scope }}
Colonne de Retour : id
```

Génère :
```yaml
user_id:
  table: users
  db: hexagonal_secondary
  where:
    username: "admin_{{ scope }}"
  return: id
```

#### 4.4. Types (Optionnel)

Convertir les valeurs en types PHP spécifiques.

```json
{
  "created_at": "datetime_immutable",
  "age": "int",
  "price": "float",
  "is_active": "bool"
}
```

**Types disponibles :**
- `datetime_immutable` - DateTimeImmutable
- `datetime` - DateTime
- `int` - Integer
- `float` - Float/Decimal
- `bool` - Boolean
- `string` - String (par défaut)

#### 4.5. Pivot Custom (Optionnel)

Suivre les ressources par une colonne autre que `id`.

**Cas d'usage :** Tables avec id VARCHAR mais besoin de suivre par FK INT.

```json
{
  "id": 42,
  "column": "user_id"
}
```

Ou avec lookup :
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

### 5. Instructions de Purge (Optionnel)

Logique de nettoyage personnalisée exécutée avant la purge automatique.

**Cas d'usage :** Supprimer les données créées en dehors du suivi du scénario.

```yaml
purge:
  - table: posts
    db: hexagonal_secondary
    where:
      author: "user_{{ scope }}"
```

**Comment faire :**
1. Cliquez sur "+ Ajouter Instruction de Purge"
2. **Table** : Table à purger
3. **Base de Données (Optionnel)** : Nom de la base de données cible
4. **Where** : Conditions (format JSON)

**Exemple :**
```json
{
  "username": "admin_{{ scope }}",
  "status": "test"
}
```

---

## 👁️ Prévisualisation

Cliquez sur **"🔄 Actualiser Prévisualisation"** pour voir le YAML généré.

La prévisualisation montre exactement ce qui sera sauvegardé dans le fichier.

---

## 💾 Sauvegarde

Cliquez sur **"💾 Sauvegarder Scénario"** pour :
1. Valider le scénario
2. Générer le fichier YAML
3. Sauvegarder dans `prism/{nom}.yaml`
4. Rediriger vers la liste des scénarios

---

## 🎯 Exemple Complet

### Objectif
Créer 2 utilisateurs et leur assigner des permissions ACL.

### Étapes

**1. Nom du Scénario :** `team_users`

**2. Variables :**
```
admin = "admin_{{ scope }}"
manager = "manager_{{ scope }}"
```

**3. Instruction de Chargement #1 - Utilisateur Admin :**
- Table : `users`
- Champs :
  - `username` : `{{ $admin }}`
  - `email` : `{{ $admin }}@test.com`
  - `password` : `{{ hash('admin123') }}`
  - `created_at` : `{{ now }}`
- Types :
  ```json
  {"created_at": "datetime_immutable"}
  ```

**4. Instruction de Chargement #2 - Utilisateur Manager :**
- Table : `users`
- Champs :
  - `username` : `{{ $manager }}`
  - `email` : `{{ $manager }}@test.com`
  - `password` : `{{ hash('manager123') }}`
  - `created_at` : `{{ now }}`
- Types :
  ```json
  {"created_at": "datetime_immutable"}
  ```

**5. Instruction de Chargement #3 - ACL Admin :**
- Table : `users_acl`
- Champs :
  - `user_id` : **Lookup**
    - Table : `users`
    - Colonne Where : `username`
    - Valeur Where : `{{ $admin }}`
    - Retour : `id`
  - `acl_id` : `1` (en supposant qu'un ACL avec id=1 existe)

**6. Sauvegarder**

### YAML Généré

```yaml
# Scénario : team_users
#
# Créé par PrismOffice le 2025-12-21 14:30:00
#
# Utilisation :
#   php bin/console app:prism:load team_users --scope=VOTRE_SCOPE
#   php bin/console app:prism:purge team_users --scope=VOTRE_SCOPE

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

## 💡 Conseils

### Conseil 1 : Commencez Simple
Construisez de manière incrémentale :
1. Créez un scénario minimal avec 1 table
2. Testez avec `php bin/console app:prism:load`
3. Ajoutez plus de complexité

### Conseil 2 : Utilisez les Imports
Créez des modules réutilisables :
- `prism/base_users.yaml` - Utilisateurs communs
- `prism/base_acl.yaml` - Structure ACL
- Importez les deux dans votre scénario

### Conseil 3 : Variables pour DRY
Au lieu de répéter les valeurs :
```yaml
# ❌ Mauvais
data:
  username: "admin_{{ scope }}"
  email: "admin_{{ scope }}@test.com"

# ✅ Bon
vars:
  admin: "admin_{{ scope }}"
data:
  username: "{{ $admin }}"
  email: "{{ $admin }}@test.com"
```

### Conseil 4 : Lookup pour les FK
Utilisez toujours des lookups pour les clés étrangères :
```yaml
# ❌ Mauvais (ID en dur)
user_id: 1

# ✅ Bon (lookup dynamique)
user_id:
  table: users
  where:
    username: "admin_{{ scope }}"
  return: id
```

### Conseil 5 : Utilisez les Données Factices
Générez des données de test réalistes (55 types disponibles) :
```yaml
data:
  # Identité
  username: "{{ fake(user) }}"
  email: "{{ fake(email) }}"
  firstname: "{{ fake(firstname) }}"      # Rick, Morty, Linus...
  lastname: "{{ fake(lastname) }}"        # Sanchez, Torvalds...
  fullname: "{{ fake(fullname) }}"        # Rick Sanchez, Linus Torvalds...
  company: "{{ fake(company) }}"          # Aperture Science, Arasaka...
  gender: "{{ fake(gender) }}"            # male, female, other, non-binary
  age: "{{ fake(age) }}"                  # 18-99
  country: "{{ fake(country) }}"          # France, Germany, Japan...
  
  # Adresses (FR par défaut, support multi-pays)
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
  
  # Réseau
  phone: "{{ fake(tel, '+33') }}"
  ip: "{{ fake(ip) }}"
  ipv6: "{{ fake(ipv6) }}"
  mac: "{{ fake(mac) }}"
  url: "{{ fake(url) }}"
  user_agent: "{{ fake(useragent) }}"
  
  # Localisation
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
  
  # Texte & Fichiers
  slug: "{{ fake(slug) }}"                # rick-arasaka-742
  bio: "{{ fake(text, 200) }}"
  mime_type: "{{ fake(mime) }}"           # application/pdf, image/png...
  encoding: "{{ fake(charset) }}"         # UTF-8, ISO-8859-1...
  device: "{{ fake(device) }}"            # iPhone, Galaxy S23, MacBook...
  device_icon: "{{ fake(device, 'symbol') }}" # 📱, 💻, ⌚...
  full_device: "{{ fake(fulldevice) }}"   # Apple iPhone 15 Pro Max, Samsung Galaxy S24 Ultra...
  
  # Devises
  currency: "{{ fake(currency) }}"         # EUR, USD, GBP, JPY... (aléatoire)
  currency_symbol: "{{ fake(currency, 'symbol') }}" # €, $, £, ¥... (aléatoire)
  currency_iso: "{{ fake(currency, 'eur') }}"  # EUR (code devise)
  currency_country: "{{ fake(currency, 'fr') }}"  # EUR (via code pays)
  currency_euro: "{{ fake(currency, 'eur', 'symbol') }}" # € (symbole devise)
  currency_us_symbol: "{{ fake(currency, 'us', 'symbol') }}" # $ (symbole via pays)
  currency_full: "{{ fake(fullcurrency) }}" # Euro, US Dollar... (aléatoire)
  currency_name: "{{ fake(fullcurrency, 'eur') }}" # Euro (code devise)
  currency_from_country: "{{ fake(fullcurrency, 'us') }}" # US Dollar (via code pays)
  
  # Couleurs
  color_name: "{{ fake(color) }}"         # aliceblue, crimson...
  color_hex: "{{ fake(hexcolor) }}"       # #A3F2B8
  color_rgb: "{{ fake(rgb) }}"            # rgb(255, 128, 0)
  color_rgba: "{{ fake(rgba) }}"          # rgba(255, 128, 0, 0.75)
  
  # Structures JSON - TOUS les 57 types de faker supportés !
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
  
  # Structures Serialize (même syntaxe que JSON !)
  simple_serialize: "{{ fake(serialize, 'id:int, name:string, active:boolean') }}"
  # a:3:{s:2:"id";i:42;s:4:"name";s:5:"lorem";s:6:"active";b:1;}
  
  user_settings: "{{ fake(serialize, 'theme:string, lang:string, notifications:boolean, data:{font:string, size:int}') }}"
  # a:4:{s:5:"theme";s:5:"lorem";s:4:"lang";s:5:"ipsum";s:13:"notifications";b:1;s:4:"data";a:2:{...}}
  
  preferences: "{{ fake(serialize, 'colors:hexcolor, timezone:string, currency:string') }}"
  # a:3:{s:6:"colors";s:7:"#A3F2B8";s:8:"timezone";s:5:"lorem";s:8:"currency";s:5:"ipsum";}
```

---

## ❓ Dépannage

### Mon scénario ne se sauvegarde pas
- Vérifiez le nom du scénario (pas d'espaces, caractères spéciaux)
- Au moins une instruction de chargement est requise
- Vérifiez la console du navigateur pour les erreurs

### Les lookups ne fonctionnent pas
- Vérifiez que la table/colonne de lookup existe
- Vérifiez que les conditions `where` correspondent à des données existantes
- Vérifiez que la colonne `return` existe dans la table

### Le YAML généré est incorrect
- Vérifiez la syntaxe JSON dans les champs de données
- Vérifiez que les placeholders sont correctement formatés
- Utilisez la prévisualisation pour déboguer

### Le constructeur ne se charge pas après l'édition YAML
- Vérifiez la syntaxe YAML (indentation, deux-points, tirets)
- Assurez-vous que toutes les sections requises sont présentes
- Vérifiez la console du navigateur pour les erreurs de parsing

---

## 📚 Ressources

- [Référence Rapide](QUICK_REFERENCE.md) - Liste complète des routes et fonctionnalités
- [Guide d'Installation](INSTALLATION.md) - Configuration et déploiement
- [Édition Bidirectionnelle](BIDIRECTIONAL_EDITING.md) - Comprendre la synchronisation YAML ↔ Constructeur
