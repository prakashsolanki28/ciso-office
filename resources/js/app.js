import Alpine from 'alpinejs';
import tiptapEditor from './editor.js';
import * as _lucideAll from 'lucide';
import { createIcons } from 'lucide';

window.Alpine = Alpine;

Alpine.data('tiptapEditor', tiptapEditor);

Alpine.start();

// Full icon map: filter out non-icon exports (createIcons fn, defaultAttributes obj, etc.)
const _allIcons = Object.fromEntries(
    Object.entries(_lucideAll).filter(([, v]) => Array.isArray(v))
);

// Toolbar + any static data-lucide elements rendered on page load.
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons: _allIcons });
});

// Called by Alpine components after they dynamically inject <i data-lucide="…"> elements.
window._lucideCreate = () => createIcons({ icons: _allIcons });
