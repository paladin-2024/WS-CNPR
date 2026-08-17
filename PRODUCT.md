# Product

## Register

product

## Users

Two very different user populations on one portal:

- **Ministry staff, ~14 role types** (admin, minister_admin, agent, inspecteur, gestionnaire_parking,
  operateur_saisie, validateur, receveur, instructeur, imprimeur, receptionnaire, transporteur), using
  the `/admin/*` dashboard daily to register drivers, manage vehicles, collect taxes, print/receive
  professional driver cards ("brevets"), run parking, and view statistics. High-frequency, workflow-driven
  use — speed and clarity matter more than delight.
- **Citizens and drivers**, using the public-facing pages (`/`, `/register`, `/login`, `/contact`,
  fraud-report verification pages) rarely — often on low-end phones over unreliable mobile networks in
  RDC. First impressions and low-bandwidth resilience matter here.

## Product Purpose

Digitizes driver licensing, vehicle registration, tax/payment collection, parking management, and
professional driver card ("brevet") printing for the DR Congo Ministry of Transport. Replaces a
paper-based process; success looks like ministry staff completing daily operational tasks without
friction, and citizens being able to register/verify without confusion.

## Brand Personality

Institutional and functional first. Not a consumer product chasing delight — a government operational
tool where clarity, consistency, and trustworthiness matter more than personality. The existing visual
language (white cards, 12px radius, soft shadows, blue/indigo/emerald/amber functional color coding) is
already appropriate and should be refined, not replaced.

## Anti-references

Not a flashy consumer app, not a marketing site. Avoid gimmicky animation, decorative-only color, or
anything that reads as "startup SaaS" rather than a serious institutional tool.

## Design Principles

- Consistency over cleverness: one icon set, one type scale, applied identically across all ~14 role
  dashboards and every public page.
- Function over decoration: every visual choice should aid a task (find a driver, print a card, read a
  chart) — not exist for its own sake.
- Resilient on weak networks: this session already found and fixed a real production bug where an
  unpinned third-party icon CDN silently failed to render on real-world RDC mobile connections. Prefer
  self-hosted, lightweight assets over CDN dependencies going forward.
- Legible at a glance: staff scan dense tables and dashboards all day; body text, data labels, and status
  badges must stay easily readable, not just decorative.

## Accessibility & Inclusion

Standard WCAG AA (4.5:1 body text contrast, keyboard navigable, no color-only status indicators).
Prioritize practical resilience over exhaustive AAA compliance: strong contrast and lightweight,
self-hosted assets matter more here than advanced screen-reader choreography, given the real network
conditions ministry staff and citizens are actually on.
