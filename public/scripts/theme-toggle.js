document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('themeToggle');
    const icon = themeToggleBtn.querySelector('i');
    const root = document.documentElement;
    
    // Theme variables
    const themes = {
        light: {
            '--primary-color': '#2563eb',
            '--primary-hover': '#1d4ed8',
            '--error-color': '#dc2626',
            '--success-color': '#16a34a',
            '--border-color': '#e5e7eb',
            '--text-color': '#1f2937',
            '--bg-color': '#f9fafb',
            '--input-bg': '#ffffff'
        },
        dark: {
            '--primary-color': '#4f94ff', // Brighter blue for better visibility
            '--primary-hover': '#2563eb',
            '--error-color': '#ff4444',
            '--success-color': '#22c55e',
            '--border-color': '#2e3644', // More visible border
            '--text-color': '#e2e8f0', // Much lighter text for better contrast
            '--bg-color': '#000000', // Pure black background
            '--input-bg': '#1a1d24' // Slightly lighter input background
        }
    };

    // Additional CSS variables for dark theme
    const darkModeExtras = {
        '--secondary-text': '#94a3b8', // Lighter secondary text
        '--card-bg': '#111318', // Dark but distinguishable card background
        '--card-border': '#2e3644', // Visible card border
        '--hover-bg': '#1e2128', // Lighter hover state
        '--header-text': '#ffffff', // Pure white for headers
        '--link-color': '#4f94ff', // Bright blue for links
        '--disabled-text': '#94a3b8', // Lighter gray for disabled elements
        '--alert-bg': '#1e2128', // Dark alert background
        '--nav-bg': '#0c0d10', // Dark navigation background
        '--nav-text': '#e2e8f0', // Light navigation text
        '--nav-border': '#2e3644', // Navigation border
        '--breadcrumb-text': '#94a3b8', // Breadcrumb text color
        '--breadcrumb-hover': '#ffffff', // Breadcrumb hover color
        '--input-text': '#ffffff', // Input text color
        '--input-border': '#2e3644', // Input border color
        '--input-border-focus': '#4f94ff', // Input focus border color
        '--button-secondary-bg': '#1a1d24', // Secondary button background
        '--button-secondary-text': '#e2e8f0', // Secondary button text
        '--button-secondary-border': '#2e3644' // Secondary button border
    };

    // Apply theme to document
    function applyTheme(theme) {
        const themeVars = themes[theme];
        for (const [property, value] of Object.entries(themeVars)) {
            root.style.setProperty(property, value);
        }

        if (theme === 'dark') {
            // Apply additional dark mode styles
            for (const [property, value] of Object.entries(darkModeExtras)) {
                root.style.setProperty(property, value);
            }
            
            // Style specific elements for dark mode
            styleForDarkMode();
        } else {
            // Reset dark mode specific styles
            for (const property of Object.keys(darkModeExtras)) {
                root.style.removeProperty(property);
            }
            
            // Reset specific elements to light mode
            resetToLightMode();
        }

        // Update icon
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        
        // Update body class
        document.body.classList.remove('theme-light', 'theme-dark');
        document.body.classList.add(`theme-${theme}`);

        // Store preference
        localStorage.setItem('theme', theme);
    }

    function styleForDarkMode() {
        // Style cards
        document.querySelectorAll('.card').forEach(card => {
            card.style.backgroundColor = 'var(--card-bg)';
            card.style.borderColor = 'var(--card-border)';
            card.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -2px rgba(0, 0, 0, 0.1)';
        });

        // Style top navigation
        const topNav = document.querySelector('.top-nav');
        if (topNav) {
            topNav.style.backgroundColor = 'var(--nav-bg)';
            topNav.style.borderColor = 'var(--nav-border)';
        }

        // Style breadcrumbs
        document.querySelectorAll('.breadcrumb a').forEach(link => {
            link.style.color = 'var(--breadcrumb-text)';
        });

        // Style inputs
        document.querySelectorAll('input, select, textarea').forEach(input => {
            input.style.backgroundColor = 'var(--input-bg)';
            input.style.color = 'var(--input-text)';
            input.style.borderColor = 'var(--input-border)';
        });

        // Style secondary buttons
        document.querySelectorAll('.btn-secondary').forEach(button => {
            button.style.backgroundColor = 'var(--button-secondary-bg)';
            button.style.color = 'var(--button-secondary-text)';
            button.style.borderColor = 'var(--button-secondary-border)';
        });

        // Style theme toggle
        themeToggleBtn.style.color = 'var(--nav-text)';
    }

    function resetToLightMode() {
        const elementsToReset = document.querySelectorAll('.card, .top-nav, .breadcrumb a, input, select, textarea, .btn-secondary');
        elementsToReset.forEach(element => {
            element.style.removeProperty('background-color');
            element.style.removeProperty('color');
            element.style.removeProperty('border-color');
            element.style.removeProperty('box-shadow');
        });

        themeToggleBtn.style.removeProperty('color');
    }

    // Check for saved theme preference or system preference
    function getInitialTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            return savedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    // Initialize theme
    const initialTheme = getInitialTheme();
    applyTheme(initialTheme);

    // Handle button click
    themeToggleBtn.addEventListener('click', function() {
        const currentTheme = localStorage.getItem('theme') || initialTheme;
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);

        // Add animation to icon
        icon.style.animation = 'rotate 0.5s ease';
        setTimeout(() => {
            icon.style.animation = '';
        }, 500);
    });

    // Handle system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });

    // Add necessary CSS for theme transitions
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .theme-toggle {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        .theme-toggle i {
            transition: color 0.3s ease;
        }
        
        .theme-dark {
            color-scheme: dark;
        }
        
        .theme-dark .breadcrumb a:hover {
            color: var(--breadcrumb-hover);
        }
        
        .theme-dark input::placeholder {
            color: #64748b;
        }
        
        .theme-dark input:focus {
            border-color: var(--input-border-focus);
            box-shadow: 0 0 0 3px rgba(79, 148, 255, 0.2);
        }
        
        .theme-dark .btn-secondary:hover {
            background-color: var(--hover-bg);
        }
        
        .theme-light {
            color-scheme: light;
        }
        
        /* Smooth transitions for theme changes */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});