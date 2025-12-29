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

### Opening the YAML Editor

1. Click the floating **📝 YAML** button (bottom-right corner)
2. The YAML editor panel appears as an overlay

### Builder → YAML (Generate)

1. Fill in the visual form:
   - Add imports, variables, load instructions, etc.
2. Click **🔄 Builder → YAML** button
3. YAML is generated from your form data

### YAML → Builder (Parse & Apply)

1. Edit the YAML in the textarea:
   - Fix typos
   - Add new sections
   - Copy/paste from other files
2. Click **⬅️ YAML → Builder** button
3. Confirm the warning dialog
4. Visual form is updated with your YAML content

**Warning:** Clicking "YAML → Builder" will **replace all current form content**. Make sure to save your work or generate YAML first if you want to keep it.

---

## 📝 YAML Parser Features

The custom JavaScript YAML parser supports all PrismBundle features:

### ✅ Supported Structures

#### Imports
```yaml
import:
  - base_users
  - includes/acl
```

#### Variables
```yaml
vars:
  admin: "admin_{{ scope }}"
  api_key: "{{ env('API_KEY') }}"
```

#### Load Instructions
```yaml
load:
  - table: users
    data:
      username: "user_{{ scope }}"
      email: "{{ fake(email) }}"
      password: "{{ hash('secret') }}"
    types:
      created_at: datetime_immutable
    pivot:
      id: 123
      column: user_id
```

#### Lookup (FK Resolution)
```yaml
load:
  - table: posts
    data:
      title: "My Post"
      author_id:
        table: users
        where:
          username: "admin_{{ scope }}"
        return: id
```

#### Purge Instructions
```yaml
purge:
  - table: users
    where:
      username: "admin_{{ scope }}"
  - purge_pivot: true
```

### ⚠️ Limitations

The parser is simplified and has some limitations:

1. **Comments are removed**: Comments (`#`) are stripped during parsing
2. **Basic indentation detection**: Uses simple indent counting
3. **Single where condition in lookups**: Only one `where` key per lookup
4. **No complex YAML features**: No anchors, aliases, or multi-line strings
5. **Whitespace sensitive**: Proper indentation is required

**Tip:** After applying YAML to builder, regenerate YAML to see the canonical format.

---

## 🎨 Workflow Examples

### Example 1: Import Existing Scenario

**Scenario:** You have a YAML file and want to modify it in the builder.

1. Open file: `cat prism/my_scenario.yaml`
2. Copy all content
3. Go to `/prism/create`
4. Click **📝 YAML**
5. Paste content into editor
6. Click **⬅️ YAML → Builder**
7. Edit using visual form
8. Click **💾 Save Scenario**

### Example 2: Quick YAML Editing

**Scenario:** You want to add 5 similar users but with different names.

1. Create first user in visual builder
2. Click **🔄 Builder → YAML**
3. Copy/paste the user block 5 times in YAML
4. Modify names directly in YAML
5. Click **⬅️ YAML → Builder**
6. Verify in form
7. Save

### Example 3: Learning Mode

**Scenario:** New to YAML, want to learn the syntax.

1. Use visual builder to create a scenario
2. Add import, variable, load instruction
3. Click **🔄 Builder → YAML**
4. Study the generated YAML
5. Make small changes in YAML
6. Click **⬅️ YAML → Builder**
7. See how changes affect the form
8. Repeat to learn patterns

---

## 🔧 Technical Details

### Parser Implementation

The YAML parser is a custom JavaScript implementation (~200 lines) that:

1. Splits text into lines
2. Filters comments and empty lines
3. Tracks indentation levels
4. Identifies sections (import, vars, load, purge)
5. Parses nested structures (data, types, pivot, where)
6. Handles lookups with nested where conditions
7. Returns structured JavaScript object

### Rebuilding the Form

When applying YAML to builder:

1. Existing form items are cleared
2. Counters are reset
3. New items are created using `add*()` functions
4. Field values are populated from parsed data
5. Preview is regenerated for validation

### Data Flow

```
User Input (Form) 
  ↓
collectFormData()
  ↓
generateYAML()
  ↓
YAML Text (Editable)
  ↓
parseYAML()
  ↓
JavaScript Object
  ↓
applyYamlToBuilder()
  ↓
Form Rebuilt
```

---

## 💡 Tips & Best Practices

### ✅ Do's

- **Save often**: Use browser's local storage or copy YAML externally
- **Test with simple scenarios first**: Understand the parser before complex edits
- **Use "Builder → YAML" to validate**: Generate YAML to see canonical format
- **Keep YAML clean**: Proper indentation (2 spaces per level)
- **Check the form after applying**: Verify that YAML was parsed correctly

### ❌ Don'ts

- **Don't rely on comments**: They're stripped during parsing
- **Don't use complex YAML features**: Stick to simple key-value structures
- **Don't skip confirmation dialog**: It prevents accidental data loss
- **Don't edit deeply nested structures directly**: Use builder for complex lookups
- **Don't forget to save**: Changes in YAML editor are not auto-saved

---

## 🐛 Troubleshooting

### "Error parsing YAML"

**Cause:** Invalid YAML syntax or unsupported structure.

**Solution:**
1. Check indentation (must be 2 spaces)
2. Verify colons (`:`) are properly placed
3. Check that all sections are properly closed
4. Look at generated YAML for reference format

### Form is empty after applying YAML

**Cause:** Parser couldn't recognize the YAML structure.

**Solution:**
1. Click **🔄 Builder → YAML** on a working scenario
2. Compare your YAML to the generated format
3. Fix structural differences
4. Try again

### Lookup not working

**Cause:** Nested `where` structure not properly indented.

**Solution:**
```yaml
# ❌ Wrong
user_id:
  table: users
  where: username: "admin"
  
# ✅ Correct
user_id:
  table: users
  where:
    username: "admin"
  return: id
```

### Missing fields after sync

**Cause:** Fields might use unsupported YAML features.

**Solution:**
- Stick to simple key-value pairs
- Use visual builder for complex structures
- Check console for parser errors (F12)

---

## 🎓 Advanced Usage

### Batch Operations

Create multiple similar items efficiently:

```yaml
load:
  # Created in builder
  - table: users
    data:
      username: "user1_{{ scope }}"
      
  # Copied in YAML editor
  - table: users
    data:
      username: "user2_{{ scope }}"
  
  - table: users
    data:
      username: "user3_{{ scope }}"
```

### Template Reuse

1. Create base template in builder
2. Generate YAML
3. Save YAML as template
4. Copy template for new scenarios
5. Modify in YAML editor
6. Apply to builder

### Mixed Workflow

1. **Complex lookups**: Use visual builder
2. **Bulk edits**: Switch to YAML
3. **Validation**: Generate YAML from builder
4. **Final save**: Use visual form's Save button

---

## 📚 Related Documentation

- [SCENARIO_BUILDER.md](../SCENARIO_BUILDER.md) - Complete builder guide
- [README.md](../README.md) - PrismOffice overview
- [PrismBundle YAML Guide](../../PrismBundle/docs/PRISM_YAML.md) - Full YAML syntax reference

---

## 🎉 Conclusion

Bidirectional editing makes PrismOffice the most flexible scenario builder:

- **Beginners**: Use visual builder exclusively
- **Intermediate**: Generate YAML to learn syntax
- **Advanced**: Edit YAML directly, validate with builder
- **Everyone**: Mix both approaches as needed

The choice is yours! 🚀
