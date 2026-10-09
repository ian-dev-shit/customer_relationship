<script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            // Accent colours are driven by CSS custom properties
                            // (see includes/tailwind_css.php -> "ACCENT THEMES") so the
                            // customer Appearance settings can retint the portal at
                            // runtime via <html data-accent="...">.
                            // The <alpha-value> placeholder keeps /10, /20, ...
                            // opacity modifiers working.
                            blue: 'rgb(var(--accent-rgb) / <alpha-value>)',
                            darkblue: 'rgb(var(--accent-strong-rgb) / <alpha-value>)',
                            navy: '#182554',
                            sidebar: '#0f1734',
                            lightbg: '#f2f4f9',
                        },
                        // Priority PH brand colours - mirrors the CSS custom
                        // properties in includes/tailwind_css.php so utilities and
                        // component classes never drift apart.
                        priority: {
                            navy: '#1d2e6a',
                            button: '#213854',
                            teal: '#084163',
                            'teal-deep': '#003a5f',
                            sky: '#4e83c5',
                            surface: '#f0f0f0',
                            ink: '#1b1b1c',
                            // Pipeline stage semantics - mirrors the
                            // --stage-* custom properties in includes/tailwind_css.php.
                            stage: {
                                new:         '#8d9dd0',
                                qualifying:  '#4e83c5',
                                quote:       '#2b3f7c',
                                negotiation: '#1d2e6a',
                                won:         '#047857',
                                'won-soft':  '#ecfdf5',
                                lost:        '#b91c1c',
                                'lost-soft': '#fef2f2',
                            },
                            // Sequential chart ramp - mirrors --chart-1..6.
                            chart: {
                                1: '#1d2e6a',
                                2: '#084163',
                                3: '#4e83c5',
                                4: '#8d9dd0',
                                5: '#b9c4e3',
                                6: '#dbe1f1',
                            },
                            // Soft brand tints - the single pair that the old
                            // purple-50/100 and indigo-50/100 fills collapse to.
                            soft: '#eef1f9',
                            'softer': '#f6f8fc',
                            line: '#dbe1f1',
                            'line-strong': '#c9d0e2',
                        },
                        // "Priority Navy" design scale - mirrors the CSS custom
                        // properties in includes/tailwind_css.php so utilities and
                        // component classes never drift apart.
                        navy: {
                            50:  '#eef1f9',
                            100: '#dbe1f1',
                            200: '#b9c4e3',
                            300: '#8d9dd0',
                            400: '#5f74b5',
                            500: '#3d5494',
                            600: '#2b3f7c',
                            700: '#1d2e6a',
                            800: '#182554',
                            850: '#131d43',
                            900: '#0f1734',
                            950: '#0a0f24',
                        },
                        canvas: '#f2f4f9',
                        surface: {
                            DEFAULT: '#ffffff',
                            muted: '#eef1f7',
                            hover: '#e7ecf6',
                        },
                        line: '#dfe3ee',
                        'line-strong': '#c9d0e2',
                        neutral: {
                            'primary-soft': '#eef1f7',
                            'primary-medium': '#ffffff',
                            'secondary-medium': '#eef1f9',
                            'tertiary-medium': '#dbe1f1',
                        },
                        'default': '#dbe1f1',
                        'default-medium': '#b9c4e3',
                        'body': '#3d4451',
                        'heading': '#1b1b1c',
                        'muted': '#6e6e6e',
                    },
                    borderRadius: {
                        base: '0.5rem',
                        tile: '0.75rem',
                        card: '1rem',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'xs': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                        'card': '0 1px 2px rgba(8,14,30,.04), 0 1px 3px rgba(8,14,30,.06)',
                        'lift': '0 12px 32px -14px rgba(8,14,30,.20)',
                        'navy': '0 8px 20px -10px rgba(8,14,30,.55)',
                    }
                }
            }
        }
    </script>