# Workcity Client Projects

**Client Projects Management Plugin** for Workcity Africa assessment.

## Features

- Registers a `client_project` Custom Post Type.
- Adds meta fields: Client Name, Project Status, Deadline.
- Custom admin list table with filters, sortable columns, and bulk actions.
- `[client_projects]` shortcode for a responsive, interactive front‑end.
- AJAX filtering (via `class-ajax-handler.php`).
- REST API endpoint at `/wp-json/wcp/v1/projects` (via `class-rest-api.php`).
- Custom capabilities for fine‑grained access control.
- Internationalization ready (`languages/`).
- PHPUnit tests included (`tests/`).
