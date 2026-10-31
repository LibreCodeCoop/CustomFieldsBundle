# CustomFieldsBundle (Kimai)

Custom user preferences provided by the plugin:

- **CPF** (`tax_number`) — used only to reconcile the same person across systems.
- **Email** — copied from the Kimai user attribute into a hidden API preference.

**Dependents have moved exclusively to Nextcloud `profile_fields`.** The plugin
no longer registers or edits the old `dependents` preference. Existing Kimai
database values are not changed automatically on install or upgrade.

## Installation

Install the plugin files in `var/plugins/CustomFieldsBundle/` and rebuild the
Kimai application cache according to the installed Kimai version.

## Safe removal of legacy Kimai values

First complete and verify the Nextcloud Profile Fields records for **every**
cooperado who has a previous `dependents` preference. A missing value is **not**
equivalent to zero. Preserve an authorized archival snapshot of existing data.

Run the audit-only command (no deletion):

```bash
php bin/console kimai:custom-fields:remove-legacy-dependents
```

When Nextcloud has been reconciled and the operator has approved cleanup, run:

```bash
php bin/console kimai:custom-fields:remove-legacy-dependents --apply --profile-fields-verified
```

This deletes only rows named `dependents` in `kimai2_user_preferences`. It does
not modify `tax_number`, `email`, employee records, or time entries. It is not
executed automatically by the plugin upgrade. Do not run against production as
a substitute for reconciling Nextcloud first.
