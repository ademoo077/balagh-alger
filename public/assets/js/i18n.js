const I18n = {
    lang: 'fr',
    dir: 'ltr',
    translations: {},
    locales: {
        fr: { dir: 'ltr', label: 'Français', flag: '🇫🇷', dateFormat: 'd/m/Y', decimal: ',', thousand: ' ' },
        ar: { dir: 'rtl', label: 'العربية', flag: '🇩🇿', dateFormat: 'Y/m/d', decimal: '.', thousand: ',' }
    },

    init() {
        this.lang = localStorage.getItem('lang') || document.documentElement.getAttribute('lang') || 'fr';
        if (!this.locales[this.lang]) this.lang = 'fr';
        this.translations = window.__translations || {};
        this.dir = this.locales[this.lang].dir;
        this.apply();
    },

    setLang(lang) {
        if (!this.locales[lang]) return;
        this.lang = lang;
        this.dir = this.locales[lang].dir;
        localStorage.setItem('lang', lang);
        document.cookie = `lang=${lang};path=/;max-age=${365*86400}`;
        this.apply();
        // Notify server
        fetch('/api/set-lang', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lang })
        });
        // Reload to apply PHP translations
        location.reload();
    },

    apply() {
        document.documentElement.setAttribute('lang', this.lang);
        document.documentElement.setAttribute('dir', this.dir);
        if (this.dir === 'rtl') {
            document.body.classList.add('rtl');
        } else {
            document.body.classList.remove('rtl');
        }
    },

    t(key, replacements = {}) {
        const keys = key.split('.');
        let val = this.translations;
        for (const k of keys) {
            if (val && typeof val === 'object' && k in val) {
                val = val[k];
            } else {
                return key;
            }
        }
        if (typeof val !== 'string') return key;
        for (const [k, v] of Object.entries(replacements)) {
            val = val.replace(new RegExp(`:${k}`, 'g'), v);
        }
        return val;
    },

    formatNumber(num) {
        const loc = this.locales[this.lang];
        return new Intl.NumberFormat(this.lang === 'ar' ? 'ar-DZ' : 'fr-FR', {
            maximumFractionDigits: 0
        }).format(num);
    },

    formatDate(dateStr) {
        const d = new Date(dateStr);
        if (this.lang === 'ar') {
            return `${d.getFullYear()}/${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}`;
        }
        return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    },

    formatDateTime(dateStr) {
        const d = new Date(dateStr);
        const time = `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
        return this.formatDate(dateStr) + ' ' + time;
    },

    timeAgo(dateStr) {
        const diff = (Date.now() - new Date(dateStr).getTime()) / 1000;
        if (diff < 60) return this.t('time.just_now');
        if (diff < 3600) { const m = Math.floor(diff/60); return this.t(m > 1 ? 'time.minutes_ago' : 'time.minute_ago', { count: m }); }
        if (diff < 86400) { const h = Math.floor(diff/3600); return this.t(h > 1 ? 'time.hours_ago' : 'time.hour_ago', { count: h }); }
        const days = Math.floor(diff/86400);
        if (days < 30) return this.t(days > 1 ? 'time.days_ago' : 'time.day_ago', { count: days });
        const months = Math.floor(days/30);
        if (months < 12) return this.t(months > 1 ? 'time.months_ago' : 'time.month_ago', { count: months });
        const years = Math.floor(months/12);
        return this.t(years > 1 ? 'time.years_ago' : 'time.year_ago', { count: years });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    I18n.init();

    // Apply translations to elements with data-i18n attribute
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        const text = I18n.t(key);
        if (text !== key) el.textContent = text;
    });

    // Apply to placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        const text = I18n.t(key);
        if (text !== key) el.placeholder = text;
    });

    // Apply to titles
    document.querySelectorAll('[data-i18n-title]').forEach(el => {
        const key = el.getAttribute('data-i18n-title');
        const text = I18n.t(key);
        if (text !== key) el.title = text;
    });
});
