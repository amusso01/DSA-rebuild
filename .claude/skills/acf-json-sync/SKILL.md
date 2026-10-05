---
name: acf-json-sync
description: Edit and sync ACF field group JSON (acf-json). Use when adding or changing ACF fields in JSON, when sync is not appearing, when Sync available never clears after syncing, or when working with acf-json folder and block field groups.
---

# ACF JSON – editing and sync

Use this skill when creating or editing ACF field group JSON files (e.g. in `acf-json/`) so that the agent follows project conventions and sync works.

## 1. Human-readable JSON

**Always write ACF JSON in expanded form**, not as a single line.

- Use **4-space indentation** for nesting.
- Put each object key and array element on its own line where it stays readable.
- ACF accepts both minified and pretty-printed JSON; readable files are easier to diff and review.

Example of the right style:

```json
{
    "key": "group_abc123",
    "title": "My Block",
    "fields": [
        {
            "key": "field_xyz",
            "label": "Title",
            "name": "title",
            "type": "text"
        }
    ],
    "modified": 1771519580
}
```

## 2. Set `modified` so sync works (without a loop)

ACF shows **Sync available** when the JSON file is considered **newer** than the field group in the database.

- **`modified`** in the JSON is a **Unix timestamp** (seconds since 1 January 1970 00:00:00 UTC).
- ACF compares this to the field group's **`post_modified`** in the database (for this check, treat both as comparable Unix times).
- If **JSON `modified` > DB `post_modified`**, that group is listed under Sync available.

### When you change JSON and want the site to pull those changes

Bump **`modified`** so it is **strictly greater than** the group's current **`post_modified`** in the database.

**Practical rule:** run `date +%s` and use that value, or **current + 1**. That is enough for the JSON to count as newer after a normal edit.

**Do not** set `modified` to arbitrary values far in the future (rounded "placeholder" times, hours or days ahead, etc.). That does **not** make sync "more reliable"—it causes the **infinite Sync available loop** in section 3.

**Clock alignment:** `post_modified` follows the **WordPress server's** clock. If the dev machine clock differs from production, prefer `date +%s` on the server when possible, or compare JSON `modified` to a known-good `post_modified` after saving a group in the UI once.

### When JSON will not show Sync available

If **JSON `modified` ≤ DB `post_modified`**, ACF will not offer sync from that file until `modified` is bumped again.

## 3. "Sync available" never clears (infinite sync loop)

### What it looks like

You run **Sync** under **Custom Fields → Tools**, ACF reports success, but the same field group(s) still show **Sync available**. Multiple groups may behave the same way after hand-edited JSON.

### Why it happens

- Sync **imports JSON into the database** and sets **`post_modified`** to about **now**.
- Sync **does not** update the JSON file on disk; `modified` in the repo stays whatever it was.
- If **`modified` in JSON is still greater than** the new **`post_modified`** (typical when `modified` was **ahead of the server clock** or set artificially high), ACF immediately treats the file as newer again → **Sync available** forever.

### Fix

1. Open the group's file under `acf-json/` (filename is usually `{group_key}.json`).
2. Set **`modified`** to the **current Unix timestamp** from `date +%s` (on the WordPress host if clocks differ).
3. Save, then sync **once** in the dashboard. The entry should clear.

### Optional diagnosis

Compare JSON `modified` to DB `post_modified`, e.g. `wp post list --post_type=acf-field-group --fields=post_name,post_modified`. If the JSON number is larger than the Unix time you expect for "now" on the server, you are in the loop scenario above.

## 4. Other conventions

- **Save/load path**: The theme must set `acf/settings/save_json` and `acf/settings/load_json` to the theme's `acf-json` folder (e.g. `get_template_directory() . '/acf-json'`). Otherwise ACF uses the default path and theme JSON is ignored.
- **Field type IDs**: Use the official slugs from the docs (e.g. `text`, `textarea`, `color_picker`, `tab`, `image`, `wysiwyg`, `repeater`). Wrong or custom type strings can break sync or field behaviour.
- **Color Picker (ACF 6)**: Use **`enable_transparency`** (0 or 1), not `enable_opacity`, in the JSON.
- **Block location**: Block field groups use `"param": "block"` and `"value": "acf/fd-block-name"` in the `location` array. Each block has its own field group JSON file — do not create separate library groups for shared fields.
- **Shared block fields**: Add `anchor_id` first in each block's `Options` tab, then `padding_top` and `padding_bottom` (50/50 row), then `appearance` when the block uses it, then any block-specific option fields. Do not use ACF Clone fields pointing at shared library groups for these.
- **Options tab order**: Anchor id → padding top + padding bottom → appearance (if used) → block-specific options. Use `conditional_logic` on optional 50/50 fields so hidden fields do not shift the layout of earlier rows.
- **Block API version**: Always set `'api_version' => 3` and `'acf_block_version' => 3` in the `acf_register_block()` registration array. `api_version` is the WordPress/Gutenberg block API version (required for iframe editor, mandatory from WP 7.0). `acf_block_version` is ACF's own version that enables the V3 editing model (preview always in canvas, fields in sidebar + expanded editor). Do not include `'mode' => 'edit'` -- ACF Blocks V3 removes modes.

## 5. Documentation links

Future agents (and you) can use these for exact behaviour and field structures:

- **ACF Resources (overview)**: https://www.advancedcustomfields.com/resources/
- **Local JSON** (save/load paths, folder): https://www.advancedcustomfields.com/resources/local-json/
- **Synchronized JSON** (when sync is offered, `modified`): https://www.advancedcustomfields.com/resources/synchronized-json/
- **Field types** (type IDs and settings):
  - Text: https://www.advancedcustomfields.com/resources/text/
  - Text Area: https://www.advancedcustomfields.com/resources/textarea/
  - Color Picker: https://www.advancedcustomfields.com/resources/color-picker/
  - Tab: part of Layout fields in the Resources index
- **Register fields via PHP** (can help infer JSON structure): https://www.advancedcustomfields.com/resources/register-fields-via-php/

When in doubt, compare with an existing JSON file in the project that ACF has already written (e.g. one that was saved from the field group editor), or check the relevant field type page in the ACF docs.
