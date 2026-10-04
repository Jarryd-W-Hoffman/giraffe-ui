{{-- Shared by the catalog and the full-page examples: meta, dark mode, the theme customiser's choices and the CSS. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} · GiraffeUI</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%234f46e5'/%3E%3Ctext x='16' y='23' font-family='system-ui,sans-serif' font-size='20' font-weight='700' fill='white' text-anchor='middle'%3EG%3C/text%3E%3C/svg%3E">

<script>
    // Same dark mode helper the published layout ships with.
    window.GiraffeUI = {
        setTheme(theme) {
            try {
                theme === 'system' ? localStorage.removeItem('theme') : localStorage.setItem('theme', theme);
            } catch (e) {}
            this.applyTheme();
        },
        applyTheme() {
            let saved = null;
            try { saved = localStorage.getItem('theme'); } catch (e) {}
            const dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        },
    };
    GiraffeUI.applyTheme();

    // The theme customiser's choices, kept across catalog pages.
    window.Catalog = {
        defaults: { primary: 'indigo', radius: 'md' },
        load() {
            try {
                return { ...this.defaults, ...JSON.parse(localStorage.getItem('catalog-theme') || '{}') };
            } catch (e) {
                return { ...this.defaults };
            }
        },
        save(theme) {
            try { localStorage.setItem('catalog-theme', JSON.stringify(theme)); } catch (e) {}
            this.apply(theme);
        },
        radiusValue(radius) {
            return radius === 'none' ? '0px' : `var(--radius-${radius})`;
        },
        apply(theme) {
            // A <style> tag rather than inline styles, so the dark-mode text shade can apply too.
            let style = document.getElementById('catalog-theme');

            if (! style) {
                style = document.createElement('style');
                style.id = 'catalog-theme';
                document.head.appendChild(style);
            }

            const rules = [];

            if (theme.primary !== this.defaults.primary) {
                rules.push(`:root { --color-gui-primary: var(--color-${theme.primary}-600); }`);
                rules.push(`:root.dark { --color-gui-primary-text: var(--color-${theme.primary}-400); }`);
            }

            if (theme.radius !== this.defaults.radius) {
                rules.push(`:root { --radius-gui: ${this.radiusValue(theme.radius)}; }`);
            }

            style.textContent = rules.join('\n');
        },
    };
    Catalog.apply(Catalog.load());
</script>

<link rel="stylesheet" href="/catalog/assets/catalog.css?v={{ time() }}">
