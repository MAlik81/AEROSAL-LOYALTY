# Repository guide

This repository contains the **Aerosal Loyalty** feature module for Siberian CMS. It mixes PHP (backend controllers/models), AngularJS/Ionic (mobile app client), and static assets.

## Layout at a glance
- `Controller/` and `controllers/` contain admin/application, mobile, and public REST controllers. The capitalised directory holds the Core router entry point, while the lowercase tree follows Siberian's MVC conventions—keep files in their existing directories when extending features.
- `Model/` contains business models plus `Model/Db/Table/` Zend DB table gateways. Models typically extend `Core_Model_Default` and expose helper methods that wrap table queries.
- `features/aerosalloyalty/` holds the mobile AngularJS feature: templates, controllers, factories, SCSS, and metadata (`feature.json`).
- `resources/` stores DB schema definitions, translation resources, and generated card snapshots (`resources/cards`).
- `init.php` and `uninstall.php` are module bootstrap hooks.

## Backend (PHP) conventions
- Follow the existing 4-space indentation and brace style. Use short array syntax (`[]`).
- Controller actions should validate required parameters early, wrap risky operations in `try/catch`, and always respond via `$this->_sendJson([...])`. Prefer translatable messages via `p__('Aerosalloyalty', 'Message')` for user-facing strings.
- Models usually wrap a single table class. Instantiate the relevant `Aerosalloyalty_Model_Db_Table_*` once in `__construct` and expose slim helper methods (finders, upserts, etc.). Share logic between controllers by adding methods to models instead of duplicating DB access.
- Database interactions use Zend DB. When you add columns or tables, update the appropriate file in `resources/db/schema/` and mirror changes inside the corresponding table gateway.
- Webhook and external HTTP calls should be logged with `Aerosalloyalty_Model_WebhookLog::log` as done in the public controller; keep logging best-effort and never allow it to break the main flow.
- Card numbers are treated as EAN-13 by default. Re-use the validation helpers in `controllers/Mobile/ViewController.php` when adding new entry points to avoid divergence.

## Frontend (AngularJS/Ionic) conventions
- The mobile client is a classic Siberian/Ionic AngularJS 1.x module registered on `starter`. Controllers are under `features/aerosalloyalty/js/controllers/` and services under `features/aerosalloyalty/js/factory/`.
- Use the provided `Aerosalloyalty` factory for HTTP calls. If you introduce new endpoints, extend the factory first and keep requests either `application/x-www-form-urlencoded` (POST) or query parameters (GET) for consistency.
- UI state is handled by `$scope.state` (`loading`, `setup`, `card`, `campaigns`, etc.). Stick to that state machine when adding flows.
- Barcode rendering currently defaults to the external BWIP-JS service on the client and Zend_Barcode on the server. Keep both pathways compatible when making changes.

## Translations & assets
- Wrap user-facing messages in `p__('Aerosalloyalty', '...')` (backend) or rely on Ionic popups with plain strings on the client. Add new localisation strings to the translation resources if you expand messaging.
- Icons referenced in `feature.json` live under `features/aerosalloyalty/icons/`; update both if you add more artwork.

## Testing & troubleshooting
- There are no automated tests in this repository. After backend changes, verify key flows manually via the admin (campaign management) and mobile endpoints (`controllers/Mobile/ViewController.php`).
- For API changes, exercise the public REST endpoints with cURL/Postman using Bearer tokens validated by `Aerosalloyalty_Model_ApiToken`.
- When modifying uninstall/bootstrap scripts ensure that newly added resources are cleaned up in `uninstall.php`.

## Miscellaneous
- Random token/UID helpers live in `Model/ApiToken.php` and `Model/Campaign.php`. Re-use them instead of rolling new generators.
- Any filesystem writes (e.g., card JSON snapshots) go under `resources/cards`. Maintain the defensive checks around directory creation/writability.

