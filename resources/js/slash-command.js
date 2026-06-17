import { Extension } from '@tiptap/core';
import Suggestion from '@tiptap/suggestion';

/* Inline SVG icons (stroke = currentColor) used by the slash menu. */
const icon = (paths) =>
    `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${paths}</svg>`;

const ICONS = {
    text:    icon('<path d="M4 7h16M4 12h16M4 17h10"/>'),
    h1:      icon('<path d="M4 6v12M12 6v12M4 12h8"/><path d="M17 9l3-1v10"/>'),
    h2:      icon('<path d="M4 6v12M12 6v12M4 12h8"/><path d="M16 10a2 2 0 1 1 4 0c0 1.5-1.5 2.5-4 5h4"/>'),
    h3:      icon('<path d="M4 6v12M12 6v12M4 12h8"/><path d="M16 9a2 2 0 1 1 3 1.6A2 2 0 0 1 16 15"/>'),
    bullet:  icon('<circle cx="5" cy="7" r="1"/><circle cx="5" cy="12" r="1"/><circle cx="5" cy="17" r="1"/><path d="M9 7h11M9 12h11M9 17h11"/>'),
    ordered: icon('<path d="M9 7h11M9 12h11M9 17h11M4 6h1v4M4 10h2"/>'),
    quote:   icon('<path d="M7 7H4v6h3l-1 4M17 7h-3v6h3l-1 4"/>'),
    code:    icon('<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M8 10l-3 3 3 3m8-6l3 3-3 3"/>'),
    divider: icon('<path d="M4 12h16"/>'),
    image:   icon('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>'),
    table:   icon('<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>'),
    callout: icon('<path d="M12 2a7 7 0 0 1 4 12.7V17a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-2.3A7 7 0 0 1 12 2z"/><path d="M9 21h6"/>'),
};

/**
 * Returns the Notion-style command palette items. `onImage` lets the editor
 * component open its own image popover instead of forcing a browser prompt.
 */
function buildItems({ onImage }) {
    return [
        {
            title: 'Text', desc: 'Plain paragraph', icon: ICONS.text,
            keywords: ['text', 'paragraph', 'plain', 'p'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).setParagraph().run(),
        },
        {
            title: 'Heading 1', desc: 'Large section heading', icon: ICONS.h1,
            keywords: ['h1', 'heading', 'title', 'big'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).setNode('heading', { level: 1 }).run(),
        },
        {
            title: 'Heading 2', desc: 'Medium section heading', icon: ICONS.h2,
            keywords: ['h2', 'heading', 'subtitle'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).setNode('heading', { level: 2 }).run(),
        },
        {
            title: 'Heading 3', desc: 'Small section heading', icon: ICONS.h3,
            keywords: ['h3', 'heading'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).setNode('heading', { level: 3 }).run(),
        },
        {
            title: 'Bulleted list', desc: 'Simple bulleted list', icon: ICONS.bullet,
            keywords: ['bullet', 'unordered', 'ul', 'list'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).toggleBulletList().run(),
        },
        {
            title: 'Numbered list', desc: 'List with numbering', icon: ICONS.ordered,
            keywords: ['numbered', 'ordered', 'ol', 'list'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).toggleOrderedList().run(),
        },
        {
            title: 'Quote', desc: 'Capture a quotation', icon: ICONS.quote,
            keywords: ['quote', 'blockquote', 'citation'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).toggleBlockquote().run(),
        },
        {
            title: 'Code block', desc: 'Capture a code snippet', icon: ICONS.code,
            keywords: ['code', 'codeblock', 'pre', 'snippet'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).toggleCodeBlock().run(),
        },
        {
            title: 'Table', desc: '3×3 table with header row', icon: ICONS.table,
            keywords: ['table', 'grid', 'rows', 'columns', 'cells'],
            command: ({ editor, range }) =>
                editor.chain().focus().deleteRange(range)
                    .insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(),
        },
        {
            title: 'Callout', desc: 'Highlighted info box', icon: ICONS.callout,
            keywords: ['callout', 'note', 'info', 'tip', 'highlight', 'panel'],
            command: ({ editor, range }) =>
                editor.chain().focus().deleteRange(range).setCallout({ type: 'info' }).run(),
        },
        {
            title: 'Divider', desc: 'Visual separator', icon: ICONS.divider,
            keywords: ['divider', 'hr', 'rule', 'separator', 'line'],
            command: ({ editor, range }) => editor.chain().focus().deleteRange(range).setHorizontalRule().run(),
        },
        {
            title: 'Image', desc: 'Embed an image by URL', icon: ICONS.image,
            keywords: ['image', 'img', 'picture', 'photo'],
            command: ({ editor, range }) => {
                editor.chain().focus().deleteRange(range).run();
                onImage?.();
            },
        },
    ];
}

function filterItems(items, query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) return items;
    return items.filter(i =>
        i.title.toLowerCase().includes(q) ||
        i.keywords.some(k => k.includes(q))
    );
}

/* Builds the floating popup renderer for the suggestion utility. */
function createRenderer() {
    let popup = null;
    let items = [];
    let selected = 0;
    let command = null;

    const render = () => {
        popup.innerHTML = '';
        if (!items.length) {
            const empty = document.createElement('div');
            empty.className = 'slash-empty';
            empty.textContent = 'No matching blocks';
            popup.appendChild(empty);
            return;
        }
        items.forEach((item, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'slash-item' + (i === selected ? ' is-selected' : '');
            btn.innerHTML =
                `<span class="slash-item-icon">${item.icon}</span>` +
                `<span class="slash-item-body">` +
                `<span class="slash-item-title">${item.title}</span>` +
                `<span class="slash-item-desc">${item.desc}</span>` +
                `</span>`;
            // Keep the editor selection while clicking.
            btn.addEventListener('mousedown', (e) => e.preventDefault());
            btn.addEventListener('click', () => choose(i));
            if (i === selected) requestAnimationFrame(() => btn.scrollIntoView({ block: 'nearest' }));
            popup.appendChild(btn);
        });
    };

    const choose = (i) => {
        const item = items[i];
        if (item && command) command(item);
    };

    const position = (rect) => {
        if (!rect) return;
        const margin = 8;
        const height = popup.offsetHeight || 320;
        const width = popup.offsetWidth || 280;
        let top = rect.bottom + margin;
        if (top + height > window.innerHeight - margin) {
            top = Math.max(margin, rect.top - height - margin);
        }
        let left = rect.left;
        if (left + width > window.innerWidth - margin) {
            left = Math.max(margin, window.innerWidth - width - margin);
        }
        popup.style.top = `${top}px`;
        popup.style.left = `${left}px`;
    };

    return {
        onStart: (props) => {
            items = props.items;
            command = props.command;
            selected = 0;
            popup = document.createElement('div');
            popup.className = 'slash-menu';
            document.body.appendChild(popup);
            render();
            position(props.clientRect?.());
        },
        onUpdate: (props) => {
            items = props.items;
            command = props.command;
            if (selected > items.length - 1) selected = 0;
            render();
            position(props.clientRect?.());
        },
        onKeyDown: (props) => {
            const key = props.event.key;
            if (!items.length) return key === 'Enter';
            if (key === 'ArrowDown') { selected = (selected + 1) % items.length; render(); return true; }
            if (key === 'ArrowUp') { selected = (selected - 1 + items.length) % items.length; render(); return true; }
            if (key === 'Enter') { choose(selected); return true; }
            return false;
        },
        onExit: () => {
            popup?.remove();
            popup = null;
        },
    };
}

/**
 * Notion-style slash command extension. Type "/" to open a block menu.
 */
export default function SlashCommand({ onImage } = {}) {
    const items = buildItems({ onImage });

    return Extension.create({
        name: 'slashCommand',

        addOptions() {
            return {
                suggestion: {
                    char: '/',
                    startOfLine: false,
                    command: ({ editor, range, props }) => props.command({ editor, range }),
                },
            };
        },

        addProseMirrorPlugins() {
            return [
                Suggestion({
                    editor: this.editor,
                    ...this.options.suggestion,
                    items: ({ query }) => filterItems(items, query),
                    render: createRenderer,
                }),
            ];
        },
    });
}
