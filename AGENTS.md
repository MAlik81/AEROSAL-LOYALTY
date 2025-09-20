# Agent Notes for Migachat Bridge API Module

This repository contains a customized **Siberian CMS** module focused on the Migastone "Migachat" integration.
Use this document to track key updates and helpful references when iterating on the codebase.

## Module context
- Upstream framework: [Siberian CMS](https://github.com/Xtraball/Siberian.git)
- Primary entry point for the public API: `controllers/Public/BridgeapiController.php`
- A preserved copy of the legacy controller lives in `controllers/Public/BridgeapiControllerBU.php` for reference during refactors.

## Recent update highlights
- Consolidated request parsing, validation, and error logging helpers within `sendmessageAction()` to simplify flow control.
- Strengthened mandatory parameter checks for `instance_id`, `message`, and `auth_token` while keeping decoded JSON payloads intact for logging.

_Add any new features, fixes, or structural adjustments here so future tasks have quick historical context._

## Workflow tips
- Run `php -l <file>` after editing PHP files to catch syntax issues quickly.
- When a class or helper is missing from this repo, consult the official Siberian CMS repository linked above for baseline implementations.
- Keep the BU controller file unchanged unless explicitly instructed, as it serves as the canonical reference to the original behavior.
