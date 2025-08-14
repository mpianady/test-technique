export class MapLoader {
    constructor() {
        this.el = document.getElementById('map-loader');
        this.host = document.getElementById('map');
        this.legend = document.getElementById('legend');
    }

    show(msg = 'Chargement…') {
        if (this.el) {
            this.el.classList.remove('hidden');
            const text = this.el.querySelector('span');
            if (text) text.textContent = msg;
        }
        if (this.host) this.host.setAttribute('aria-busy', 'true');

        if (this.legend) {
            this.legend.setAttribute('aria-busy', 'true');
            this.legend.setAttribute('hidden', '');
        }
    }

    message(msg) {
        if (!this.el) return;
        const text = this.el.querySelector('span');
        if (text) text.textContent = msg;
    }

    hide() {
        if (this.el) this.el.classList.add('hidden');
        if (this.host) this.host.setAttribute('aria-busy', 'false');

        if (this.legend) {
            this.legend.removeAttribute('hidden');
            this.legend.setAttribute('aria-busy', 'false');
        }
    }

    error(msg = 'Erreur de chargement') {
        if (this.el) {
            const text = this.el.querySelector('span');
            if (text) text.textContent = msg;
        }
        if (this.host) this.host.setAttribute('aria-busy', 'false');
        if (this.legend) {
            this.legend.setAttribute('aria-busy', 'false');
        }
    }
}
