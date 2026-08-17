---
name: Portail Numérique du Ministère des Transports
description: Admin/citizen portal for driver, vehicle, tax, and parking management (RDC)
colors:
  primary-blue: "#007FFF"
  primary-blue-dark: "#005FCC"
  primary-blue-light: "#3399FF"
  secondary-red: "#CE1021"
  secondary-red-dark: "#A00D1A"
  accent-yellow: "#FCD116"
  ink: "#1A2744"
  ink-muted: "#64748B"
  ink-faint: "#94A3B8"
  border: "#E2E8F0"
  surface-tint: "#F1F5F9"
  surface-tint-light: "#F8FAFC"
  status-info: "#3B82F6"
  status-accent: "#6366F1"
  status-secondary: "#8B5CF6"
  status-success: "#059669"
  status-success-bright: "#10B981"
  status-warning: "#D97706"
  status-danger: "#EF4444"
typography:
  display:
    fontFamily: "Poppins, sans-serif"
    fontSize: "32px"
    fontWeight: 700
    lineHeight: 1.3
  headline:
    fontFamily: "Poppins, sans-serif"
    fontSize: "24px"
    fontWeight: 700
    lineHeight: 1.3
  title:
    fontFamily: "Poppins, sans-serif"
    fontSize: "16px"
    fontWeight: 600
    lineHeight: 1.3
  body:
    fontFamily: "Roboto, sans-serif"
    fontSize: "16px"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Roboto, sans-serif"
    fontSize: "13px"
    fontWeight: 500
    lineHeight: 1.4
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  xxl: "48px"
components:
  button-primary:
    backgroundColor: "{colors.primary-blue}"
    textColor: "#FFFFFF"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  card:
    backgroundColor: "#FFFFFF"
    rounded: "{rounded.lg}"
    padding: "20px"
---

# Design System: Portail Numérique du Ministère des Transports

## 1. Overview

**Creative North Star: "The Operations Desk"**

This is a government operations tool, not a consumer product — the honest metaphor is a well-run
administrative desk, not a startup dashboard. Ministry staff spend entire shifts inside it processing
drivers, vehicles, taxes, and printed cards; citizens touch it rarely, often on weak mobile connections.
Every visual decision should read as "competent institution", never "trying to look modern" — clarity and
speed of task completion are the actual product.

The public-facing pages (home, login, register, contact) carry the DR Congo national palette — blue,
red, yellow — as real brand identity. The `/admin/*` operational tool intentionally does **not** use that
palette for its interface chrome: functional status color (blue=info, green=success, amber=warning,
red=danger) needs to follow near-universal software conventions so staff can scan a table at speed,
and forcing national colors into status badges would fight that legibility instead of serving it. This is
a deliberate split, not an oversight — document it so future work doesn't "fix" it by accident.

**What this system explicitly rejects**: decorative flourish, gimmicky animation, gradient-for-its-own-sake,
anything that reads as consumer SaaS rather than a serious institutional tool. See PRODUCT.md's
anti-references.

**Key Characteristics:**
- White cards on a light-gray canvas, soft single-level shadow, no heavy elevation
- Poppins for anything a user reads as a heading; Roboto for everything they read as data or body copy
- Function-coded color (status, category) on a neutral gray/white base — color is meaningful, never decorative
- Dense, table-heavy layouts on `/admin/*`; more breathing room on the public-facing pages

## 2. Colors

Two coexisting palettes by design, not by accident — see Overview.

### Primary
- **National Blue** (#007FFF): the institutional brand color. Header/nav accents, primary CTAs, and
  every touchpoint on the public-facing pages (home, login, register, contact).

### Secondary
- **National Red** (#CE1021): reserved, used sparingly on public pages only — never as a functional
  "danger" color inside `/admin/*` (that role belongs to Status Danger below; conflating the two would
  make an ordinary UI warning read as a national-flag statement).

### Tertiary
- **National Yellow** (#FCD116): accent only, smallest footprint of the three. Never body text (fails
  contrast on white).

### Neutral
- **Ink** (#1A2744): primary text color across `/admin/*`.
- **Ink Muted** (#64748B): secondary text, labels, table sub-text. Verify against your actual background
  before reusing at small sizes — see Do's and Don'ts.
- **Ink Faint** (#94A3B8): tertiary/disabled text and counts. Reserve for genuinely de-emphasized content,
  not default body copy.
- **Border** (#E2E8F0): dividers, table borders, input outlines.
- **Surface Tint** (#F1F5F9) / **Surface Tint Light** (#F8FAFC): table header backgrounds, subtle section
  backgrounds, hover states on white cards.

### Status roles (admin operational UI only)
- **Status Info** (#3B82F6): informational, primary data emphasis.
- **Status Accent** (#6366F1): a second accent for categorical distinction (e.g. "nouveau" brevet state)
  where Info is already used elsewhere on the same screen.
- **Status Secondary** (#8B5CF6): tertiary categorical color (vehicles, secondary metrics).
- **Status Success** (#059669) / **Status Success Bright** (#10B981): completed/active/confirmed states.
  Two greens exist in the current codebase for historical reasons (icon fill vs. badge fill) — treat
  #059669 as the canonical "success" and #10B981 as its bright/badge variant, don't introduce a third.
- **Status Warning** (#D97706): pending/in-progress states.
- **Status Danger** (#EF4444): destructive actions, error states, expired/suspended records.

### Named Rules
**The Two-Palette Rule.** National colors (blue/red/yellow) belong to the public-facing brand surface.
Status colors (info/success/warning/danger) belong to the operational admin surface. Never cross them —
an admin status badge should never turn "national red", and a public marketing CTA should never be
colored "status danger red" just because the hex happens to be similar.

## 3. Typography

**Display Font:** Poppins (with system sans-serif fallback)
**Body Font:** Roboto (with system sans-serif fallback)

**Character:** Poppins' geometric roundness reads as approachable-but-official for headings; Roboto's
neutral, highly-legible letterforms carry the actual operational data (tables, forms, numbers) without
competing for attention. The pairing is already correct — the problem is execution, not font choice (see
Do's and Don'ts).

### Hierarchy
- **Display** (700, 32px, 1.3): page-level `h1`, public-site hero headings only.
- **Headline** (700, 24px, 1.3): dashboard/section page titles ("Tableau de bord", "Statistiques").
- **Title** (600, 16px, 1.3): card titles, chart titles, section headers within a page.
- **Body** (400, 16px, 1.6): public-site paragraph copy, form field values. On `/admin/*` this drops to
  14px in practice for table cells — an intentional density trade-off for data-dense screens, not a bug.
- **Label** (500, 13px, 1.4): table headers, stat-card labels, badges, timestamps.

### Named Rules
**The One-Scale-Per-Surface Rule.** The public site already uses `--font-size-h1` through
`--font-size-xs` custom properties from `globals.css` consistently. The admin dashboard/statistiques
views do not — they hardcode their own px values inline (spotted in this audit: 10px, 11px, 12px, 13px,
14px, 16px, 24px, 28px, none referencing the shared CSS variables). This isn't a different *intentional*
scale, it's scale drift from dozens of views being built independently over time. The fix isn't to unify
the two surfaces onto one scale (they have genuinely different density needs), it's to give `/admin/*`
its own explicit, named scale — 11/13/14/16/24/28px, six steps, not eight-plus ad-hoc values — and
reference it consistently, the same discipline `globals.css` already models for the public site.

## 4. Elevation

Flat by default, single soft shadow on interactive cards, nothing heavier. Two shadow levels exist
across the whole app and that's the right number — don't add a third.

### Shadow Vocabulary
- **Resting** (`box-shadow: 0 1px 3px rgba(0,0,0,0.08)`): default state for every white card
  (stat cards, chart cards, table containers) across `/admin/*`.
- **Hover/Lift** (`box-shadow: 0 4px 12px rgba(0,0,0,0.12)`): hover state on clickable cards, paired
  with a small `translateY(-2px)`. Never apply this as a resting state — it should only appear as a
  response to interaction.

### Named Rules
**The No-Third-Shadow Rule.** If a new component seems to need a heavier shadow than Hover/Lift, that's
a signal the component needs a different treatment (a border, a colored surface tint), not a bigger
shadow. Two shadow levels have been sufficient for the entire admin surface so far.

## 5. Components

### Buttons
- **Shape:** 8px radius (`{rounded.md}`) is the norm; the codebase also has stray 6px/10px/20px values
  on individual buttons that should converge to 8px (or the full-pill radius for status badges/chips
  specifically, which is a legitimate distinct case, not drift).
- **Primary:** solid fill in the relevant palette color (national blue on public pages, status-info blue
  on admin), white text, 12px/24px padding.
- **Hover/Focus:** background darkens one step (e.g. `--primary-blue` → `--primary-blue-dark`); admin
  buttons additionally use the lift-shadow pattern from Elevation.
- **Secondary/Ghost:** white background, colored border and text, same radius and padding as primary.

### Cards
- **Corner Style:** 12px radius (`{rounded.lg}`) for stat cards, chart cards, and content containers.
- **Background:** white, on the `#F1F5F9`/`#F8FAFC` page canvas.
- **Shadow Strategy:** Resting at rest, Hover/Lift on interactive cards only (static display cards like
  chart containers should stay at Resting even on hover).
- **Border:** none by default. **Do not** add colored side-stripe borders as a category indicator — see
  Do's and Don'ts; this was an actual bug fixed in this audit.
- **Internal Padding:** 20px (between `{spacing.md}` and `{spacing.lg}`).

### Badges / Status Chips
- **Style:** full-pill radius (`border-radius: 20px`), tinted background at ~15-20% opacity of the
  status color, full-opacity text in that same color. This is the one legitimate use of the "20px" radius
  spotted in the audit — pill badges are a different shape category from cards/buttons, not drift.
- **State:** color alone currently carries some status meaning (e.g. photo-present ✓/✗ badges) — these
  already pair a checkmark/cross glyph with the color, which is correct; keep that pairing on any new
  status badge rather than color-only.

### Tables
- **Header:** `#F8FAFC` background, uppercase 11-12px label-weight text, `1px solid {colors.border}`
  bottom rule.
- **Rows:** white background, `1px solid #F1F5F9` row dividers, no zebra striping currently — that's a
  legitimate minimal choice for these dense operational tables, not a gap.
- **Row density:** admin tables run tighter (13-14px text, ~12px vertical cell padding) than the public
  site's 16px body text — intentional, appropriate for scan-heavy daily-use screens.

### Navigation (admin sidebar)
- Dark sidebar, role-scoped menu items, active item indicated by a left border accent
  (`border-left-color: #007FFF`) plus a background tint — this is the *legitimate* use of a left-border
  indicator (current-location signal, not decoration), distinct from the card side-stripe anti-pattern
  removed in this audit.

## 6. Do's and Don'ts

### Do:
- **Do** self-host Poppins/Roboto (already done in this audit) rather than depend on Google Fonts at
  request time — this app runs in low-bandwidth conditions where every third-party request is a real
  failure point, not a theoretical one (the Lucide-icon CDN bug found earlier this session proved it).
- **Do** keep the national palette (blue/red/yellow) confined to public-facing brand surfaces, and status
  colors (info/success/warning/danger) confined to the operational admin surface — see the Two-Palette
  Rule.
- **Do** use the pill-radius badge pattern for status chips; it's already consistent and correct.
- **Do** pair color with an icon or symbol for any status meaning (the existing ✓/✗ badge pattern is
  correct) rather than relying on color alone.

### Don't:
- **Don't** add colored `border-left`/`border-right` accent stripes to cards as a category indicator —
  fixed in this audit on `statistiques.php`'s brevet cards, where the colored icon badge already carried
  that information. If a future card needs category color-coding, use the icon-badge pattern, a background
  tint, or a leading icon — never a side stripe.
- **Don't** introduce a third shadow level. Two (Resting, Hover/Lift) have covered every real need so far.
- **Don't** hardcode new px font-size or border-radius values inline in admin views. The scale drift found
  in this audit (8+ distinct font sizes, 6+ distinct radius values on `/admin/*` alone) came from exactly
  that habit, view by view, over time.
- **Don't** load fonts, icons, or any other render-blocking asset from a third-party CDN without a strong
  reason — self-host by default given this app's real network conditions.
- **Don't** use gradient text (`background-clip: text`), decorative glassmorphism outside a single hero
  moment or modal scrim, or emoji as structural icons — audited clean this pass, keep it that way.
