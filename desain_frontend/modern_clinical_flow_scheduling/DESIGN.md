---
name: Modern Clinical Flow & Scheduling
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#3e4947'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#6e7977'
  outline-variant: '#bdc9c6'
  surface-tint: '#006a63'
  primary: '#005c55'
  on-primary: '#ffffff'
  primary-container: '#0f766e'
  on-primary-container: '#a3faef'
  inverse-primary: '#80d5cb'
  secondary: '#006a61'
  on-secondary: '#ffffff'
  secondary-container: '#86f2e4'
  on-secondary-container: '#006f66'
  tertiary: '#7d4200'
  on-tertiary: '#ffffff'
  tertiary-container: '#a15600'
  on-tertiary-container: '#ffe6d5'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9cf2e8'
  primary-fixed-dim: '#80d5cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#00504a'
  secondary-fixed: '#89f5e7'
  secondary-fixed-dim: '#6bd8cb'
  on-secondary-fixed: '#00201d'
  on-secondary-fixed-variant: '#005049'
  tertiary-fixed: '#ffdcc3'
  tertiary-fixed-dim: '#ffb77d'
  on-tertiary-fixed: '#2f1500'
  on-tertiary-fixed-variant: '#6e3900'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
  surface-canvas: '#F8FAFC'
  surface-card: '#FFFFFF'
  status-available: '#059669'
  status-limited: '#D97706'
  status-full: '#DC2626'
  brand-navy: '#082747'
  brand-gold: '#FBBA15'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.015em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
    letterSpacing: -0.005em
  title-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
    letterSpacing: 0.03em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  space-2xs: 0.25rem
  space-xs: 0.5rem
  space-sm: 0.75rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
  space-2xl: 3rem
  space-3xl: 4rem
  gutter-mobile: 1rem
  gutter-desktop: 1.5rem
  container-max: 75rem
---

## Brand & Style

The design system establishes an atmosphere of clinical authority, modern hospitality, and empathetic calm. Targeted at patients, families, and healthcare staff navigating critical scheduling moments, the aesthetic counters typical medical anxiety with pristine organization, soft airiness, and unambiguous clarity. 

Rooted in a **Modern Clinical Clean** aesthetic, the design blends hospital-grade precision with the approachable warmth of premium wellness hospitality. Layouts emphasize open breathing room, structured data hierarchy, reassuring micro-interactions, and instant legibility across ambient, high-stress environments. Surfaces feel crisp yet gentle, avoiding harsh sterile lines through softly contoured geometry and measured warmth.

## Colors

The palette grounds the interface in restorative, trustworthy tones. 

- **Primary (`#0F766E`) & Secondary (`#0D9488`):** Deep emerald and calming teal convey hygiene, scientific rigour, and psychological restoration. They command primary navigational cues, confirmation badges, active session states, and critical actions.
- **Tertiary (`#D97706`) & Accent Gold (`#FBBA15`):** Warm amber accents represent urgency control, VIP/premium clinic tags, and critical warnings without invoking panic.
- **Canvas & Surfaces (`#F8FAFC` & `#FFFFFF`):** High-key clinical white surfaces hover above an ultra-pale slate backdrop, avoiding eye fatigue and keeping schedule tables easy to parse.
- **Semantic Statuses:** `status-available` (#059669) denotes open slots; `status-limited` (#D97706) highlights remaining quotas (< 3 slots); `status-full` (#DC2626) signals closed queues clearly and decisively.

## Typography

Typography prioritizes effortless scanability under rapid decision-making:

- **Display & Headlines (Plus Jakarta Sans):** Offers geometric balance and friendly, modern humanism. Curved apexes and generous counters make department names and doctor titles instantly warm and approachable.
- **Body & Numerical Data (Inter):** Provides maximum legibility for dense tabular scheduling, time slots, quota figures, and medical instructions. Strict vertical proportioning and neutral tracking ensure tabular numerals stay aligned in queue countdowns.

## Layout & Spacing

The layout is built on an **8pt modular baseline** and a responsive fluid grid:

- **Mobile (under 768px):** 4-column layout with 16px side margins (`gutter-mobile`) and 12px horizontal gutters. Booking panels and quota states collapse into stacked cards with sticky bottom booking action bars.
- **Tablet (768px - 1024px):** 8-column layout with 24px margins. Doctor cards switch to a dual-pane format with doctor bio on the left and session matrix on the right.
- **Desktop (1024px+):** 12-column system capped at `container-max` (1200px / 75rem) with 24px gutters. Schedule matrices span 8 columns alongside a persistent 4-column live queue tracker and quick booking drawer.

## Elevation & Depth

Visual hierarchy uses **tonal layering and tinted atmospheric shadows** rather than heavy drop shadows, reinforcing sterile purity and calm:

- **Base Canvas:** Neutral `#F8FAFC` background.
- **Layer 0 (Flat Container):** Surface `#FFFFFF` with a 1px border of `#E2E8F0` for schedule filters and calendar day selectors.
- **Layer 1 (Resting Doctor Card):** `box-shadow: 0 1px 3px 0 rgba(15, 118, 110, 0.04), 0 1px 2px -1px rgba(15, 118, 110, 0.04)` with a subtle stroke of `#F1F5F9`.
- **Layer 2 (Hover / Active Slot Selection):** `box-shadow: 0 8px 20px -4px rgba(15, 118, 110, 0.08), 0 4px 6px -2px rgba(15, 118, 110, 0.03)`.
- **Layer 3 (Modals / Booking Sliders):** `box-shadow: 0 20px 25px -5px rgba(8, 39, 71, 0.12), 0 8px 10px -6px rgba(8, 39, 71, 0.08)`.

## Shapes

The design uses **Rounded (`roundedness: 2`)** geometry to convey empathy, clinical precision, and approachability:

- **Base Radius (0.5rem / 8px):** Session slot chips, input elements, day filters, and dropdown menus.
- **Card Radius (1rem / 16px):** Doctor directory cards, quota summary panels, and booking summaries.
- **Modal & Surface Radius (1.5rem / 24px):** Bottom sheets, main booking modals, and queue status banners.
- **Doctor Avatars:** Squircle with a 16px radius (`rounded-lg`) or full circular frames (`rounded-full`) with a 2px inner border of `#0D9488/20`.

## Components

### Buttons
- **Primary Action (Book Appointment):** Solid `#0F766E` background, white text (`label-lg`), 8px border radius, 48px height. Active state transitions to `#115E59`.
- **Secondary Action (View Profile / Switch Day):** Subtle teal tint `#F0FDFA` with `#0F766E` text and 1px border `#CCFBF1`.
- **Disabled State (Slot Full):** `#F1F5F9` background, `#94A3B8` text, cursor not-allowed, zero shadow.

### Doctor Schedule Card
- **Header:** Doctor portrait (80x80px rounded squircle) paired with medical title, sub-specialty badge (e.g., "Spesialis Jantung & Pembuluh Darah"), and clinic room number.
- **Schedule Ribbon:** Horizontal timeline divided into daily chunks (Senin - Sabtu). Days with active practice display active green indicators.
- **Session Chips:** Micro-cards displaying session ranges (e.g., "Sesi 1: 08:00 - 11:00") and quota pill tags.
- **Real-Time Quota Badge:** Pill tag with indicator dot:
  - *Tersedia (Quota > 3):* `#ECFDF5` background, `#047857` text.
  - *Sisa Sedikit (Quota 1–3):* `#FFFBEB` background, `#B45309` text.
  - *Penuh (Quota 0):* `#FEF2F2` background, `#B91C1C` text.
- **CTA:** Right-aligned direct booking button triggering the immediate appointment intake flow.

### Form Inputs & Search
- Specialty and doctor search bars feature leading medical search icons, 44px height, `#FFFFFF` fill, and `#CBD5E1` borders that transition to a 2px `#0D9488` ring on focus.

### Chips & Day Filters
- Segmented pills allow day-by-day scheduling switches. Active day features `#0F766E` fill with bold white typography, while inactive days rest on clean slate `#F8FAFC`.

### Real-Time Live Queue Display
- Floating or fixed high-contrast widget for outpatient queues: shows Current Called Number (Nomor Antrean Dipanggil) in large monospaced Inter numerals alongside estimated waiting times.