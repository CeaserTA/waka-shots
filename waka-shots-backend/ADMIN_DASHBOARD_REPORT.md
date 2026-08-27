# Waka Shots Admin Dashboard Report

**Report date:** 25 August 2026  
**Application:** Waka Shots Laravel + Filament admin panel  
**Admin URL:** `/admin`

## Executive Summary

The Waka Shots admin dashboard is operational and provides content management, enquiry follow-up, service/package management, site settings, Google Drive connection management, and private client gallery administration.

The panel uses Filament 4.12 with Laravel 12 and supports two authenticated roles: `admin` and `editor`. Both roles can access the Filament panel. Sensitive resources such as Site Settings and Galleries are restricted to administrators.

## Current Sidebar Structure

The panel explicitly defines this group order:

1. Dashboard
2. Content
3. Operations
4. Client Delivery
5. Settings

### Content

- **Categories**
  - Create, edit, list, and delete content categories.
  - Category deletion is protected when the category is already in use.
- **Portfolio Items**
  - Manage portfolio title, category, and image path.
  - Portfolio images are shown as thumbnails in the table.
- **Journal Posts**
  - Manage category, title, published state, and rich-text post content.
  - Published state can be toggled from the table.
- **Films**
  - Manage YouTube video IDs and their content category.
  - Category filtering is available.

### Operations

- **Partners**
  - Manage client brands, corporate partners, and publications.
  - Upload partner logos to the public disk under `partners`.
  - Manage website URL and display order.
  - Partner logos appear as table thumbnails.
- **Enquiries**
  - View incoming client enquiries.
  - Update enquiry status: New, Contacted, Booked, or Closed.
  - Filter by status and requested service.
  - New enquiries appear in the sidebar badge count.
  - Admin users do not create enquiries manually; enquiries are received through the public contact flow.
  - Client and event details are protected from accidental editing on the edit page.

### Client Delivery

- **Galleries**
  - Create and manage private client galleries.
  - Store client name, event name, event date, Drive folder, expiry date, and active state.
  - Paste a full Google Drive folder URL; the application validates it and stores only the folder ID.
  - Generate secure, non-sequential client access tokens automatically.
  - Display active/expired status and the client-facing gallery link.
  - Copy the client-facing link from the table.
  - Gallery records are administrator-only.

### Settings

- **Studio Settings**
  - Manage studio name and hero tagline.
  - Manage contact email, phone, WhatsApp number, and address.
  - Manage Instagram, YouTube, and Facebook URLs.
  - Restricted to administrators.
- **Google Drive**
  - Not displayed as a standalone sidebar item.
  - Accessed from the top of the Galleries list page through `Connect Google Drive` or `Google Drive Settings`.
  - Restricted to administrators.

## Services and Packages

Services and packages are implemented with nested management:

- **Services** manage service names and whether packages are offered.
- **Packages** manage service association, tier name, and price.
- **Package Features** are managed inside a package through a nested relation manager.
- Packages can also be managed directly from the Service edit page.

The current resources still declare the navigation group `Services & Packages`. This is a legacy group and is not one of the four explicitly ordered groups above. It should be assigned to either **Content**, **Operations**, or **Client Delivery** if the sidebar must contain only the requested four groups.

## Google Drive Integration

The dashboard includes an OAuth 2.0 connection flow for the photographer's Google Drive account:

- Uses `google/apiclient`.
- Uses the read-only Drive scope.
- Uses offline access and consent prompting to obtain a refresh token.
- Stores access and refresh tokens using Laravel encrypted casts.
- Refreshes expired access tokens automatically.
- Stores the connected account email when available.
- Provides a `drive:test-connection {folderId}` Artisan command.
- Handles missing or failed connections with catchable errors instead of exposing raw exceptions to clients.

## Public Gallery Support Connected to Admin

Although the public gallery page is not part of the Filament sidebar, it is managed through the Galleries resource:

- Public URL format: `/gallery/{token}`
- Gallery links are based on secure generated tokens.
- Inactive or expired galleries show a friendly unavailable page.
- Image previews are served through the application.
- Google Drive thumbnail URLs are upgraded from sizes such as `=s220` to `=s1600` for sharper browsing previews.
- Individual image downloads are streamed through the application.
- Download All creates a temporary ZIP archive and deletes it after sending.
- Gallery view, image download, and bulk download access are logged.
- Download routes use rate limiting.
- Gallery pages include `noindex, nofollow` metadata.

## Authorization

### Panel Access

The `User` model implements Filament panel access rules. Users with either of these roles can enter the panel:

- `admin`
- `editor`

Other or invalid roles are rejected.

### Resource Authorization

Explicit administrator-only policies currently exist for:

- Site Settings
- Galleries

Editors are denied access to these sensitive areas, including direct resource access. Other content, service, and operations resources currently rely on Filament's default resource authorization behavior and remain available to both panel roles.

## Technical Structure

- **Framework:** Laravel 12
- **Admin framework:** Filament 4.12
- **Authentication:** Laravel session authentication with Filament panel login
- **Database:** MySQL in the local environment
- **OAuth client:** `google/apiclient`
- **Token storage:** encrypted Eloquent casts
- **Resource discovery:** automatic discovery under `app/Filament/Resources`
- **Custom page discovery:** automatic discovery under `app/Filament/Pages`

## Validation Completed

The current automated suite passes:

- **30 tests passed**
- **95 assertions passed**
- Database migrations have been applied successfully.
- Blade templates compile successfully.
- Google Drive image listing has been verified with the configured test folder.
- Gallery ZIP creation has been covered by a feature test.
- OAuth guest redirect behavior has been tested against the Filament login route.

## Remaining Considerations

1. Move Services and Packages into one of the four approved sidebar groups if `Services & Packages` should no longer appear.
2. Add a dedicated admin user-management resource if administrators need to create or change user roles from the dashboard.
3. Consider a single-record settings page pattern for Studio Settings to prevent duplicate settings rows.
4. Consider moving very large Gallery ZIP creation to a queued job if galleries regularly exceed approximately 100 images.
5. Continue monitoring Google OAuth connectivity and refresh-token validity in production.
