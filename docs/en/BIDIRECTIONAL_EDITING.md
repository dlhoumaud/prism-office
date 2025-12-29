# Bidirectional YAML Editing

## 🔄 Overview

PrismOffice now supports **bidirectional editing** between the visual builder and YAML code. This means you can:

1. **Build visually** → Generate YAML
2. **Edit YAML** → Update visual builder

This gives you the best of both worlds: the simplicity of a visual interface and the power of direct YAML editing.

---

## 🎯 Use Cases

### 1. Quick Copy/Paste
Copy YAML from an existing scenario file, paste it into the editor, and click "YAML → Builder" to import it instantly.

```yaml
# Copy this from an existing file
load:
  - table: users
    data:
      username: "admin_{{ scope }}"
      email: "admin@test.com"
```

### 2. Power User Workflow
Expert users can write YAML faster than clicking through forms:
- Open YAML editor
- Write complete scenario in YAML
- Click "YAML → Builder" to validate
- Fine-tune with visual builder if needed

### 3. Learning Tool
New users can:
- Build using visual form
- Click "Builder → YAML" to see the result
- Learn YAML syntax by example
- Experiment with manual edits

### 4. Hybrid Editing
- Use visual builder for complex lookups (easier to visualize)
- Switch to YAML for bulk edits
- Sync back and forth as needed

---

## 🚀 How to Use

### Access the YAML Editor

Click the floating **"📝 YAML"** button (bottom-right corner of the create page).

### Two Sync Directions

#### Builder → YAML (Generate YAML from Form)

1. Fill in the visual form:
   - Add imports
   - Define variables
   - Create load instructions
   - Add purge rules
2. Click **"📝 YAML"** button
3. Click **"🔄 Builder → YAML"**
4. YAML appears in the editor

**Use when:**
- You want to see the generated YAML
- You prefer visual building
- You want to copy the YAML to another file

#### YAML → Builder (Import YAML to Form)

1. Click **"📝 YAML"** button
2. Paste or edit YAML directly in the textarea
3. Click **"⬅️ YAML → Builder"**
4. Confirm the action in the dialog
5. Form is populated with parsed YAML

**Use when:**
- You have existing YAML to import
- You want to make bulk edits
- You're faster with YAML than forms

---

## 💡 Tips & Tricks

### Safety Features

✅ **Confirmation Dialog** - Prevents accidental overwrites when syncing YAML → Builder  
✅ **Validation** - Invalid YAML shows an error message instead of breaking the form  
✅ **Round-trip Verification** - After applying YAML, the builder regenerates it to confirm parsing  

### Best Practices

1. **Start Visual, Finish in YAML**
   - Build complex lookups visually
   - Switch to YAML for final touches
   
2. **Copy Existing Scenarios**
   ```bash
   # Copy YAML from existing file
   cat prism/my_scenario.yaml
   # Paste in editor → Click "YAML → Builder"
   ```

3. **Learn by Example**
   - Build something simple visually
   - Click "Builder → YAML"
   - Study the generated YAML

4. **Bulk Editing**
   - Use YAML editor for repetitive data
   - Much faster than clicking "Add Field" 20 times

### Keyboard Shortcuts

The YAML textarea supports standard editing:
- `Ctrl+A` / `Cmd+A` - Select all
- `Ctrl+C` / `Cmd+C` - Copy
- `Ctrl+V` / `Cmd+V` - Paste
- `Tab` - Insert tab (indentation)

---

## 📝 Supported YAML Features

All PrismBundle YAML features are supported:

### ✅ Imports
```yaml
import:
  - base_scenario
  - another_scenario
```

### ✅ Variables
```yaml
vars:
  admin: "admin_{{ scope }}"
  email_domain: "test.com"
```

### ✅ Load Instructions

#### Simple Values
```yaml
load:
  - table: users
    data:
      username: "{{ $admin }}"
      email: "{{ $admin }}@{{ $email_domain }}"
```

#### Lookups (FK)
```yaml
load:
  - table: posts
    data:
      title: "My Post"
      user_id:
        table: users
        where:
          username: "{{ $admin }}"
        return: id
```

#### With Database
```yaml
load:
  - table: audit_logs
    db: hexagonal_secondary
    data:
      action: "user_created"
```

#### Types
```yaml
load:
  - table: users
    data:
      username: "admin"
      created_at: "{{ now }}"
    types:
      created_at: datetime_immutable
```

#### Pivot Custom
```yaml
load:
  - table: sessions
    data:
      session_id: "{{ uuid }}"
      user_id: 1
    pivot:
      column: session_id
```

### ✅ Purge Instructions

#### Simple Purge
```yaml
purge:
  - table: users
    where:
      username: "admin_{{ scope }}"
```

#### With Database
```yaml
purge:
  - table: audit_logs
    db: hexagonal_secondary
    where:
      action: "user_created"
```

#### Purge Pivot
```yaml
purge:
  - table: sessions
    where:
      user_id: 1
    purge_pivot: true
```

---

## 🎬 Complete Workflow Example

### Scenario: Import Existing YAML

1. **Open PrismOffice**
   ```
   http://localhost:8000/prism/create
   ```

2. **Click "📝 YAML" button**

3. **Paste existing YAML**
   ```yaml
   import:
     - base_users
   
   vars:
     admin: "admin_{{ scope }}"
   
   load:
     - table: users
       data:
         username: "{{ $admin }}"
         email: "{{ $admin }}@test.com"
         password: "{{ hash('secret') }}"
       types:
         created_at: datetime_immutable
     
     - table: posts
       data:
         title: "First Post"
         user_id:
           table: users
           where:
             username: "{{ $admin }}"
           return: id
   
   purge:
     - table: posts
       where:
         title: "First Post"
     - table: users
       where:
         username: "{{ $admin }}"
   ```

4. **Click "⬅️ YAML → Builder"**

5. **Confirm the action**

6. **Result:**
   - Form is populated with 1 import
   - Form shows 1 variable
   - Form displays 2 load instructions (with lookup)
   - Form shows 2 purge instructions

7. **Make adjustments** (if needed) using visual form

8. **Click "💾 Save Scenario"**

---

## 🐛 Troubleshooting

### "Invalid YAML" Error

**Problem:** Clicked "YAML → Builder" but got an error

**Solution:**
- Check YAML syntax (indentation must be consistent)
- Ensure colons have spaces: `table: users` (not `table:users`)
- Arrays start with `-` and a space
- Strings with special chars need quotes

**Example of invalid YAML:**
```yaml
load:
- table:users  # ❌ Missing space after colon
  data:
   username: test  # ❌ Inconsistent indentation
```

**Corrected:**
```yaml
load:
  - table: users  # ✅ Space after colon
    data:
      username: test  # ✅ Consistent 2-space indentation
```

### Form Not Updating

**Problem:** Clicked "YAML → Builder" but form didn't change

**Solution:**
- Make sure you clicked "Confirm" in the dialog
- Check browser console for JavaScript errors
- Refresh the page and try again

### YAML Lost After Error

**Problem:** Made a typo in YAML, now it's gone

**Solution:**
- The YAML textarea is preserved even after errors
- Just fix the syntax error and try again
- Copy YAML to external editor before complex edits

---

## 🔗 Related Documentation

- [Scenario Builder Guide](SCENARIO_BUILDER.md)
- [Installation Guide](INSTALLATION.md)
- [Quick Reference](QUICK_REFERENCE.md)
