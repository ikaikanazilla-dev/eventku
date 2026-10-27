---
name: Eventku Digital Platform
colors:
  surface: '#f9f9ff'
  surface-dim: '#d1daf4'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f1f3ff'
  surface-container: '#e9edff'
  surface-container-high: '#e1e8ff'
  surface-container-highest: '#d9e2fc'
  on-surface: '#121b2e'
  on-surface-variant: '#434655'
  inverse-surface: '#273044'
  inverse-on-surface: '#edf0ff'
  outline: '#747687'
  outline-variant: '#c3c5d8'
  surface-tint: '#104de7'
  primary: '#094be5'
  on-primary: '#ffffff'
  primary-container: '#3867ff'
  on-primary-container: '#fffdff'
  inverse-primary: '#b7c4ff'
  secondary: '#565e71'
  on-secondary: '#ffffff'
  secondary-container: '#dbe2f9'
  on-secondary-container: '#5c6477'
  tertiary: '#5d39e5'
  on-tertiary: '#ffffff'
  tertiary-container: '#7657ff'
  on-tertiary-container: '#fffdff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde1ff'
  primary-fixed-dim: '#b7c4ff'
  on-primary-fixed: '#001552'
  on-primary-fixed-variant: '#0038b6'
  secondary-fixed: '#dbe2f9'
  secondary-fixed-dim: '#bfc6dc'
  on-secondary-fixed: '#141b2c'
  on-secondary-fixed-variant: '#3f4759'
  tertiary-fixed: '#e6deff'
  tertiary-fixed-dim: '#c9beff'
  on-tertiary-fixed: '#1b0063'
  on-tertiary-fixed-variant: '#4612cf'
  background: '#f9f9ff'
  on-background: '#121b2e'
  surface-variant: '#d9e2fc'
typography:
  display-hero:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.03em
  display-hero-mobile:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-xl:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.025em
  headline-xl-mobile:
    fontFamily: Inter
    fontSize: 26px
    fontWeight: '700'
    lineHeight: 34px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
    letterSpacing: -0.01em
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: -0.005em
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0em
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.005em
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.04em
  mono-data:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 18px
    letterSpacing: -0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-sm: 1rem
  margin: 2rem
  margin-sm: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system establishes a high-performance visual language for a modern Indonesian digital event management and ticketing SaaS platform. The aesthetic merges enterprise-grade trust with the vibrant energy of live experiences, balancing Swiss-style structural precision with subtle futuristic details.

The visual style is **Modern Corporate with Tactile Digital Refinements**. It relies on deep architectural navies anchored by high-luminance electric blues and ambient violet glows. Visual noise is minimized through disciplined white space, crisp hairline borders, and layered planar elevation, avoiding unnecessary decorative clutter in favor of legible data visualization, actionable analytics, and intuitive ticket transaction flows.

Target users include enterprise conference organizers, high-volume music festival promoters, and tech-forward attendees across Indonesia's rapidly digitizing events sector. The interface must communicate institutional reliability, absolute transaction security, and cultural vibrancy.

## Colors

The palette uses deliberate contrast ratios meeting WCAG 2.1 AA/AAA compliance across administrative dashboards and attendee checkout funnels.

- **Primary Brand (`#3867FF`)**: Used for primary calls-to-action, key interactive states, checkout steps, active nav items, and essential brand focus rings.
- **Deep Navy & Midnight (`#101828`, `#142D4E`)**: Provides authoritative grounding. Applied across sidebars, structural page headers, inverted stat cards, and dark surface modes.
- **Accent Violet (`#7657FF`)**: Injects energy into premium tiers, VIP status tags, featured event badges, and subtle gradient highlights paired with the electric blue.
- **Canvas & Surface Layering**: 
  - Base canvas: `#F7F8FC`
  - High surface: `#FFFFFF`
  - Border hairline: `#E4E7EC`
- **Typography Tokens**:
  - Primary text: `#172033` (high contrast, crisp readability)
  - Secondary text: `#667085` (metadata, helper text, empty states)
- **Functional Semantics**:
  - Success: `#12B76A` (confirmed tickets, successful payouts, valid check-ins)
  - Warning: `#F79009` (low ticket inventory, pending gate validations)
  - Error: `#F04438` (failed transactions, invalid scan alerts, destructive actions)

## Typography

The type system is powered entirely by Inter to maintain crisp legibility across data-dense tables, ticketing checkout steps, and mobile scanner viewports.

Headline styles deploy tighter letter-spacing and robust weights (`700` and `800`) to create strong focal points for event titles and key financial figures. Body typography retains natural proportions and standard tracking to ensure scanning speed. Labels and ticket data utilize medium-to-bold weights with slight optical tracking for micro-elements such as badge pills, currency codes (`IDR / Rp`), time stamps, and verification indicators.

## Layout & Spacing

The layout is built on an 8pt spatial grid system with a responsive 12-column structure on desktop, an 8-column layout on tablet, and a 4-column layout on mobile devices.

- **Desktop (1280px and above)**: 12-column grid, max-width 1360px centered, 32px (`margin`) outer gutters, 24px (`gutter`) column gutters.
- **Tablet (768px – 1279px)**: 8-column grid, 24px outer margins, 16px column gutters. Collapsible side navigation shifts to an off-canvas drawer.
- **Mobile (below 768px)**: 4-column grid, 16px outer margins, 12px column gutters. Complex grid tables collapse into stacked cards with sticky action bars pinned to the bottom viewport.
- **Rhythm**: Internal card padding alternates strictly between `space-md` (16px) for compact widgets and `space-lg` (24px) for expansive analytical and checkout surfaces.

## Elevation & Depth

Visual hierarchy uses layered ambient shadows and subtle hairline borders rather than heavy drop shadows. Surfaces feel light, floating, and precise.

- **Level 0 (Base Surface)**: `#F7F8FC` canvas with no shadow.
- **Level 1 (Card & Content Surface)**: `#FFFFFF` background with a subtle border (`1px solid #E4E7EC`) and a soft diffused drop: `0px 1px 3px rgba(16, 24, 40, 0.05), 0px 1px 2px rgba(16, 24, 40, 0.03)`.
- **Level 2 (Interactive Floating & Hover States)**: `0px 4px 12px -2px rgba(16, 24, 40, 0.06), 0px 2px 6px -1px rgba(16, 24, 40, 0.04)`. Border color darkens slightly to `#D0D5DD`.
- **Level 3 (Modals, Slide-overs & Flyouts)**: `0px 12px 32px -4px rgba(16, 24, 40, 0.12), 0px 4px 12px -2px rgba(16, 24, 40, 0.06)`. Backdrops use `rgba(16, 24, 40, 0.6)` with a 4px blur (`backdrop-filter: blur(4px)`).
- **Brand Glow Elevation (Primary Focus / VIP elements)**: `0px 0px 0px 4px rgba(56, 103, 255, 0.14)`.

## Shapes

The interface balances sharp digital precision with organic tactile corners:
- **Buttons, Text Inputs, and Dropdowns**: Standardized with a moderate radius of 8px to 10px (`0.5rem` to `0.625rem`) for high tap targets and visual balance.
- **Cards, Modals, and Dashboard Containers**: Set between 12px and 16px (`0.75rem` to `1rem`), providing a contained, refined frame.
- **Badges, Tags, and Status Pills**: Fully circular/pill-shaped (`border-radius: 9999px`) to maintain contrast against rectangular containers.
- **Ticket Stub Notches**: Physical ticket metaphor featuring opposing semi-circular cutouts (12px radius) along coupon seams or divider hairlines, backed by a dashed separator (`border-style: dashed; border-color: #E4E7EC`).

## Components

### Buttons
- **Primary**: Solid `#3867FF` fill, `#FFFFFF` text, 9px radius, medium weight. Hover shifts to `#2A54E8` with `0px 2px 8px rgba(56, 103, 255, 0.28)`.
- **Secondary / Outline**: `#FFFFFF` fill, 1px `#E4E7EC` border, `#172033` text. Hover changes to `#F9FAFB` fill and `#D0D5DD` border.
- **Ghost**: Transparent fill, `#667085` text; hover reveals `#F2F4F7` background with `#172033` text.
- **Destructive**: Solid `#F04438` fill or transparent with `#F04438` text and soft error tint background on hover.

### Form Inputs & Selectors
- Height: 44px for standard desktop targets, 48px for mobile screens.
- Resting state: `#FFFFFF` fill, 1px `#E4E7EC` border, `#172033` text, `#98A2B3` placeholder text, 8px radius.
- Active / Focus: Border changes to `#3867FF` with a continuous 3px outer glow ring (`rgba(56, 103, 255, 0.15)`).

### Cards & Stat Widgets
- Pure white background (`#FFFFFF`), 1px solid border (`#E4E7EC`), 12px to 16px radius, padding of 20px or 24px.
- Stat cards feature top-aligned label metadata, large bold metric numbers (`headline-lg`), and inline pill badges displaying trending percentages (emerald for positive growth, coral for decline).

### Digital Tickets & Notched Containers
- Dual-segment card design: Left/Top section contains event visual, name, date, and gate details; Right/Bottom segment holds QR code, barcode, and seat allocation.
- Junction contains complementary inward-curved circular notches (16px diameter) on both borders, connected by a 1px dashed divider line (`#D0D5DD`).

### Status Badges & Chips
- Form factor: 24px height, pill-shaped (`9999px`), 12px typography (`label-md`).
- Success (Confirmed / Checked In): Background `#ECFDF3`, Text `#027A48`, optional 6px pulsing emerald indicator dot.
- Warning (Reserved / Expiring): Background `#FEF0C7`, Text `#B54708`.
- Error (Cancelled / Void): Background `#FEF3F2`, Text `#B42318`.
- Neutral (Draft): Background `#F2F4F7`, Text `#344054`.

### Navigation & Segmented Tabs
- Container: 6px padded bar in `#F2F4F7`, 10px outer radius.
- Tab triggers: Transparent background, `#667085` text. Active tab switches to solid `#FFFFFF` card with soft elevation (`0px 1px 3px rgba(16, 24, 40, 0.1)`), `#101828` bold text, and 8px corner radius.