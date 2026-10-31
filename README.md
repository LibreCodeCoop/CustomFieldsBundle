# CustomFieldsBundle

Adds user preferences to Kimai:

- CPF (tax_number): used to match cooperados across systems.
- Email: a hidden preference copied from the user's email for API clients.

Dependents are maintained only in Nextcloud profile_fields. This plugin no
longer defines a Kimai field for them. Existing preferences are not used or
removed by this plugin.

## Installation

Copy the plugin files to var/plugins/CustomFieldsBundle/ and clear the Kimai
application cache.
