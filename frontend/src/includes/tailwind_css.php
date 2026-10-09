<style type="text/tailwindcss">
/* ===== consolidated from assets/css/style.css ===== */
/* ==========================================================================
   APP SHELL - STATIC SIDEBAR / SCROLLING MAIN (kept from live frontend)
   header.php locks <body> to viewport height so only <main> scrolls.
   Legacy customer pages wrap sidebar+main in .app-container.
   ========================================================================== */
.app-container { display: flex; flex: 1; min-width: 0; height: 100%; overflow: hidden; }
.main-content { flex: 1 1 0%; min-width: 0; overflow-y: auto; }
/* ==========================================================================
   PRIORITY HANDLING LOGISTICS SALES AGENT PORTAL - CUSTOM STYLES & ANIMATIONS
   ========================================================================== */

/* Scroll Reveal Fade Animation */
.reveal {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

.reveal.active {
    opacity: 1;
    transform: translateY(0);
}

/* Pulsing Glowing Indicator Animation */
@keyframes pulseGlow {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(29, 46, 106, 0.7);
    }
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(29, 46, 106, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(29, 46, 106, 0);
    }
}

.animate-pulse-glow {
    animation: pulseGlow 2s infinite;
}

/* Leaflet Dark Map Overrides */
.leaflet-container {
    background: #000c1a !important;
}

.leaflet-popup-content-wrapper {
    border-radius: 10px !important;
    padding: 2px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
}

/* Modal Open Pointer Utilities */
.modal-open {
    opacity: 1 !important;
    pointer-events: auto !important;
}

/* ==========================================================================
   DARK MODE NORMALIZATION
   Dashboards and the chat widget (plus JS-rendered tables, timeline and
   chat bubbles) were authored with hardcoded light utilities (bg-white,
   text-slate-900, border-gray-200, bg-[#f4f7fa], bg-gray-200, etc.) and no
   dark: variants, so they stayed light in dark mode. This block remaps
   those generic light utilities to the dark palette whenever <html>.dark
   is present.

   Intentionally NO !important: pages that already ship their own dark:
   variants (e.g. settings.php) keep their styling because Tailwind's
   generated dark: utilities are injected after this file and win on equal
   specificity.
   ========================================================================== */


/* 1. Page / main canvas -------------------------------------------------- */
html.dark body { background-color: var(--navy-950, #0a0f24); color: #dbe1f1; }
html.dark .bg-\[#f4f7fa\] { background-color: var(--navy-950, #0a0f24); }

/* 2. Generic white cards / containers ------------------------------------ */
html.dark .bg-white { background-color: var(--navy-850, #131d43); }
html.dark .border-gray-200,
html.dark .border-gray-200\/80 { border-color: rgba(148, 163, 184, 0.18); }

/* 3. Neutral light backgrounds (slate / stone / gray) -------------------- */
html.dark .bg-slate-50,
html.dark .bg-slate-50\/30,
html.dark .bg-slate-50\/50,
html.dark .bg-slate-50\/70 { background-color: var(--navy-800, #182554); }
html.dark .bg-slate-100,
html.dark .bg-slate-100\/70,
html.dark .bg-slate-100\/80 { background-color: var(--navy-850, #131d43); }
html.dark .bg-slate-200,
html.dark .bg-slate-200\/50 { background-color: var(--navy-700, #1d2e6a); }
html.dark .bg-stone-200 { background-color: var(--navy-700, #1d2e6a); }
html.dark .bg-gray-50 { background-color: var(--navy-800, #182554); }
html.dark .bg-gray-200 { background-color: var(--navy-600, #2b3f7c); }

/* 4. Form inputs / selects authored with light fills --------------------- */
html.dark input.bg-slate-50,
html.dark select.bg-slate-50,
html.dark input.bg-gray-50,
html.dark select.bg-gray-50,
html.dark input.bg-white,
html.dark select.bg-white,
html.dark textarea.bg-slate-50,
html.dark textarea.bg-white { background-color: var(--navy-800, #182554); border-color: rgba(148, 163, 184, 0.25); color: #e2e8f0; }
html.dark input.bg-slate-50::placeholder,
html.dark input.bg-gray-50::placeholder,
html.dark textarea.bg-slate-50::placeholder { color: #64748b; }

/* 5. Borders & dividers -------------------------------------------------- */
html.dark .border-gray-100 { border-color: rgba(148, 163, 184, 0.14); }
html.dark .border-gray-200 { border-color: rgba(148, 163, 184, 0.18); }
html.dark .border-gray-200\/80 { border-color: rgba(148, 163, 184, 0.18); }
html.dark .border-gray-300,
html.dark .border-gray-300\/80 { border-color: rgba(148, 163, 184, 0.28); }
html.dark .border-slate-100 { border-color: rgba(148, 163, 184, 0.14); }
html.dark .border-slate-200,
html.dark .border-slate-200\/80 { border-color: rgba(148, 163, 184, 0.18); }
html.dark .border-slate-300 { border-color: rgba(148, 163, 184, 0.28); }
html.dark .divide-gray-100 > * + * { border-color: rgba(148, 163, 184, 0.14); }
html.dark .divide-gray-200 > * + * { border-color: rgba(148, 163, 184, 0.18); }
html.dark .divide-slate-100 > * + *,
html.dark .divide-y.divide-slate-100 > * + * { border-color: rgba(148, 163, 184, 0.14); }

/* 6. Neutral text (slate / gray) -----------------------------------------
   Each step keeps its own value so the heading > body > muted hierarchy
   survives in dark mode. Previously 700/600/500/400 all collapsed to one
   identical grey, which flattened every text tier into the same weight. */
html.dark .text-slate-900 { color: #eef1f9; }
html.dark .text-slate-800 { color: #dbe1f1; }
html.dark .text-slate-700 { color: #c3cbe0; }
html.dark .text-slate-600 { color: #b7c1da; }
html.dark .text-slate-500 { color: #a2aecb; }
html.dark .text-slate-400 { color: #8b96b3; }
html.dark .text-gray-700 { color: #c3cbe0; }
html.dark .text-gray-600 { color: #b7c1da; }
html.dark .text-gray-500 { color: #a2aecb; }
html.dark .text-gray-400 { color: #8b96b3; }
html.dark .text-gray-50 { color: #dbe1f1; }
html.dark .text-slate-50 { color: #dbe1f1; }

/* 7. Colored status pills -> dark same-hue tints (preserve hue coding) --- */
html.dark .bg-blue-50 { background-color: #142540; }
html.dark .bg-blue-100,
html.dark .bg-blue-100\/70 { background-color: #172a4d; }
html.dark .text-blue-600 { color: #60a5fa; }
html.dark .text-blue-700 { color: #93c5fd; }
html.dark .border-blue-50 { border-color: rgba(59, 130, 246, 0.35); }
html.dark .border-blue-100 { border-color: rgba(59, 130, 246, 0.40); }

html.dark .bg-emerald-50 { background-color: #0f2a22; }
html.dark .bg-emerald-100 { background-color: #123025; }
html.dark .text-emerald-600 { color: #34d399; }
html.dark .text-emerald-700 { color: #6ee7b7; }
html.dark .border-emerald-50 { border-color: rgba(16, 185, 129, 0.35); }
html.dark .border-emerald-100 { border-color: rgba(16, 185, 129, 0.40); }

html.dark .bg-red-50 { background-color: #2a1414; }
html.dark .bg-red-100 { background-color: #311818; }
html.dark .text-red-600 { color: #f87171; }
html.dark .text-red-700 { color: #fca5a5; }
html.dark .border-red-50 { border-color: rgba(239, 68, 68, 0.35); }

html.dark .bg-rose-50 { background-color: #2a141c; }
html.dark .bg-rose-100 { background-color: #311822; }
html.dark .text-rose-600 { color: #fb7185; }
html.dark .text-rose-700 { color: #fda4af; }

html.dark .bg-amber-50 { background-color: #2a2110; }
html.dark .bg-amber-100 { background-color: #312715; }
html.dark .text-amber-600 { color: #fbbf24; }
html.dark .text-amber-700 { color: #fcd34d; }
html.dark .border-amber-200\/60 { border-color: rgba(245, 158, 11, 0.40); }

html.dark .bg-sky-50 { background-color: #0c2233; }
html.dark .bg-sky-100 { background-color: #0f2a40; }
html.dark .text-sky-600 { color: #38bdf8; }
html.dark .text-sky-700 { color: #7dd3fc; }

/* 8. Hover states -------------------------------------------------------- */
html.dark .hover\:bg-slate-50:hover,
html.dark .hover\:bg-slate-50\/50:hover { background-color: #16284a; }
html.dark .hover\:bg-slate-100:hover { background-color: #16284a; }
html.dark .hover\:bg-slate-200:hover { background-color: #1b2c4d; }
html.dark .hover\:bg-gray-50:hover { background-color: #16284a; }
html.dark .hover\:bg-blue-50:hover,
html.dark .hover\:bg-blue-50\/40:hover,
html.dark .hover\:bg-blue-50\/50:hover { background-color: #1a3056; }
html.dark .hover\:bg-blue-100:hover { background-color: #1f3a66; }
html.dark .hover\:text-slate-600:hover { color: #dbe1f1; }
html.dark .hover\:text-amber-700:hover { color: #fcd34d; }
html.dark .hover\:text-emerald-600:hover { color: #34d399; }
html.dark .hover\:text-emerald-700:hover { color: #6ee7b7; }
html.dark .hover\:text-red-400:hover { color: #fca5a5; }

/* 9. Placeholder / ring extras ------------------------------------------- */
html.dark .placeholder-slate-400::placeholder,
html.dark .placeholder-slate-50::placeholder { color: #64748b; }
html.dark .ring-blue-50 { --tw-ring-color: rgba(59, 130, 246, 0.35); }
html.dark .ring-emerald-50 { --tw-ring-color: rgba(16, 185, 129, 0.35); }


/* ==========================================================================
   10. ACCENT THEMES
   --------------------------------------------------------------------------
   tailwind_config.php maps brand.blue / brand.darkblue to these custom
   properties, so every bg-brand-blue / text-brand-blue / border-brand-blue
   utility in the portal retints when <html data-accent="..."> changes.
   Values are space-separated RGB channels for use inside rgb().
   Default is the Priority PH brand navy (#1D2E6A) with its button navy
   (#213854) as the strong step.
   ========================================================================== */

:root {
    --accent-rgb: 29 46 106;           /* priority navy   #1d2e6a */
    --accent-strong-rgb: 33 56 84;     /* priority button #213854 */
}

/* Priority navy is the default, but the swatch is still selectable. */
html[data-accent="navy"] {
    --accent-rgb: 29 46 106;
    --accent-strong-rgb: 33 56 84;
}

html[data-accent="violet"] {
    --accent-rgb: 124 58 237;
    --accent-strong-rgb: 109 40 217;
}

html[data-accent="emerald"] {
    --accent-rgb: 5 150 105;
    --accent-strong-rgb: 4 120 87;
}

html[data-accent="amber"] {
    --accent-rgb: 217 119 6;
    --accent-strong-rgb: 180 83 9;
}

html[data-accent="rose"] {
    --accent-rgb: 220 38 38;
    --accent-strong-rgb: 185 28 28;
}

/* Blue is the default, but the swatch is still selectable, so declare it. */
html[data-accent="blue"] {
    --accent-rgb: 0 102 255;
    --accent-strong-rgb: 0 82 204;
}

/* Accent-tinted shadows follow the active accent. */
html[data-accent] .shadow-blue-500\/20 {
    --tw-shadow-color: rgb(var(--accent-rgb) / 0.2);
    --tw-shadow: var(--tw-shadow-colored);
}


/* ==========================================================================
   11. COMPACT DENSITY
   --------------------------------------------------------------------------
   Opt-in via <html data-density="compact"> (Appearance settings).
   Uses rem-based overrides so it stays predictable next to Tailwind's
   arbitrary px utilities, and only touches the shared structural patterns
   (cards, tables, headers, inputs) rather than every single utility.
   ========================================================================== */

html[data-density="compact"] {
    --density-scale: 0.82;
}

html[data-density="compact"] body {
    font-size: 0.8125rem;
}

/* Tighten vertical rhythm on the common stacking containers. */
html[data-density="compact"] .space-y-1 > * + * { margin-top: 0.1875rem; }
html[data-density="compact"] .space-y-2 > * + * { margin-top: 0.375rem; }
html[data-density="compact"] .space-y-3 > * + * { margin-top: 0.5625rem; }
html[data-density="compact"] .space-y-4 > * + * { margin-top: 0.75rem; }
html[data-density="compact"] .space-y-5 > * + * { margin-top: 0.9375rem; }
html[data-density="compact"] .space-y-6 > * + * { margin-top: 1.125rem; }
html[data-density="compact"] .space-y-8 > * + * { margin-top: 1.5rem; }

/* Card / panel padding. */
html[data-density="compact"] .p-4  { padding: 0.6875rem; }
html[data-density="compact"] .p-5  { padding: 0.8125rem; }
html[data-density="compact"] .p-6  { padding: 1rem; }
html[data-density="compact"] .p-8  { padding: 1.25rem; }
html[data-density="compact"] .px-4 { padding-left: 0.6875rem; padding-right: 0.6875rem; }
html[data-density="compact"] .px-5 { padding-left: 0.8125rem; padding-right: 0.8125rem; }
html[data-density="compact"] .px-6 { padding-left: 1rem; padding-right: 1rem; }
html[data-density="compact"] .px-8 { padding-left: 1.25rem; padding-right: 1.25rem; }
html[data-density="compact"] .py-2   { padding-top: 0.375rem; padding-bottom: 0.375rem; }
html[data-density="compact"] .py-2\.5 { padding-top: 0.46875rem; padding-bottom: 0.46875rem; }
html[data-density="compact"] .py-3   { padding-top: 0.5625rem; padding-bottom: 0.5625rem; }
html[data-density="compact"] .py-4   { padding-top: 0.75rem; padding-bottom: 0.75rem; }
html[data-density="compact"] .py-6   { padding-top: 1rem; padding-bottom: 1rem; }
html[data-density="compact"] .sm\:p-8 { padding: 1.25rem; }

/* Page + header chrome. */
html[data-density="compact"] .p-6.lg\:p-8,
html[data-density="compact"] .p-8 { padding-top: 1rem; padding-bottom: 1rem; }
html[data-density="compact"] header { padding-top: 0.625rem; padding-bottom: 0.625rem; }

/* Table cells. */
html[data-density="compact"] td,
html[data-density="compact"] th {
    padding-top: 0.4375rem;
    padding-bottom: 0.4375rem;
}

/* Slightly smaller headings so dense screens do not feel oversized. */
html[data-density="compact"] h1 { font-size: 1.375rem; }
html[data-density="compact"] h2 { font-size: 1.125rem; }
html[data-density="compact"] h3 { font-size: 0.9375rem; }
/* ===== consolidated from assets/css/view_leads_modal.css ===== */
/* Real entrance animations */
@keyframes lmBackdropIn { 
  from { opacity: 0; } 
  to { opacity: 1; } 
}

@keyframes lmPanelIn {
  from { opacity: 0; transform: translateY(18px) scale(.96); }
  to   { opacity: 1; transform: translateY(0)    scale(1);   }
}

#viewModal:not(.hidden) { 
  animation: lmBackdropIn .25s ease-out both; 
}

#viewModal:not(.hidden) > .lm-panel { 
  animation: lmPanelIn .32s cubic-bezier(.22,1,.36,1) both; 
}

/* Pipeline stepper */
.lm-step { 
  position: relative; 
}

.lm-step::before {
  content: ""; 
  position: absolute; 
  left: 50%; 
  top: 13px; 
  transform: translateX(-50%);
  width: 100%; 
  height: 2px; 
  background: #e2e8f0; 
  z-index: 0;
}

.lm-step:last-child::before { 
  display: none; 
}

.lm-dot {
  position: relative; 
  z-index: 1; 
  width: 26px; 
  height: 26px; 
  border-radius: 9999px;
  display: flex; 
  align-items: center; 
  justify-content: center; 
  font-size: 11px; 
  font-weight: 800;
  background: #f1f5f9; 
  color: #94a3b8; 
  border: 2px solid #e2e8f0; 
  transition: all .25s ease;
}

.lm-step.is-done .lm-dot { 
  background: #ede9fe; 
  color: #7c3aed; 
  border-color: #c4b5fd; 
}

.lm-step.is-active .lm-dot {
  background: linear-gradient(135deg,#7c3aed,#6366f1); 
  color: #fff; 
  border-color: #a78bfa;
  box-shadow: 0 0 0 4px rgba(124,58,237,.18);
}

.lm-step.is-done::before, 
.lm-step.is-active::before { 
  background: #c4b5fd; 
}

.lm-label { 
  font-size: 9.5px; 
  line-height: 1.1; 
}
/* ===== consolidated from assets/css/theme.css ===== */
/* ==========================================================================
   CRM DESIGN TOKENS  —  "PRIORITY NAVY"
   --------------------------------------------------------------------------
   Single source of truth for the portal's modernised look. Inlined by
   includes/header.php inside a "text/tailwindcss" style block that the
   Tailwind Play CDN processes after its base + utilities layers.

   Palette anchored on the Priority PH brand navy (#1D2E6A) with its deep
   teal-navy companions (#003A5F / #084163) and soft brand blue (#4E83C5).

   Scheme: the sidebar navy is the brand surface, everything else is white.
   Navy owns STRUCTURE (rail, buttons, headings, table heads, chrome) while
   the existing --accent-rgb picker (style.css -> ACCENT THEMES) keeps
   owning LINKS and FOCUS rings so affordances survive.

   Component classes (.crm-*) are plain CSS, so they are immune to the
   Tailwind Play CDN's class scanner and work inside JS-injected markup.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. TOKENS
   -------------------------------------------------------------------------- */
:root {
    /* Navy scale - anchored on the Priority PH brand navy #1D2E6A */
    --navy-950: #0a0f24;   /* deepest: rail base, page footer, overlays   */
    --navy-900: #0f1734;   /* sidebar surface                             */
    --navy-850: #131d43;   /* raised dark surface                         */
    --navy-800: #182554;   /* headings, primary buttons                   */
    --navy-700: #1d2e6a;   /* BRAND ANCHOR - strong secondary text        */
    --navy-600: #2b3f7c;   /* labels                                      */
    --navy-500: #3d5494;   /* body emphasis                               */
    --navy-400: #5f74b5;   /* subdued text                                */
    --navy-300: #8d9dd0;   /* muted / placeholder                         */
    --navy-200: #b9c4e3;   /* disabled strokes                            */
    --navy-100: #dbe1f1;   /* dividers on white                           */
    --navy-50:  #eef1f9;   /* zebra / chip fills                          */

    /* Priority PH brand extensions */
    --brand-navy:  #1d2e6a;   /* site brand navy                          */
    --brand-button: #213854;  /* button navy from priority-ph.com         */
    --teal-700:    #003a5f;   /* deep teal-navy - gradient stop           */
    --teal-600:    #084163;   /* deep teal-navy - gradient stop           */
    --sky-brand:   #4e83c5;   /* soft brand blue - accents, charts        */
    --brand-violet: #290f74;  /* the actual logo mark colour, sampled from
                                 assets/image/logo.png - reserved for the
                                 brand mark and AI/insight surfaces.       */
    --brand-grad:  linear-gradient(120deg, var(--brand-navy) 0%, var(--teal-600) 55%, var(--teal-700) 100%);

    /* Text tiers that sit ON the navy rail / dark surfaces. Kept as tokens
       so the sidebar never drifts from the rest of the navy family. */
    --on-navy-strong: #e6ebfa;   /* active pill fill, highest emphasis    */
    --on-navy-200:    #b9c4e3;   /* count badges                          */
    --on-navy-300:    #a8b4d4;   /* idle nav labels                       */
    --on-navy-400:    #8d9bc0;   /* icon tiles, secondary nav text        */
    --on-navy-500:    #6f7fa8;   /* brand badge                           */
    --on-navy-600:    #7385b0;   /* section group headers (lowest tier)   */

    /* Light canvas - cool tint of the Priority #F0F0F0 surface */
    --canvas:        #f2f4f9;
    --surface:       #ffffff;
    --surface-muted: #eef1f7;
    --surface-hover: #e7ecf6;
    --line:          #dfe3ee;
    --line-strong:   #c9d0e2;

    /* Text roles - Priority near-black + AA-safe muted grey */
    --fg-heading: #1b1b1c;
    --fg-body:    #3d4451;
    --fg-muted:   #6e6e6e;   /* 4.6:1 on white - passes AA for body text  */

    /* Brand + focus (follows the accent picker) */
    --brand:        rgb(var(--accent-rgb));
    --brand-strong: rgb(var(--accent-strong-rgb));
    --ring:         rgb(var(--accent-rgb) / 0.28);

    /* Elevation - shadows tinted with the brand navy, not neutral black */
    --shadow-card: 0 1px 2px rgba(29, 46, 106, .05), 0 1px 3px rgba(29, 46, 106, .07);
    --shadow-lift: 0 12px 32px -14px rgba(29, 46, 106, .24);
    --shadow-navy: 0 8px 20px -10px rgba(29, 46, 106, .55);

    /* Geometry */
    --radius-card: 1rem;
    --radius-ctl:  .625rem;
    --radius-pill: 999px;

    /* ------------------------------------------------------------------
       Priority brand extensions - pipeline stage semantics
       ------------------------------------------------------------------
       Replaces the purple / violet / amber "stage soup" that was hardcoded
       in dashboard.php + bi_analytics.php. The five stages form a monotone
       lightness ramp through the navy family (light -> dark = early -> late
       in the funnel), so the series stay distinguishable at 2px chart-line
       weight while reading as one brand. Won/lost keep their universal
       green/red so the terminal states still pop out of the ramp. */
    --stage-new:         #8d9dd0;   /* navy-300  */
    --stage-qualifying:  #4e83c5;   /* sky-brand */
    --stage-quote:       #2b3f7c;   /* navy-600  */
    --stage-negotiation: #1d2e6a;   /* navy-700 - brand anchor */
    --stage-won:         #047857;   /* emerald-700 */
    --stage-won-soft:    #ecfdf5;
    --stage-lost:        #b91c1c;   /* red-700     */
    --stage-lost-soft:   #fef2f2;

    /* Semantic aliases over the stage ramp. Status colours already existed as
       pipeline-stage tokens, which made them awkward to reach for in a form
       alert; these names read as intent. Kept as references so the two can
       never drift apart. */
    --danger:        var(--stage-lost);
    --danger-soft:   var(--stage-lost-soft);
    --success:       var(--stage-won);
    --success-soft:  var(--stage-won-soft);

    /* Priority PH's own purple is reserved as a single deliberate accent for
       AI / insight surfaces - never as a general primary colour. */
    --brand-purple: #403375;

    /* Sequential chart ramp - one palette for every ApexCharts / Chart.js
       widget so the six analytics cards stop each inventing their own blues. */
    --chart-1: #1d2e6a;
    --chart-2: #084163;
    --chart-3: #4e83c5;
    --chart-4: #8d9dd0;
    --chart-5: #b9c4e3;
    --chart-6: #dbe1f1;

    /* Soft brand tints - the single pair that every purple-50/100,
       indigo-50/100 and one-off #ebf3fa fill collapses into. */
    --brand-soft:   #eef1f9;
    --brand-softer: #f6f8fc;
    --brand-line:   #dbe1f1;
    --brand-line-2: #c9d0e2;

    /* Type scale - documented here and enforced by section 17. The portal
       used 9/10/11px for real content; 12px is the floor that stays legible. */
    --text-micro: 0.75rem;
    --text-xs:    0.75rem;
    --text-sm:    0.8125rem;
    --text-base:  0.875rem;
    --text-lg:    1rem;
    --text-xl:    1.25rem;
    --text-2xl:   1.5rem;

    /* Radii - two steps only. --radius-card already exists above; --radius-tile
       covers chips, icon tiles and inputs. */
    --radius-tile: 0.75rem;

}

/* Dark mode re-tune: same navy family, inverted canvas. Text roles keep a
   three-step hierarchy (heading > body > muted) instead of flattening. */
html.dark {
    --canvas:        #080d1f;
    --surface:       #0f1734;
    --surface-muted: #131d43;
    --surface-hover: #1a2650;
    --line:          rgba(157, 172, 208, .17);
    --line-strong:   rgba(157, 172, 208, .32);

    --fg-heading: #eef1f9;
    --fg-body:    #b7c1da;
    --fg-muted:   #8b96b3;

    /* Stage + chart ramps brighten a step so they survive on the dark
       canvas; soft tints become translucent navy instead of light fills. */
    --stage-new:         #a8b4d4;
    --stage-qualifying:  #6f9dd8;
    --stage-quote:       #8d9dd0;
    --stage-negotiation: #b9c4e3;
    --stage-won:         #10b981;
    --stage-won-soft:    rgba(16, 185, 129, .12);
    --stage-lost:        #f87171;
    --stage-lost-soft:   rgba(248, 113, 113, .12);

    --chart-1: #4e83c5;
    --chart-2: #6f9dd8;
    --chart-3: #8d9dd0;
    --chart-4: #b9c4e3;
    --chart-5: #2b3f7c;
    --chart-6: #182554;

    --brand-soft:   rgba(141, 157, 208, .14);
    --brand-softer: rgba(141, 157, 208, .08);
    --brand-line:   rgba(157, 172, 208, .22);
    --brand-line-2: rgba(157, 172, 208, .34);

    --shadow-card: 0 1px 2px rgba(0, 0, 0, .45);
    --shadow-lift: 0 16px 40px -16px rgba(0, 0, 0, .70);
}

/* --------------------------------------------------------------------------
   2. BASE
   -------------------------------------------------------------------------- */
/* Specificity (0,2,2) deliberately beats the Tailwind body utility. */
html body.crm-body {
    background-color: var(--canvas);
    color: var(--fg-body);
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* Fallback heading colour only. :where() keeps specificity at (0,0,0) so an
   explicit Tailwind text or tracking utility on a heading still wins. Without
   this, ".crm-body h3" (0,1,1) beat ".text-white" (0,1,0) and painted the
   light-mode navy (#080e1e) onto headings sitting on dark gradient banners. */
:where(.crm-body :is(h1, h2, h3, h4)) {
    color: var(--fg-heading);
    letter-spacing: -0.015em;
}

/* --------------------------------------------------------------------------
   3. SIDEBAR  -  navy rail
   --------------------------------------------------------------------------
   The rail is pinned to the viewport on desktop from the markup, not here:
   includes/sidebar.php carries h-screen + md:sticky md:top-0 md:self-start
   md:overflow-y-auto. Keep that utility set intact - a min-h-screen or
   md:relative on #sidebar lets the whole rail scroll away with <main>.
   -------------------------------------------------------------------------- */
.crm-sidebar {
    background: linear-gradient(180deg, var(--navy-700) 0%, var(--navy-950) 100%);
    border-right: 1px solid rgba(148, 163, 184, .10);
    box-shadow: inset -1px 0 0 rgba(255, 255, 255, .02);
}

/* Brand block */
.crm-brand-name {
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #ffffff;
}
.crm-brand-badge {
    display: inline-block;
    margin-top: .3rem;
    padding: .1rem .45rem;
    border-radius: var(--radius-pill);
    background: rgba(255, 255, 255, .07);
    border: 1px solid rgba(255, 255, 255, .10);
    font-size: .5625rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--on-navy-500);
}

/* Section headers */
.crm-nav-group {
    padding: 0 0 0 .625rem;
    margin-bottom: .4rem;
    font-size: .5625rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: var(--on-navy-600);
}

/* Nav items */
.crm-nav-item {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .625rem;
    width: 100%;
    padding: .375rem .5rem;
    border-radius: var(--radius-ctl);
    border: 1px solid transparent;
    color: #a8b4d4;
    font-size: .75rem;
    font-weight: 500;
    text-align: left;
    transition: background-color .18s ease, color .18s ease, box-shadow .18s ease;
}
.crm-nav-item:hover {
    background: rgba(255, 255, 255, .06);
    color: #ffffff;
}

/* Active = white pill, navy text (the "navy + white" signature) */
.crm-nav-item.is-active {
    background: var(--surface);
    color: var(--navy-900);
    font-weight: 700;
    box-shadow: var(--shadow-navy);
}
html.dark .crm-nav-item.is-active {
    background: #e6ebfa;
    color: var(--navy-950);
}

/* Icon tile */
.crm-nav-ico {
    flex-shrink: 0;
    width: 1.625rem;
    height: 1.625rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: .5rem;
    background: rgba(255, 255, 255, .05);
    color: var(--on-navy-400);
    font-size: .75rem;
    transition: background-color .18s ease, color .18s ease;
}
.crm-nav-item:hover .crm-nav-ico {
    background: rgba(255, 255, 255, .12);
    color: #ffffff;
}
.crm-nav-item.is-active .crm-nav-ico {
    background: var(--navy-900);
    color: #ffffff;
}
html.dark .crm-nav-item.is-active .crm-nav-ico {
    background: var(--navy-950);
}

/* Left accent bar on the active pill */
.crm-nav-item.is-active::before {
    content: "";
    position: absolute;
    left: -.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 55%;
    border-radius: 0 3px 3px 0;
    background: var(--brand);
}

/* Submenu */
.crm-sub-item {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .3rem .5rem;
    border-radius: .5rem;
    color: var(--on-navy-400);
    font-size: .6875rem;
    font-weight: 500;
    transition: background-color .16s ease, color .16s ease;
}
.crm-sub-item:hover {
    background: rgba(255, 255, 255, .06);
    color: #ffffff;
}
.crm-sub-item.is-active {
    background: rgba(255, 255, 255, .10);
    color: #ffffff;
    font-weight: 700;
}
.crm-sub-item.is-active .crm-sub-ico { color: var(--brand); }
.crm-sub-ico { width: 1rem; text-align: center; font-size: .625rem; flex-shrink: 0; }

/* Count badge on a nav row */
.crm-nav-badge {
    flex-shrink: 0;
    min-width: 1.15rem;
    padding: .05rem .35rem;
    border-radius: var(--radius-pill);
    background: rgba(148, 163, 184, .18);
    color: #b9c4e3;
    font-size: .5625rem;
    font-weight: 800;
    text-align: center;
    line-height: 1.15rem;
}
.crm-nav-item.is-active .crm-nav-badge {
    background: var(--navy-900);
    color: #ffffff;
}

/* Footer card */
.crm-side-foot {
    border-top: 1px solid rgba(148, 163, 184, .14);
}
.crm-side-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    padding: .45rem;
    border-radius: .875rem;
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .07);
    transition: background-color .18s ease, border-color .18s ease;
}
.crm-side-card:hover {
    background: rgba(255, 255, 255, .07);
    border-color: rgba(255, 255, 255, .13);
}
.crm-avatar-navy {
    flex-shrink: 0;
    width: 2.125rem;
    height: 2.125rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .625rem;
    background: linear-gradient(140deg, var(--navy-700) 0%, var(--navy-950) 100%);
    border: 1px solid rgba(255, 255, 255, .12);
    color: #ffffff;
    font-size: .6875rem;
    font-weight: 800;
    letter-spacing: .02em;
}
.crm-side-name {
    font-size: .6875rem;
    font-weight: 700;
    color: #eef1f9;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.crm-side-meta {
    display: block;
    font-size: .5625rem;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--on-navy-400);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.crm-logout-btn {
    flex-shrink: 0;
    width: 1.75rem;
    height: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: .5rem;
    color: var(--on-navy-400);
    transition: background-color .16s ease, color .16s ease;
}
.crm-logout-btn:hover {
    background: rgba(244, 63, 94, .14);
    color: #fb7185;
}

/* Reveal-on-hover labels (rail stays collapsed at w-20) */
.crm-reveal {
    opacity: 0;
    transition: opacity .22s ease;
}
.group:hover .crm-reveal { opacity: 1; }

/* SLA mini-widget (customer rail) */
.crm-sla-box {
    padding: .625rem .75rem;
    border-radius: .875rem;
    background: rgba(255, 255, 255, .04);
    border: 1px solid rgba(255, 255, 255, .07);
}
.crm-sla-track {
    height: .3125rem;
    border-radius: var(--radius-pill);
    background: rgba(148, 163, 184, .22);
    overflow: hidden;
}
.crm-sla-fill {
    height: 100%;
    border-radius: var(--radius-pill);
    background: linear-gradient(90deg, #34d399, #10b981);
}

/* --------------------------------------------------------------------------
   4. SURFACES  -  cards, panels
   -------------------------------------------------------------------------- */
.crm-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-card);
    box-shadow: var(--shadow-card);
}

.crm-card-hover {
    transition: box-shadow .22s ease, transform .22s ease, border-color .22s ease;
}
.crm-card-hover:hover {
    box-shadow: var(--shadow-lift);
    border-color: var(--line-strong);
    transform: translateY(-2px);
}

/* Panel header: title row inside a card */
.crm-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--line);
}
.crm-panel-title {
    font-size: .9375rem;
    font-weight: 700;
    color: var(--fg-heading);
    letter-spacing: -0.01em;
}
.crm-panel-sub {
    display: block;
    margin-top: .125rem;
    font-size: .75rem;
    font-weight: 500;
    color: var(--fg-muted);
}
.crm-panel-body { padding: 1.25rem; }

/* Section eyebrow above a card grid */
.crm-section-label {
    font-size: .6875rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--fg-muted);
}

/* --------------------------------------------------------------------------
   5. BUTTONS
   -------------------------------------------------------------------------- */
.crm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    height: 2.25rem;
    padding: 0 1rem;
    border-radius: var(--radius-ctl);
    border: 1px solid transparent;
    font-size: .8125rem;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease,
                color .16s ease, box-shadow .16s ease, transform .12s ease;
}
.crm-btn:active { transform: scale(.98); }
.crm-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); }
.crm-btn:disabled { opacity: .5; cursor: not-allowed; }

/* Primary: navy - the structural action */
.crm-btn-primary {
    background: var(--navy-800);
    color: #ffffff;
    box-shadow: var(--shadow-navy);
}
.crm-btn-primary:hover { background: var(--navy-900); }
html.dark .crm-btn-primary {
    background: var(--on-navy-strong);
    color: var(--navy-950);
    box-shadow: none;
}
html.dark .crm-btn-primary:hover { background: #ffffff; }

/* Accent: follows the picker - reserved for the single hero action */
.crm-btn-accent {
    background: var(--brand);
    color: #ffffff;
}
.crm-btn-accent:hover { background: var(--brand-strong); }

/* Ghost: bordered, transparent */
.crm-btn-ghost {
    background: var(--surface);
    border-color: var(--line-strong);
    color: var(--navy-700);
}
.crm-btn-ghost:hover {
    background: var(--surface-hover);
    border-color: var(--navy-300);
    color: var(--navy-900);
}
html.dark .crm-btn-ghost { color: var(--fg-body); }
html.dark .crm-btn-ghost:hover {
    background: var(--surface-hover);
    color: #ffffff;
}

/* Subtle: borderless quiet action */
.crm-btn-subtle {
    background: transparent;
    color: var(--navy-500);
}
.crm-btn-subtle:hover {
    background: var(--surface-hover);
    color: var(--navy-900);
}

/* Danger */
.crm-btn-danger {
    background: #dc2626;
    color: #ffffff;
}
.crm-btn-danger:hover { background: #b91c1c; }

/* Sizes */
.crm-btn-sm { height: 1.875rem; padding: 0 .75rem; font-size: .75rem; border-radius: .5rem; }
.crm-btn-lg { height: 2.75rem; padding: 0 1.375rem; font-size: .875rem; }

/* Icon-only */
.crm-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: var(--radius-ctl);
    border: 1px solid transparent;
    color: var(--navy-500);
    cursor: pointer;
    transition: background-color .16s ease, color .16s ease, border-color .16s ease;
}
.crm-icon-btn:hover {
    background: var(--surface-hover);
    border-color: var(--line);
    color: var(--navy-900);
}
.crm-icon-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); }
html.dark .crm-icon-btn { color: var(--fg-muted); }
html.dark .crm-icon-btn:hover { color: #ffffff; }


/* --------------------------------------------------------------------------
   6. FORMS
   -------------------------------------------------------------------------- */
.crm-input,
.crm-select,
.crm-textarea {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: var(--radius-ctl);
    padding: .55rem .75rem;
    font-size: .8125rem;
    color: var(--navy-800);
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
}
.crm-input::placeholder,
.crm-textarea::placeholder { color: var(--navy-300); }
.crm-input:hover,
.crm-select:hover,
.crm-textarea:hover { border-color: var(--navy-300); }
.crm-input:focus,
.crm-select:focus,
.crm-textarea:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--ring);
}
html.dark .crm-input,
html.dark .crm-select,
html.dark .crm-textarea {
    background: var(--surface-muted);
    color: var(--on-navy-strong);
}

.crm-label {
    display: block;
    margin-bottom: .375rem;
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--navy-500);
}
html.dark .crm-label { color: var(--fg-muted); }

/* Search field with leading icon */
.crm-search {
    position: relative;
    display: flex;
    align-items: center;
}
.crm-search .crm-search-ico {
    position: absolute;
    left: .75rem;
    font-size: .75rem;
    color: var(--navy-300);
    pointer-events: none;
}
.crm-search .crm-input { padding-left: 2.125rem; }

/* --------------------------------------------------------------------------
   7. TABLES
   -------------------------------------------------------------------------- */
.crm-table-wrap {
    overflow-x: auto;
    border-radius: var(--radius-card);
    background: var(--surface);
    border: 1px solid var(--line);
    box-shadow: var(--shadow-card);
}
.crm-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: .8125rem;
}
.crm-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: var(--surface-muted);
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--line);
    font-size: .625rem;
    font-weight: 800;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--navy-500);
    text-align: left;
    white-space: nowrap;
}
html.dark .crm-table thead th {
    background: var(--surface-muted);
    color: var(--fg-muted);
}
.crm-table tbody td {
    padding: .8125rem 1rem;
    border-bottom: 1px solid var(--line);
    color: var(--fg-body);
    vertical-align: middle;
}
.crm-table tbody tr { transition: background-color .14s ease; }
.crm-table tbody tr:hover td { background: var(--surface-hover); }
.crm-table tbody tr:last-child td { border-bottom: none; }
.crm-table .cell-strong {
    font-weight: 600;
    color: var(--fg-heading);
}
html.dark .crm-table tbody tr:hover td { background: var(--surface-hover); }


/* --------------------------------------------------------------------------
   8. BADGES, CHIPS, PILLS
   -------------------------------------------------------------------------- */
.crm-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .2rem .6rem;
    border-radius: var(--radius-pill);
    border: 1px solid transparent;
    font-size: .6875rem;
    font-weight: 700;
    line-height: 1.1;
    white-space: nowrap;
}
.crm-badge-dot {
    width: .375rem;
    height: .375rem;
    border-radius: var(--radius-pill);
    background: currentColor;
    flex-shrink: 0;
}
.crm-badge-navy   { background: var(--navy-50);       color: var(--navy-700); border-color: var(--navy-100); }
.crm-badge-green  { background: #ecfdf5;              color: #047857;          border-color: #a7f3d0; }
.crm-badge-amber  { background: #fffbeb;              color: #b45309;          border-color: #fde68a; }
.crm-badge-red    { background: #fef2f2;              color: #b91c1c;          border-color: #fecaca; }
.crm-badge-blue   { background: #eff6ff;              color: #1d4ed8;          border-color: #bfdbfe; }
.crm-badge-violet { background: #f5f3ff;              color: #6d28d9;          border-color: #ddd6fe; }
.crm-badge-slate  { background: var(--surface-muted); color: var(--navy-500);  border-color: var(--line); }
html.dark .crm-badge-navy,
html.dark .crm-badge-green,
html.dark .crm-badge-amber,
html.dark .crm-badge-red,
html.dark .crm-badge-blue,
html.dark .crm-badge-violet,
html.dark .crm-badge-slate {
    border-color: rgba(148, 163, 184, .22);
}

/* Filter pill (segmented control item) */
.crm-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .375rem .8125rem;
    border-radius: var(--radius-pill);
    border: 1px solid var(--line-strong);
    background: var(--surface);
    color: var(--navy-500);
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}
.crm-pill:hover {
    border-color: var(--navy-300);
    color: var(--navy-900);
    background: var(--surface-hover);
}
.crm-pill.is-active {
    background: var(--navy-800);
    border-color: var(--navy-800);
    color: #ffffff;
    box-shadow: var(--shadow-navy);
}
html.dark .crm-pill.is-active {
    background: var(--on-navy-strong);
    border-color: var(--on-navy-strong);
    color: var(--navy-950);
}

/* --------------------------------------------------------------------------
   9. TOPBAR + AVATAR
   -------------------------------------------------------------------------- */
.crm-topbar {
    position: sticky;
    top: 0;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    min-height: 4rem;
    padding: 0 1.25rem;
    background: rgba(255, 255, 255, .82);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--line);
}
html.dark .crm-topbar {
    background: rgba(15, 23, 52, .82);
    border-bottom-color: var(--line);
}
.crm-topbar-title {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--fg-heading);
}
.crm-topbar-sub {
    display: block;
    font-size: .6875rem;
    font-weight: 500;
    color: var(--fg-muted);
}
.crm-topbar-group {
    display: flex;
    align-items: center;
    gap: .5rem;
}
.crm-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.125rem;
    height: 2.125rem;
    border-radius: .625rem;
    background: linear-gradient(140deg, var(--navy-700), var(--navy-950));
    color: #ffffff;
    font-size: .6875rem;
    font-weight: 800;
    flex-shrink: 0;
}


/* --------------------------------------------------------------------------
   10. KPI / STAT CARDS
   -------------------------------------------------------------------------- */
.crm-kpi {
    position: relative;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-card);
    padding: 1.125rem 1.25rem;
    box-shadow: var(--shadow-card);
    transition: box-shadow .22s ease, transform .22s ease, border-color .22s ease;
}
.crm-kpi:hover {
    box-shadow: var(--shadow-lift);
    border-color: var(--line-strong);
    transform: translateY(-2px);
}
/* Navy hairline along the top edge */
.crm-kpi::after {
    content: "";
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, var(--navy-800), transparent 65%);
    opacity: .55;
}
.crm-kpi-label {
    font-size: .625rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--fg-muted);
}
.crm-kpi-value {
    margin-top: .375rem;
    font-size: 1.625rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    color: var(--fg-heading);
    font-variant-numeric: tabular-nums;
}
.crm-kpi-foot {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: .5rem;
    font-size: .6875rem;
    font-weight: 600;
    color: var(--fg-muted);
}
.crm-delta { display: inline-flex; align-items: center; gap: .2rem; font-weight: 700; }
.crm-delta-up   { color: #059669; }
.crm-delta-down { color: #e11d48; }
html.dark .crm-delta-up   { color: #34d399; }
html.dark .crm-delta-down { color: #fb7185; }

/* Icon tile on a KPI card */
.crm-kpi-ico {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: .75rem;
    background: var(--navy-50);
    border: 1px solid var(--navy-100);
    color: var(--navy-700);
    font-size: .875rem;
    flex-shrink: 0;
}
html.dark .crm-kpi-ico {
    background: rgba(148, 163, 184, .10);
    border-color: rgba(148, 163, 184, .18);
    color: var(--on-navy-200);
}

/* --------------------------------------------------------------------------
   11. MODALS
   -------------------------------------------------------------------------- */
.crm-overlay {
    position: fixed;
    inset: 0;
    z-index: 60;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(5, 9, 20, .55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.crm-modal {
    width: 100%;
    max-width: 28rem;
    max-height: calc(100vh - 3rem);
    overflow-y: auto;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 1.25rem;
    box-shadow: var(--shadow-lift);
    animation: crm-pop .22s cubic-bezier(.16, 1, .3, 1);
}
.crm-modal-lg { max-width: 44rem; }
.crm-modal-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--line);
}
.crm-modal-body { padding: 1.5rem; }
.crm-modal-foot {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .625rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--line);
    background: var(--surface-muted);
}

/* --------------------------------------------------------------------------
   12. EMPTY STATE + PAGINATION
   -------------------------------------------------------------------------- */
.crm-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: 3rem 1.5rem;
    text-align: center;
}
.crm-empty-ico {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3.25rem;
    height: 3.25rem;
    margin-bottom: .25rem;
    border-radius: 1rem;
    background: var(--navy-50);
    border: 1px solid var(--navy-100);
    color: var(--navy-300);
    font-size: 1.125rem;
}
.crm-empty-title {
    font-size: .875rem;
    font-weight: 700;
    color: var(--fg-heading);
}
.crm-empty-sub {
    font-size: .75rem;
    color: var(--fg-muted);
    max-width: 24rem;
}

.crm-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: .875rem 1.25rem;
    border-top: 1px solid var(--line);
    font-size: .75rem;
    color: var(--fg-muted);
}
.crm-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.875rem;
    height: 1.875rem;
    padding: 0 .5rem;
    border-radius: .5rem;
    border: 1px solid var(--line-strong);
    background: var(--surface);
    color: var(--navy-600);
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}
.crm-page-btn:hover:not(:disabled) {
    background: var(--surface-hover);
    border-color: var(--navy-300);
    color: var(--navy-900);
}
.crm-page-btn.is-active {
    background: var(--navy-800);
    border-color: var(--navy-800);
    color: #ffffff;
}
.crm-page-btn:disabled { opacity: .4; cursor: not-allowed; }

/* --------------------------------------------------------------------------
   13. MOTION
   -------------------------------------------------------------------------- */
@keyframes crm-pop {
    from { opacity: 0; transform: translateY(8px) scale(.98); }
    to   { opacity: 1; transform: none; }
}
@keyframes crm-fade-up {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: none; }
}
.crm-fade-up { animation: crm-fade-up .4s cubic-bezier(.16, 1, .3, 1) both; }
.crm-d1 { animation-delay: .04s; }
.crm-d2 { animation-delay: .08s; }
.crm-d3 { animation-delay: .12s; }
.crm-d4 { animation-delay: .16s; }
.crm-d5 { animation-delay: .20s; }
.crm-d6 { animation-delay: .24s; }

@media (prefers-reduced-motion: reduce) {
    .crm-fade-up, .crm-modal { animation: none; }
    .crm-kpi:hover, .crm-card-hover:hover { transform: none; }
    .crm-btn:active { transform: none; }
}

/* --------------------------------------------------------------------------
   14. SCROLLBAR
   -------------------------------------------------------------------------- */
.crm-scroll { scrollbar-width: thin; scrollbar-color: var(--navy-200) transparent; }
.crm-scroll::-webkit-scrollbar { width: .375rem; height: .375rem; }
.crm-scroll::-webkit-scrollbar-track { background: transparent; }
.crm-scroll::-webkit-scrollbar-thumb {
    background: var(--navy-200);
    border-radius: var(--radius-pill);
}
.crm-scroll::-webkit-scrollbar-thumb:hover { background: var(--navy-300); }
html.dark .crm-scroll { scrollbar-color: rgba(148, 163, 184, .28) transparent; }

/* --------------------------------------------------------------------------
   15. DENSITY  -  honours the existing compact preference
   -------------------------------------------------------------------------- */
html[data-density="compact"] .crm-kpi { padding: .8125rem .9375rem; }
html[data-density="compact"] .crm-kpi-value { font-size: 1.375rem; }
html[data-density="compact"] .crm-table tbody td { padding: .5rem .75rem; }
html[data-density="compact"] .crm-table thead th { padding: .5rem .75rem; }
html[data-density="compact"] .crm-panel-head { padding: .75rem 1rem; }
html[data-density="compact"] .crm-panel-body { padding: .875rem 1rem; }


/* --------------------------------------------------------------------------
   16. MOBILE RAIL
   On small screens the sidebar is off-canvas and slides in fully expanded,
   so hover-reveal labels must always be visible there.
   -------------------------------------------------------------------------- */
@media (max-width: 767px) {
    #sidebar { width: 16rem; }
    #sidebar .crm-reveal { opacity: 1; }
    #sidebar .crm-nav-item.is-active::before { left: -.25rem; }
    /* Off-canvas drawer. The markup carries Tailwind's -translate-x-full;
       these id-scoped rules win on specificity and drive the open state. */
    #sidebar.is-open { transform: translateX(0); }
    #sidebarOverlay.is-active { display: block; }
}


/* --------------------------------------------------------------------------
   17. BRAND RECONCILIATION  -  [data-brand="priority"]
   --------------------------------------------------------------------------
   The sales_agent views were written before this token layer existed and
   hand-roll Tailwind utilities, using purple / violet / indigo as a primary
   colour (~250 occurrences) plus generic blue and several one-off hexes.
   That gives three competing "brand" hues side by side, so nothing reads as
   the Priority navy.

   Rather than rewrite every view, these rules retint the off-brand utilities
   onto the token scale. They are scoped to [data-brand="priority"], which the
   sales_agent <main> elements carry, so the admin and customer portals are
   untouched. Specificity (0,2,0) beats a Tailwind utility (0,1,0), and the
   :hover / :focus forms beat (0,2,0) at (0,3,0).

   This is a deliberate bridge, not the end state: new markup should use the
   .crm-* component classes or priority.*/



/* --- A. Primary fills: every "brand" hue collapses to the navy anchor ---- */
[data-brand="priority"] .bg-purple-600,
[data-brand="priority"] .bg-purple-500,
[data-brand="priority"] .bg-indigo-600,
[data-brand="priority"] .bg-indigo-500,
[data-brand="priority"] .bg-blue-600,
[data-brand="priority"] .bg-blue-500 {
    background-color: var(--navy-700);
}
[data-brand="priority"] .checked\:bg-indigo-600:checked {
    background-color: var(--navy-700);
}

/* --- B. Hover fills: one step darker ------------------------------------- */
[data-brand="priority"] .hover\:bg-purple-700:hover,
[data-brand="priority"] .hover\:bg-purple-600:hover,
[data-brand="priority"] .hover\:bg-indigo-700:hover,
[data-brand="priority"] .hover\:bg-indigo-600:hover,
[data-brand="priority"] .hover\:bg-blue-700:hover {
    background-color: var(--navy-800);
}

/* --- C. Soft tints: one light fill instead of five ----------------------- */
[data-brand="priority"] .bg-purple-50,
[data-brand="priority"] .bg-purple-100,
[data-brand="priority"] .bg-violet-50,
[data-brand="priority"] .bg-indigo-50,
[data-brand="priority"] .bg-indigo-100,
[data-brand="priority"] .bg-blue-50,
[data-brand="priority"] .bg-blue-100,
[data-brand="priority"] .hover\:bg-purple-50:hover,
[data-brand="priority"] .hover\:bg-blue-100:hover {
    background-color: var(--brand-soft);
}
/* Decorative blur blobs and translucent chart backdrops. */
[data-brand="priority"] .bg-violet-200\/30,
[data-brand="priority"] .bg-indigo-50\/30,
[data-brand="priority"] .bg-blue-500\/10,
[data-brand="priority"] .hover\:bg-blue-500\/20:hover {
    background-color: var(--brand-softer);
}

/* --- D. Text: navy for emphasis, navy-400 for icon accents --------------- */
[data-brand="priority"] .text-purple-600,
[data-brand="priority"] .text-purple-700,
[data-brand="priority"] .text-violet-600,
[data-brand="priority"] .text-indigo-500,
[data-brand="priority"] .text-indigo-600,
[data-brand="priority"] .text-indigo-700,
[data-brand="priority"] .text-indigo-800,
[data-brand="priority"] .text-blue-600,
[data-brand="priority"] .text-blue-700,
[data-brand="priority"] .text-blue-800 {
    color: var(--navy-600);
}
[data-brand="priority"] .hover\:text-purple-600:hover,
[data-brand="priority"] .hover\:text-purple-700:hover,
[data-brand="priority"] .hover\:text-indigo-700:hover,
[data-brand="priority"] .hover\:text-indigo-800:hover,
[data-brand="priority"] .hover\:text-blue-600:hover,
[data-brand="priority"] .hover\:text-blue-800:hover {
    color: var(--navy-700);
}
/* Tailwind emits `.group:hover .group-hover\:x` at (0,3,0), which would tie
   with a plain [attr] .class:hover rule and win on source order. Repeating the
   .group:hover chain keeps these above it. */
[data-brand="priority"] .group:hover .group-hover\:text-purple-600,
[data-brand="priority"] .group:hover .group-hover\:text-purple-700,
[data-brand="priority"] .group:hover .group-hover\:text-indigo-600 {
    color: var(--navy-700);
}
[data-brand="priority"] .text-purple-400,
[data-brand="priority"] .text-indigo-400,
[data-brand="priority"] .text-blue-400 {
    color: var(--navy-400);
}


/* --- E. Borders ---------------------------------------------------------- */
[data-brand="priority"] .border-purple-100,
[data-brand="priority"] .border-purple-200,
[data-brand="priority"] .border-purple-500,
[data-brand="priority"] .border-indigo-100,
[data-brand="priority"] .border-indigo-200,
[data-brand="priority"] .border-indigo-300,
[data-brand="priority"] .border-indigo-400,
[data-brand="priority"] .border-indigo-500,
[data-brand="priority"] .border-blue-100,
[data-brand="priority"] .border-blue-200,
[data-brand="priority"] .border-blue-500,
[data-brand="priority"] .border-blue-500\/20 {
    border-color: var(--brand-line);
}
[data-brand="priority"] .hover\:border-purple-100:hover,
[data-brand="priority"] .hover\:border-purple-200:hover,
[data-brand="priority"] .hover\:border-indigo-300:hover,
[data-brand="priority"] .hover\:border-indigo-400:hover {
    border-color: var(--navy-200);
}
[data-brand="priority"] .focus\:border-purple-500:focus,
[data-brand="priority"] .focus\:border-indigo-400:focus,
[data-brand="priority"] .focus\:border-indigo-500:focus,
[data-brand="priority"] .focus\:border-blue-500:focus {
    border-color: var(--navy-700);
}

/* --- F. Focus rings ------------------------------------------------------ */
[data-brand="priority"] .ring-purple-500,
[data-brand="priority"] .ring-indigo-500,
[data-brand="priority"] .ring-blue-500,
[data-brand="priority"] .focus\:ring-purple-500:focus,
[data-brand="priority"] .focus\:ring-indigo-500:focus,
[data-brand="priority"] .focus\:ring-blue-500:focus {
    --tw-ring-color: var(--navy-700);
}
[data-brand="priority"] .ring-purple-500\/20,
[data-brand="priority"] .ring-indigo-500\/40,
[data-brand="priority"] .ring-violet-100,
[data-brand="priority"] .focus\:ring-purple-500\/20:focus,
[data-brand="priority"] .focus\:ring-indigo-500\/40:focus {
    --tw-ring-color: var(--brand-line);
}

/* --- G. Coloured shadows -> navy-tinted ---------------------------------- */
[data-brand="priority"] .shadow-purple-200,
[data-brand="priority"] .shadow-indigo-100,
[data-brand="priority"] .shadow-indigo-200,
[data-brand="priority"] .shadow-indigo-500\/30,
[data-brand="priority"] .shadow-indigo-900\/20,
[data-brand="priority"] .shadow-blue-500\/20,
[data-brand="priority"] .hover\:shadow-indigo-200:hover,
[data-brand="priority"] .hover\:shadow-indigo-500\/30:hover {
    --tw-shadow-color: rgba(29, 46, 106, .18);
    --tw-shadow: var(--tw-shadow-colored);
}


/* --- H. Gradients: brand ramp instead of violet -> indigo ---------------- */
[data-brand="priority"] .from-purple-500,
[data-brand="priority"] .from-violet-500,
[data-brand="priority"] .from-violet-600,
[data-brand="priority"] .from-violet-700,
[data-brand="priority"] .from-indigo-500,
[data-brand="priority"] .from-indigo-600 {
    --tw-gradient-from: var(--navy-700);
    --tw-gradient-to: rgba(29, 46, 106, 0);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
[data-brand="priority"] .via-indigo-600,
[data-brand="priority"] .via-indigo-500 {
    --tw-gradient-to: rgba(24, 37, 84, 0);
    --tw-gradient-stops: var(--tw-gradient-from), var(--navy-800), var(--tw-gradient-to);
}
[data-brand="priority"] .to-violet-500,
[data-brand="priority"] .to-violet-600,
[data-brand="priority"] .to-indigo-600,
[data-brand="priority"] .to-indigo-700 {
    --tw-gradient-to: var(--teal-600);
}
[data-brand="priority"] .hover\:from-violet-700:hover {
    --tw-gradient-from: var(--navy-800);
    --tw-gradient-stops: var(--tw-gradient-from), rgba(24, 37, 84, 0);
}
[data-brand="priority"] .hover\:to-indigo-700:hover {
    --tw-gradient-to: var(--teal-700);
}
/* Soft gradient washes used as card-header backdrops. */
[data-brand="priority"] .from-violet-50\/80,
[data-brand="priority"] .from-indigo-50\/60,
[data-brand="priority"] .from-indigo-50\/70 {
    --tw-gradient-from: var(--brand-soft);
    --tw-gradient-stops: var(--tw-gradient-from), rgba(238, 241, 249, 0);
}
[data-brand="priority"] .via-indigo-50\/60 {
    --tw-gradient-stops: var(--tw-gradient-from), var(--brand-softer), var(--tw-gradient-to);
}

/* --- I. Canvas: one page background -------------------------------------- */
/* Only the <main> itself carries the page canvas; inner bg-slate-50 fills are
   left alone because they are used as subtle table-head / icon-tile fills.
   Scoped to main so it cannot paint over a modal's dark backdrop. */
main[data-brand="priority"] { background-color: var(--canvas); }
[data-brand="priority"] .bg-\[\#F8FAFC\] { background-color: var(--canvas); }
/* One-off fills that duplicated the soft tint. */
[data-brand="priority"] .bg-\[\#ebf3fa\] { background-color: var(--navy-50); }

/* --- J. Typography: 9/10/11px carried real content, not decoration ------- */
[data-brand="priority"] .text-\[9px\],
[data-brand="priority"] .text-\[10px\],
[data-brand="priority"] .text-\[11px\] {
    font-size: var(--text-micro);
}
/* slate-300/400 were used for meaningful secondary text at ~2.5:1. */
[data-brand="priority"] .text-slate-300,
[data-brand="priority"] .text-slate-400 {
    color: var(--fg-muted);
}

/* --- K. Radii: two steps, no 2rem "hero" corners ------------------------- */
[data-brand="priority"] .rounded-\[2rem\],
[data-brand="priority"] .rounded-\[2\.2rem\],
[data-brand="priority"] .rounded-3xl {
    border-radius: var(--radius-card);
}

/* --- L. Elevation: one rest shadow, hover lifts -------------------------- */
[data-brand="priority"] .shadow-sm,
[data-brand="priority"] .shadow-md {
    box-shadow: var(--shadow-card);
}

/* --- M. One-off arbitrary-value hexes ------------------------------------ */
/* The two "hero" stat cards each invented their own blue gradient. */
[data-brand="priority"] .from-\[\#689dff\] {
    --tw-gradient-from: var(--navy-600);
    --tw-gradient-stops: var(--tw-gradient-from), var(--teal-600), var(--navy-800);
}
[data-brand="priority"] .via-\[\#4b82f6\],
[data-brand="priority"] .to-\[\#3b70e6\] {
    --tw-gradient-to: var(--teal-700);
}
/* view_customer.php profile banner: navy -> purple. The stops are declared in
   full on both the from- and via- rules so the cascade order between them
   cannot flatten the gradient. */
[data-brand="priority"] .from-\[\#0f1b3d\],
[data-brand="priority"] .via-\[\#1d2e6a\] {
    --tw-gradient-from: var(--navy-800);
    --tw-gradient-stops: var(--tw-gradient-from), var(--navy-700), var(--teal-700);
}
[data-brand="priority"] .to-\[\#3b1d6e\] { --tw-gradient-to: var(--teal-700); }
/* settings.php invented a second, darker navy for its surfaces. */
[data-brand="priority"] .dark\:bg-\[\#0e1b33\]:is(.dark *),
[data-brand="priority"] .dark\:bg-\[\#112240\]:is(.dark *) {
    background-color: var(--surface-muted);
}
[data-brand="priority"] .dark\:bg-\[\#0e1b33\]\/95:is(.dark *) {
    background-color: rgba(19, 29, 67, .95);
}
[data-brand="priority"] .from-\[\#0b1528\] {
    --tw-gradient-from: var(--navy-900);
    --tw-gradient-stops: var(--tw-gradient-from), var(--navy-800), var(--navy-700);
}
[data-brand="priority"] .via-\[\#0e1b33\],
[data-brand="priority"] .to-\[\#103a8a\] { --tw-gradient-to: var(--navy-700); }

/* --- N. dark: variants --------------------------------------------------- */
/* Tailwind's CDN injects its stylesheet after theme.css, so a rule that only
   ties with `.dark .foo` on specificity would lose. These match the dark:
   compound selectors directly and add the [data-brand] attribute on top. */
[data-brand="priority"] .dark\:bg-blue-500\/10:is(.dark *),
[data-brand="priority"] .dark\:hover\:bg-blue-500\/20:hover:is(.dark *) {
    background-color: var(--brand-soft);
}
[data-brand="priority"] .dark\:border-blue-500\/20:is(.dark *) {
    border-color: var(--brand-line);
}


/* --------------------------------------------------------------------------
   18. ADMIN RECONCILIATION  -  [data-brand="priority"]  (neutral + accent)
   --------------------------------------------------------------------------
   Section 17 was written for the sales_agent portal, whose off-brand colour
   was purple / violet / indigo. The admin views instead lean on two families
   that 17 never touched:

     1. The Tailwind "gray" scale (agents.php, logs.php) - a warm neutral that
        sits visibly beside the cool "slate" scale used everywhere else, so
        adjacent cards read as two different greys.
     2. teal / sky / cyan used as a *brand accent* (customer_monitoring.php
        paints its card icons and "View All" links teal-600) - a fourth hue
        competing with the navy.

   Status colours (green / emerald / rose / red / amber / yellow) are
   deliberately NOT remapped here. They carry meaning - a Closed Won badge
   must stay green - and the token layer already reserves green/red for the
   won/lost terminal states. Collapsing them would cost information.

   Every light-mode rule below is paired with an html.dark counterpart at
   (0,3,0). style.css already ships html.dark remaps for the slate scale at
   (0,2,0); because theme.css loads after it, an unpaired (0,2,0) rule here
   would win the tie and freeze those surfaces light in dark mode.
   -------------------------------------------------------------------------- */

/* --- A. Warm "gray" scale -> the cool neutral token scale ---------------- */
[data-brand="priority"] .text-gray-900,
[data-brand="priority"] .text-gray-800 {
    color: var(--fg-heading);
}
[data-brand="priority"] .text-gray-700,
[data-brand="priority"] .text-gray-600 {
    color: var(--fg-body);
}
[data-brand="priority"] .text-gray-500,
[data-brand="priority"] .text-gray-400 {
    color: var(--fg-muted);
}
[data-brand="priority"] .border-gray-100,
[data-brand="priority"] .border-gray-200,
[data-brand="priority"] .border-gray-200\/80 {
    border-color: var(--line);
}
[data-brand="priority"] .border-gray-300 {
    border-color: var(--line-strong);
}
[data-brand="priority"] .divide-gray-100 > * + *,
[data-brand="priority"] .divide-gray-200 > * + * {
    border-color: var(--line);
}
[data-brand="priority"] .bg-gray-50,
[data-brand="priority"] .bg-gray-100 {
    background-color: var(--surface-muted);
}
[data-brand="priority"] .hover\:bg-gray-50:hover,
[data-brand="priority"] .hover\:bg-gray-100:hover {
    background-color: var(--surface-hover);
}

/* --- B. Slate text tiers -> the three documented roles ------------------- */
/* slate-300/400 are already lifted to --fg-muted by 17-J; the darker tiers
   map onto the heading / body roles so the hierarchy matches the .crm-*
   components instead of Tailwind's blue-tinted greys. */
[data-brand="priority"] .text-slate-900,
[data-brand="priority"] .text-slate-800 {
    color: var(--fg-heading);
}
[data-brand="priority"] .text-slate-700,
[data-brand="priority"] .text-slate-600 {
    color: var(--fg-body);
}
[data-brand="priority"] .text-slate-500 {
    color: var(--fg-muted);
}
[data-brand="priority"] .border-slate-100,
[data-brand="priority"] .border-slate-200,
[data-brand="priority"] .border-slate-200\/80,
[data-brand="priority"] .divide-slate-100 > * + * {
    border-color: var(--line);
}
[data-brand="priority"] .bg-slate-50,
[data-brand="priority"] .bg-slate-50\/30,
[data-brand="priority"] .bg-slate-50\/50,
[data-brand="priority"] .bg-slate-50\/70 {
    background-color: var(--surface-muted);
}
[data-brand="priority"] .bg-slate-100,
[data-brand="priority"] .bg-slate-100\/70,
[data-brand="priority"] .bg-slate-100\/80 {
    background-color: var(--navy-50);
}
[data-brand="priority"] .hover\:bg-slate-50:hover,
[data-brand="priority"] .hover\:bg-slate-100:hover {
    background-color: var(--surface-hover);
}

/* --- C. teal / sky / cyan as brand accents -> the navy family ------------ */
[data-brand="priority"] .bg-teal-600,
[data-brand="priority"] .bg-teal-500,
[data-brand="priority"] .bg-sky-500,
[data-brand="priority"] .bg-cyan-500 {
    background-color: var(--navy-700);
}
[data-brand="priority"] .text-teal-600,
[data-brand="priority"] .text-teal-700,
[data-brand="priority"] .text-teal-800,
[data-brand="priority"] .text-sky-600,
[data-brand="priority"] .text-sky-700,
[data-brand="priority"] .text-cyan-600 {
    color: var(--navy-600);
}
[data-brand="priority"] .hover\:text-teal-700:hover,
[data-brand="priority"] .hover\:text-teal-800:hover,
[data-brand="priority"] .hover\:text-sky-700:hover,
[data-brand="priority"] .hover\:text-sky-800:hover {
    color: var(--navy-800);
}
[data-brand="priority"] .bg-teal-50,
[data-brand="priority"] .bg-teal-100,
[data-brand="priority"] .bg-sky-50,
[data-brand="priority"] .bg-sky-100 {
    background-color: var(--brand-soft);
}
[data-brand="priority"] .border-teal-200,
[data-brand="priority"] .border-sky-200 {
    border-color: var(--brand-line);
}

/* --- D. Modal scrims and dark fills -------------------------------------- */
/* bg-slate-900 does double duty in the admin views: it is the modal scrim on
   the fixed inset-0 root, and a solid button fill inside customers.php. The
   scrim form is matched structurally so the button keeps an opaque colour.
   Tailwind writes the /40 alpha straight into background-color, so the
   override has to carry its own. */
[data-brand="priority"] .fixed.inset-0.bg-slate-900\/40,
[data-brand="priority"] .fixed.inset-0.bg-slate-900 {
    background-color: rgba(10, 15, 36, .55);
}
[data-brand="priority"] .bg-gray-600 {
    background-color: var(--navy-950);
}
[data-brand="priority"] .bg-slate-900 {
    background-color: var(--navy-950);
}
[data-brand="priority"] .hover\:bg-slate-800:hover {
    background-color: var(--navy-800);
}

/* --- E. Dark-mode counterparts ------------------------------------------- */
html.dark [data-brand="priority"] .text-gray-900,
html.dark [data-brand="priority"] .text-gray-800,
html.dark [data-brand="priority"] .text-slate-900,
html.dark [data-brand="priority"] .text-slate-800 { color: #eef1f9; }
html.dark [data-brand="priority"] .text-gray-700,
html.dark [data-brand="priority"] .text-gray-600,
html.dark [data-brand="priority"] .text-slate-700,
html.dark [data-brand="priority"] .text-slate-600 { color: #c3cbe0; }
html.dark [data-brand="priority"] .text-gray-500,
html.dark [data-brand="priority"] .text-gray-400,
html.dark [data-brand="priority"] .text-slate-500 { color: #a2aecb; }

html.dark [data-brand="priority"] .border-gray-100,
html.dark [data-brand="priority"] .border-gray-200,
html.dark [data-brand="priority"] .border-gray-200\/80,
html.dark [data-brand="priority"] .border-slate-100,
html.dark [data-brand="priority"] .border-slate-200,
html.dark [data-brand="priority"] .border-slate-200\/80,
html.dark [data-brand="priority"] .divide-gray-100 > * + *,
html.dark [data-brand="priority"] .divide-gray-200 > * + *,
html.dark [data-brand="priority"] .divide-slate-100 > * + * {
    border-color: rgba(148, 163, 184, .18);
}
html.dark [data-brand="priority"] .border-gray-300 {
    border-color: rgba(148, 163, 184, .28);
}

html.dark [data-brand="priority"] .bg-gray-50,
html.dark [data-brand="priority"] .bg-gray-100,
html.dark [data-brand="priority"] .bg-slate-50,
html.dark [data-brand="priority"] .bg-slate-50\/30,
html.dark [data-brand="priority"] .bg-slate-50\/50,
html.dark [data-brand="priority"] .bg-slate-50\/70 {
    background-color: var(--navy-800);
}
html.dark [data-brand="priority"] .bg-slate-100,
html.dark [data-brand="priority"] .bg-slate-100\/70,
html.dark [data-brand="priority"] .bg-slate-100\/80 {
    background-color: var(--navy-850);
}
html.dark [data-brand="priority"] .hover\:bg-gray-50:hover,
html.dark [data-brand="priority"] .hover\:bg-gray-100:hover,
html.dark [data-brand="priority"] .hover\:bg-slate-50:hover,
html.dark [data-brand="priority"] .hover\:bg-slate-100:hover {
    background-color: var(--navy-700);
}

/* The soft brand tints are near-white; in dark mode they must invert to a
   navy wash or every chip and icon tile becomes a bright block. */
html.dark [data-brand="priority"] .bg-teal-50,
html.dark [data-brand="priority"] .bg-teal-100,
html.dark [data-brand="priority"] .bg-sky-50,
html.dark [data-brand="priority"] .bg-sky-100 {
    background-color: rgba(78, 131, 197, .16);
}
html.dark [data-brand="priority"] .text-teal-600,
html.dark [data-brand="priority"] .text-teal-700,
html.dark [data-brand="priority"] .text-teal-800,
html.dark [data-brand="priority"] .text-sky-600,
html.dark [data-brand="priority"] .text-sky-700,
html.dark [data-brand="priority"] .text-cyan-600 {
    color: var(--sky-brand);
}
html.dark [data-brand="priority"] .bg-teal-600,
html.dark [data-brand="priority"] .bg-teal-500,
html.dark [data-brand="priority"] .bg-sky-500,
html.dark [data-brand="priority"] .bg-cyan-500 {
    background-color: var(--sky-brand);
}
/* ===== consolidated from assets/css/auth.css ===== */
/* ==========================================================================
   AUTH SCREENS  —  login.php · register.php · otp_verification.php · logout.php
   --------------------------------------------------------------------------
   The public-facing entry point of the portal. Built entirely on the design
   tokens in assets/css/theme.css ("Priority Navy") so these pages can
   never drift from the dashboards they hand off to.

   Direction: LIGHT SPLIT-PANEL.
     - left  = navy brand panel  (--brand-grad, white type)
     - right = white form card on the #F0F0F0 Priority surface
   Below `lg` the brand panel collapses to a compact header and the form
   becomes a single centred card.

   NOTE: login.css and otp.css are dead files - nothing links them. Their
   useful parts (the .otp-input segment styling and the fade keyframes) were
   folded in here. This file replaces both.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. SHELL
   -------------------------------------------------------------------------- */
.auth-body {
    min-height: 100dvh;
    display: flex;
    flex-direction: column;
    background-color: var(--canvas);
    color: var(--fg-body);
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
}

.auth-main {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: clamp(1.5rem, 4vw, 3.5rem) clamp(1rem, 4vw, 2rem);
}

.auth-shell {
    width: 100%;
    max-width: 64rem;
    display: grid;
    grid-template-columns: 1fr;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 1.5rem;
    box-shadow: var(--shadow-lift);
    overflow: hidden;
}

@media (min-width: 1024px) {
    .auth-shell { grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); }
}

/* --------------------------------------------------------------------------
   2. BRAND PANEL  (left)
   -------------------------------------------------------------------------- */
.auth-brand {
    position: relative;
    display: none;
    flex-direction: column;
    justify-content: space-between;
    gap: 2.5rem;
    padding: 2.75rem 2.5rem;
    background: var(--brand-grad);
    color: #ffffff;
    overflow: hidden;
}

@media (min-width: 1024px) {
    .auth-brand { display: flex; }
}

/* Faint blueprint grid - replaces the hotlinked Unsplash warehouse photo.
   Zero network cost, and it reads as "logistics" without being a stock shot. */
.auth-brand::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(to right, rgba(255, 255, 255, .06) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, .06) 1px, transparent 1px);
    background-size: 44px 44px;
    -webkit-mask-image: radial-gradient(ellipse 90% 70% at 30% 20%, #000 0%, transparent 75%);
            mask-image: radial-gradient(ellipse 90% 70% at 30% 20%, #000 0%, transparent 75%);
    pointer-events: none;
}

/* Soft light bloom for depth. */
.auth-brand::after {
    content: "";
    position: absolute;
    width: 26rem;
    height: 26rem;
    right: -9rem;
    bottom: -9rem;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(78, 131, 197, .38) 0%, transparent 70%);
    pointer-events: none;
}

.auth-brand > * { position: relative; z-index: 1; }

.auth-brand-eyebrow {
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, .62);
}

.auth-brand-title {
    margin-top: .75rem;
    font-size: clamp(1.5rem, 2.4vw, 1.875rem);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -.02em;
    color: #ffffff;
}

.auth-brand-sub {
    margin-top: .75rem;
    font-size: .875rem;
    line-height: 1.65;
    color: rgba(255, 255, 255, .74);
    max-width: 30rem;
}

/* Value props */
.auth-features {
    margin-top: 2rem;
    display: grid;
    gap: .875rem;
}

.auth-feature {
    display: flex;
    align-items: flex-start;
    gap: .875rem;
}

.auth-feature-ico {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.125rem;
    height: 2.125rem;
    border-radius: .625rem;
    background: rgba(255, 255, 255, .12);
    border: 1px solid rgba(255, 255, 255, .18);
    color: #ffffff;
    font-size: .8125rem;
}

.auth-feature-txt strong {
    display: block;
    font-size: .8125rem;
    font-weight: 700;
    color: #ffffff;
}

.auth-feature-txt span {
    display: block;
    font-size: .75rem;
    line-height: 1.5;
    color: rgba(255, 255, 255, .66);
}

/* Contact strip pinned to the panel foot */
.auth-brand-foot {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, .14);
    font-size: .75rem;
    color: rgba(255, 255, 255, .7);
}

.auth-brand-foot a {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    color: rgba(255, 255, 255, .82);
    text-decoration: none;
    transition: color .16s ease;
}

.auth-brand-foot a:hover { color: #ffffff; }
.auth-brand-foot i { font-size: .6875rem; color: #b9c4e3; }

/* --------------------------------------------------------------------------
   3. LOGO MARK
   --------------------------------------------------------------------------
   assets/image/logo-mark.png is the script "P" from logo.png with the opaque
   white background knocked out to real alpha (the original is a solid
   #FEFEFE square and cannot sit on a tinted surface). It is authored in the
   brand violet #290F74; on the navy panel we invert it to white with a filter
   so one asset serves both sides. */
.auth-logo {
    display: flex;
    align-items: center;
    gap: .875rem;
}

.auth-logo-img {
    width: 2.75rem;
    height: 2.75rem;
    object-fit: contain;
    flex-shrink: 0;
}

.auth-logo-img--on-navy { filter: brightness(0) invert(1); }

.auth-logo-name {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: .02em;
    line-height: 1.15;
    color: var(--fg-heading);
}

.auth-logo-name em {
    font-style: normal;
    font-weight: 500;
    color: var(--navy-500);
}

.auth-logo-meta {
    margin-top: .1875rem;
    font-size: .625rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--fg-muted);
}

.auth-brand .auth-logo-name { color: #ffffff; }
.auth-brand .auth-logo-name em { color: rgba(255, 255, 255, .72); }
.auth-brand .auth-logo-meta { color: rgba(255, 255, 255, .55); }

/* --------------------------------------------------------------------------
   4. FORM PANEL  (right)
   -------------------------------------------------------------------------- */
.auth-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(1.75rem, 4vw, 3rem) clamp(1.25rem, 4vw, 3rem);
    background: var(--surface);
}

/* On mobile the brand panel is hidden, so a compact tinted header carries the
   identity instead. */
.auth-panel-head {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-bottom: 1.75rem;
}

@media (min-width: 1024px) {
    .auth-panel-head { display: none; }
}

.auth-eyebrow {
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--brand);
}

.auth-title {
    margin-top: .5rem;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -.02em;
    line-height: 1.25;
    color: var(--fg-heading);
}

.auth-sub {
    margin-top: .5rem;
    font-size: .875rem;
    line-height: 1.6;
    color: var(--fg-muted);
}


/* --------------------------------------------------------------------------
   5. FIELDS
   -------------------------------------------------------------------------- */
.auth-form { display: grid; gap: 1.125rem; }

.auth-field { display: grid; gap: .4375rem; }

.auth-label {
    font-size: .8125rem;
    font-weight: 600;
    color: var(--fg-heading);
}

.auth-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
}

.auth-input-wrap { position: relative; }

.auth-input {
    width: 100%;
    height: 2.875rem;
    padding: 0 .875rem 0 2.625rem;
    font-size: 1rem;                 /* >=16px: stops iOS zoom-on-focus */
    font-family: inherit;
    color: var(--fg-heading);
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: var(--radius-ctl);
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    -webkit-appearance: none;
    appearance: none;
}

.auth-input::placeholder { color: var(--navy-300); }
.auth-input:hover { border-color: var(--navy-300); }

.auth-input:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--ring);
}

.auth-input:-webkit-autofill,
.auth-input:-webkit-autofill:hover,
.auth-input:-webkit-autofill:focus {
    /* Chrome paints the field with its own yellow and ignores the rules
       above; this forces the token surface back without killing autofill. */
    -webkit-text-fill-color: var(--fg-heading);
    -webkit-box-shadow: 0 0 0 1000px var(--surface) inset;
    caret-color: var(--brand);
    transition: background-color 9999s ease-in-out 0s;
}

.auth-input-ico {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2.625rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .8125rem;
    color: var(--navy-300);
    pointer-events: none;
    transition: color .16s ease;
}

.auth-input:focus ~ .auth-input-ico { color: var(--brand); }

/* Password reveal toggle */
.auth-reveal {
    position: absolute;
    right: .25rem;
    top: .25rem;
    bottom: .25rem;
    width: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: calc(var(--radius-ctl) - .1875rem);
    background: transparent;
    color: var(--fg-muted);
    font-size: .8125rem;
    cursor: pointer;
    transition: background-color .16s ease, color .16s ease;
}

.auth-reveal:hover { background: var(--surface-muted); color: var(--fg-heading); }
.auth-reveal:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); }

.auth-hint { font-size: .75rem; line-height: 1.6; color: var(--fg-muted); }
.auth-hint--error { color: var(--danger); }

.auth-link {
    font-weight: 600;
    color: var(--brand);
    text-decoration: none;
    transition: color .16s ease;
}

.auth-link:hover { color: var(--brand-strong); text-decoration: underline; }
.auth-link:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); border-radius: .25rem; }

/* --------------------------------------------------------------------------
   6. BUTTONS
   -------------------------------------------------------------------------- */
.auth-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    width: 100%;
    height: 2.875rem;
    padding: 0 1.25rem;
    font-size: .9375rem;
    font-weight: 600;
    font-family: inherit;
    line-height: 1;
    white-space: nowrap;
    border: 1px solid transparent;
    border-radius: var(--radius-ctl);
    cursor: pointer;
    transition: background-color .16s ease, box-shadow .16s ease,
                transform .12s ease, opacity .16s ease;
}

.auth-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); }
.auth-btn:active { transform: scale(.99); }
.auth-btn:disabled { opacity: .6; cursor: progress; }

.auth-btn--primary {
    background: var(--brand-button);
    color: #ffffff;
    box-shadow: var(--shadow-navy);
}

.auth-btn--primary:hover { background: var(--navy-900); }

.auth-btn--ghost {
    background: transparent;
    color: var(--fg-body);
    border-color: var(--line-strong);
    box-shadow: none;
}

.auth-btn--ghost:hover { background: var(--surface-muted); color: var(--fg-heading); }

/* Spinner swaps in for the idle label while the form is in flight. */
.auth-btn-spin { display: none; }
.auth-btn.is-busy .auth-btn-spin { display: inline-block; animation: auth-spin .7s linear infinite; }
.auth-btn.is-busy .auth-btn-idle { display: none; }

@keyframes auth-spin { to { transform: rotate(360deg); } }


/* --------------------------------------------------------------------------
   7. ALERTS
   -------------------------------------------------------------------------- */
.auth-alert {
    display: flex;
    align-items: flex-start;
    gap: .625rem;
    padding: .75rem .875rem;
    border-radius: var(--radius-ctl);
    border: 1px solid;
    font-size: .8125rem;
    line-height: 1.55;
}

.auth-alert i { margin-top: .125rem; flex-shrink: 0; }

.auth-alert--error {
    background: var(--danger-soft);
    border-color: rgba(198, 44, 44, .28);
    color: var(--danger);
}

.auth-alert--success {
    background: var(--success-soft);
    border-color: rgba(16, 185, 129, .28);
    color: #047857;
}

.auth-alert--info {
    background: var(--brand-softer);
    border-color: var(--brand-line);
    color: var(--fg-body);
}

.auth-alert--info a { color: var(--brand); font-weight: 600; }

/* --------------------------------------------------------------------------
   8. OTP SEGMENTS
   -------------------------------------------------------------------------- */
.auth-otp {
    display: flex;
    gap: .5rem;
    justify-content: center;
}

@media (min-width: 400px) {
    .auth-otp { gap: .625rem; }
}

.auth-otp-box {
    width: clamp(2.5rem, 11vw, 3.25rem);
    height: clamp(3rem, 13vw, 3.75rem);
    text-align: center;
    font-size: 1.375rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    font-variant-numeric: tabular-nums;
    color: var(--fg-heading);
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: var(--radius-ctl);
    transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    -webkit-appearance: none;
    appearance: none;
    caret-color: var(--brand);
}

.auth-otp-box:hover { border-color: var(--navy-300); }

.auth-otp-box:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--ring);
    background: var(--brand-softer);
}

.auth-otp-box.is-filled {
    border-color: var(--brand-line-2);
    background: var(--brand-softer);
}

.auth-otp.is-invalid .auth-otp-box {
    border-color: rgba(198, 44, 44, .45);
    background: var(--danger-soft);
    color: var(--danger);
}

.auth-otp.is-shaking { animation: auth-shake .32s ease; }

@keyframes auth-shake {
    0%, 100% { transform: translateX(0); }
    20%      { transform: translateX(-6px); }
    40%      { transform: translateX(6px); }
    60%      { transform: translateX(-4px); }
    80%      { transform: translateX(4px); }
}

/* The real input the autofill agents target; the visible boxes mirror it. */
.auth-otp-master {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0 0 0 0);
    clip-path: inset(50%);
    white-space: nowrap;
    border: 0;
}

/* --------------------------------------------------------------------------
   9. META ROW  (back link / resend / countdown)
   -------------------------------------------------------------------------- */
.auth-meta {
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    font-size: .8125rem;
    color: var(--fg-muted);
}

.auth-meta strong { color: var(--fg-heading); font-weight: 600; }
.auth-meta .tabular { font-variant-numeric: tabular-nums; }

.auth-btn-link {
    border: 0;
    background: none;
    padding: .25rem 0;
    font: inherit;
    font-weight: 600;
    color: var(--brand);
    cursor: pointer;
    transition: color .16s ease;
}

.auth-btn-link:hover { color: var(--brand-strong); text-decoration: underline; }
.auth-btn-link:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); border-radius: .25rem; }
.auth-btn-link:disabled { color: var(--fg-muted); cursor: not-allowed; text-decoration: none; }


/* --------------------------------------------------------------------------
   10. PAGE FOOTER
   -------------------------------------------------------------------------- */
.auth-foot {
    padding: 1.25rem clamp(1rem, 4vw, 2rem) 1.75rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: .5rem 1.25rem;
    font-size: .75rem;
    color: var(--fg-muted);
    text-align: center;
}

.auth-foot-links { display: flex; gap: 1.25rem; }

.auth-foot button {
    border: 0;
    background: none;
    padding: 0;
    font: inherit;
    color: var(--fg-muted);
    cursor: pointer;
    transition: color .16s ease;
}

.auth-foot button:hover { color: var(--brand); text-decoration: underline; }
.auth-foot button:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--ring); border-radius: .25rem; }

/* --------------------------------------------------------------------------
   11. LEGAL MODALS
   --------------------------------------------------------------------------
   Rendered by src/components/legal_modals.php. The wrapper carries `inert`
   so the dialogs are out of the tab order until auth.js opens one. */
.legal-overlay {
    position: fixed;
    inset: 0;
    z-index: 70;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(10, 15, 36, .62);
    -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
    opacity: 0;
    transition: opacity .22s ease;
}

/* Safety net: elements with the HTML hidden attribute must never be shown by
   custom display rules. The Play CDN places custom CSS AFTER base+utilities
   (inverting the legacy <link>-based order), so .auth-alert{display:flex} used
   to beat preflight's [hidden] rule and rendered empty error boxes on load. */
[hidden] { display: none !important; }
.legal-overlay[hidden] { display: none; }
.legal-overlay.is-open { opacity: 1; }

.legal-sheet {
    display: flex;
    flex-direction: column;
    width: 100%;

/* --------------------------------------------------------------------------
   11c. SIGNED-OUT SCREEN
   -------------------------------------------------------------------------- */
.auth-shell--narrow {
    max-width: 34rem;
    margin: 0 auto;
    width: 100%;
}

.auth-panel--center {
    align-items: center;
    text-align: center;
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.auth-panel--center .auth-sub { text-align: center; }
.auth-panel--center .auth-btn { max-width: 18rem; }

.auth-badge {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: .25rem;
}

.auth-badge--success {
    color: var(--success);
    background: var(--success-soft);
    border: 1px solid color-mix(in srgb, var(--success) 25%, transparent);
}

.auth-tips {
    display: grid;
    gap: .625rem;
    width: 100%;
    margin-top: .5rem;
    padding: 1rem 1.125rem;
    list-style: none;
    text-align: left;
    background: var(--surface-muted);
    border: 1px solid var(--line);
    border-radius: var(--radius-card);
}

.auth-tips li {
    display: flex;
    align-items: flex-start;
    gap: .625rem;
    font-size: .8125rem;
    line-height: 1.55;
    color: var(--fg-body);
}

.auth-tips li i { color: var(--success); font-size: .75rem; margin-top: .25rem; }

    max-width: 46rem;
    max-height: 86vh;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-card);
    box-shadow: var(--shadow-lift);
    overflow: hidden;
    transform: translateY(12px) scale(.985);
    transition: transform .26s cubic-bezier(.16, 1, .3, 1);
}

.legal-overlay.is-open .legal-sheet { transform: none; }

.legal-head {
    display: flex;
    align-items: center;
    gap: .875rem;
    padding: 1.25rem 1.5rem;
    background: var(--brand-grad);
    color: #ffffff;
}

.legal-head-ico {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    flex-shrink: 0;
    border-radius: .625rem;
    background: rgba(255, 255, 255, .14);
    border: 1px solid rgba(255, 255, 255, .2);
    font-size: .875rem;
}

.legal-title {
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #ffffff;
}

.legal-caption {
    margin-top: .125rem;
    font-size: .6875rem;
    color: rgba(255, 255, 255, .7);
}

.legal-close {
    margin-left: auto;
    flex-shrink: 0;
    width: 2.125rem;
    height: 2.125rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, .22);
    border-radius: .5rem;
    background: rgba(255, 255, 255, .08);
    color: #ffffff;
    font-size: .875rem;
    cursor: pointer;
    transition: background-color .16s ease;
}

.legal-close:hover { background: rgba(255, 255, 255, .2); }
.legal-close:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(255, 255, 255, .45); }

.legal-body {
    padding: 1.5rem;
    overflow-y: auto;
    background: var(--surface);
}

.legal-p { font-size: .8125rem; line-height: 1.7; color: var(--fg-body); margin-bottom: .75rem; }
.legal-p strong { color: var(--fg-heading); font-weight: 600; }
.legal-sub { font-size: .8125rem; line-height: 1.7; color: var(--fg-body); padding-left: 1.25rem; margin-bottom: .5rem; }
.legal-sub strong { color: var(--fg-heading); font-weight: 600; }

p[data-legal="head"] {
    font-size: .8125rem;
    font-weight: 700;
    color: var(--fg-heading);
    margin: 1.375rem 0 .5rem;
}

p[data-legal="lead"] {
    font-size: .8125rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--brand);
    margin: 0 0 .75rem;
}

.legal-foot {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--line);
    background: var(--surface-muted);
    display: flex;
    justify-content: flex-end;
}

.legal-btn { width: auto; height: 2.375rem; font-size: .8125rem; padding: 0 1.25rem; }

/* --------------------------------------------------------------------------
   11b. MOBILE TOP BAR + PANEL RHYTHM
   -------------------------------------------------------------------------- */
.auth-topbar {
    border-bottom: 1px solid var(--line);
    background: var(--surface);
}

@media (min-width: 1024px) {
    .auth-topbar { display: none; }
}

.auth-topbar-inner {
    max-width: 64rem;
    margin: 0 auto;
    padding: .75rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.auth-topbar-tag {
    font-size: .625rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--brand);
    background: var(--brand-soft);
    border: 1px solid var(--brand-line);
    border-radius: var(--radius-pill);
    padding: .3125rem .625rem;
    white-space: nowrap;
}

/* Vertical rhythm inside the form panel: the intro block, any alert, and the
   form each need breathing room, and the panel itself is a plain flex column. */
.auth-panel { gap: 1.5rem; }
.auth-panel .auth-panel-head { margin-bottom: 0; }
.auth-panel-head + * { margin-top: -.5rem; }

.auth-brand-eyebrow--spaced { margin-top: 2.75rem; }

/* Idle label sits inline so the icon and text share a baseline with the
   spinner that replaces it. */
.auth-btn-idle {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
}

.auth-btn-idle i { font-size: .8125rem; }


/* --------------------------------------------------------------------------
   12. REGISTER: TWO-UP NAME ROW
   --------------------------------------------------------------------------
   First / last name sit side by side from `sm` up so the form stays roughly
   the same height as the login panel. Collapses to a single column below. */
.auth-grid-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.125rem;
}

@media (min-width: 640px) {
    .auth-grid-2 { grid-template-columns: 1fr 1fr; }
}

/* --------------------------------------------------------------------------
   13. REGISTER: PASSWORD STRENGTH METER
   --------------------------------------------------------------------------
   Four segments; assets/js/auth.js sets data-score="0..4" on the wrapper.
   Colours walk the same navy -> green ramp the dashboards use for progress. */
.auth-strength {
    display: flex;
    align-items: center;
    gap: .5rem;
}

.auth-strength-bars {
    display: flex;
    flex: 1 1 auto;
    gap: .25rem;
}

.auth-strength-bar {
    height: .25rem;
    flex: 1 1 0;
    border-radius: var(--radius-pill);
    background: var(--line);
    transition: background-color .2s ease;
}

.auth-strength-label {
    flex-shrink: 0;
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--fg-muted);
    font-variant-numeric: tabular-nums;
}

[data-score="1"] .auth-strength-bar:nth-child(-n+1),
[data-score="2"] .auth-strength-bar:nth-child(-n+2) { background: var(--danger); }

[data-score="3"] .auth-strength-bar:nth-child(-n+3) { background: var(--navy-500); }
[data-score="3"] .auth-strength-label { color: var(--navy-500); }

[data-score="4"] .auth-strength-bar { background: var(--success); }
[data-score="4"] .auth-strength-label { color: var(--success); }

[data-score="1"] .auth-strength-label { color: var(--danger); }
[data-score="2"] .auth-strength-label { color: var(--danger); }

/* --------------------------------------------------------------------------
   14. REGISTER: CONSENT CHECKBOX
   --------------------------------------------------------------------------
   Native input kept (so the form validates without JS) but restyled to the
   token language; the check is FontAwesome-free, drawn with a mask so it
   inherits --surface against the brand fill. */
.auth-check {
    display: flex;
    align-items: flex-start;
    gap: .625rem;
}

.auth-check-input {
    flex-shrink: 0;
    width: 1.125rem;
    height: 1.125rem;
    margin-top: .125rem;
    border: 1px solid var(--line-strong);
    border-radius: .3125rem;
    background: var(--surface);
    cursor: pointer;
    -webkit-appearance: none;
            appearance: none;
    transition: background-color .16s ease, border-color .16s ease;
}

.auth-check-input:hover { border-color: var(--navy-300); }

.auth-check-input:checked {
    background-color: var(--brand-button);
    border-color: var(--brand-button);
}

.auth-check-input:checked::after {
    content: "";
    display: block;
    width: 100%;
    height: 100%;
    background: #ffffff;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center / .8125rem .8125rem no-repeat;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center / .8125rem .8125rem no-repeat;
}

.auth-check-input:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px var(--ring);
}

.auth-check-label {
    font-size: .8125rem;
    line-height: 1.6;
    color: var(--fg-body);
    cursor: pointer;
}

/* --------------------------------------------------------------------------
   15. ENTRANCE MOTION
   -------------------------------------------------------------------------- */
@keyframes auth-rise {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: none; }
}

.auth-shell { animation: auth-rise .5s cubic-bezier(.16, 1, .3, 1) both; }

/* --------------------------------------------------------------------------
   16. REDUCED MOTION
   -------------------------------------------------------------------------- */
@media (prefers-reduced-motion: reduce) {
    .auth-shell,
    .auth-otp.is-shaking,
    .auth-btn.is-busy .auth-btn-spin { animation: none; }

    .auth-shell,
    .legal-overlay,
    .legal-sheet,
    .auth-btn,
    .auth-input,
    .auth-otp-box,
    .auth-strength-bar { transition: none; }

    .auth-btn:active { transform: none; }
}
</style>
