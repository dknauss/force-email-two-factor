# Playground test blueprint

A self-contained [WordPress Playground](https://wordpress.github.io/wordpress-playground/)
blueprint that boots a **multisite** with the full 2FA stack so you can click
through enforcement in the browser.

It installs and network-activates:

- **Two Factor** (`two-factor`) — the **required** dependency (declared in this
  plugin's `Requires Plugins` header)
- **WebAuthn Provider for Two Factor** (`two-factor-provider-webauthn`) — recommended; passkeys / hardware keys
- **WP Mail Logging** (`wp-mail-logging`) — testing aid; captures the 2FA email so you can read the code
- **force-email-two-factor** — this plugin, inlined via `writeFile` (no external fetch)

…then enables multisite and creates a subsite (`/site2/`).

> The blueprint still installs all of these explicitly: WordPress's
> `Requires Plugins` dependency feature only *gates activation* — it does not
> auto-install missing plugins — and the WebAuthn/mail-logging plugins are
> companions, not declared dependencies.

## Run it

Playground can't fetch a blueprint from a **private** repo, so use one of:

1. **Paste into the builder (no publishing):** open
   <https://playground.wordpress.net/builder/builder.html>, paste the contents of
   [`blueprint.json`](blueprint.json), and click **Run**.
2. **One-click URL (requires hosting):** host `blueprint.json` somewhere public
   (a gist or a public repo) and open:
   `https://playground.wordpress.net/?blueprint-url=<RAW_JSON_URL>`

## What to try once it boots

- Lands on **Users → Profile** — the *Two-Factor Options* section shows Email
  (forced on) plus TOTP, Backup Codes, and **WebAuthn** registration.
- Log out and back in to see the **email 2FA challenge**, then read the code at
  **Tools → WP Mail Logging** (Playground has no real mail server; the logger
  captures the message anyway).
- **Network Admin → Plugins**: deactivate `force-email-two-factor` network-wide,
  or activate it on only one site, to compare enforcement modes.

## Caveats

- **Subdirectory multisite only** (subdomain networks don't work in-browser).
- Creating/visiting a subsite can be glitchy — see Playground
  [issue #2054](https://github.com/WordPress/wordpress-playground/issues/2054).
- **WebAuthn** needs a real authenticator (Touch ID, a security key, or the
  browser's virtual authenticator devtool); the provider always appears, but
  registration may not complete in every environment.

## Regenerate

After editing the plugin, rebuild the inlined copy:

```sh
php playground/build-blueprint.php
```
