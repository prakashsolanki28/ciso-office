import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import CharacterCount from '@tiptap/extension-character-count';
import BubbleMenu from '@tiptap/extension-bubble-menu';
import { Table } from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableHeader from '@tiptap/extension-table-header';
import TableCell from '@tiptap/extension-table-cell';
import SlashCommand from './slash-command.js';
import Callout from './callout.js';

/**
 * Alpine component factory for the TipTap rich-text editor used on the
 * Blog and Project edit screens.
 *
 * IMPORTANT: the TipTap `Editor` instance is held in a closure variable, NOT on
 * the reactive Alpine component. If it lived on `this`, Alpine would wrap it in
 * a reactive Proxy; ProseMirror compares its state objects by identity, the
 * Proxy breaks that comparison, and every command throws "Applying a mismatched
 * transaction" — typing works but Bold/Italic/headings/etc. silently do nothing.
 *
 * Because the editor is non-reactive, toolbar active states (`isActive`) can't be
 * tracked by Alpine automatically, so we bump a reactive `selectionKey` on every
 * transaction and read it inside `isActive` to force `:class` bindings to refresh.
 *
 * StarterKit v3 already bundles Link and Underline, so they are configured via
 * StarterKit options rather than registered again (duplicates warn and conflict).
 */
export default function tiptapEditor(initialContent = '', placeholder = 'Start writing here…') {
    let editor = null; // non-reactive — see note above

    return {
        wordCount: 0,
        charCount: 0,
        selectionKey: 0,
        linkUrl: '',
        imageUrl: '',
        showLinkInput: false,
        showImageInput: false,

        /** Alpine calls init() automatically; defer to $nextTick so x-refs exist. */
        init() {
            this.$nextTick(() => this.mount());
        },

        mount() {
            if (!this.$refs.editorContent || editor) return;

            const extensions = [
                StarterKit.configure({
                    heading: { levels: [1, 2, 3] },
                    link: {
                        openOnClick: false,
                        autolink: true,
                        HTMLAttributes: { class: 'text-blue-600 underline' },
                    },
                }),
                Image.configure({
                    HTMLAttributes: { class: 'max-w-full rounded-lg my-4' },
                }),
                TextAlign.configure({ types: ['heading', 'paragraph'] }),
                Placeholder.configure({
                    placeholder: ({ node }) =>
                        node.type.name === 'heading' ? 'Heading' : placeholder,
                    includeChildren: true,
                }),
                CharacterCount,
                Table.configure({ resizable: true, HTMLAttributes: { class: 'editor-table' } }),
                TableRow,
                TableHeader,
                TableCell,
                Callout,
                // Notion-style "/" command palette.
                SlashCommand({ onImage: () => { this.showLinkInput = false; this.showImageInput = true; } }),
            ];

            // Notion-style selection toolbar — only if the page provides the element.
            if (this.$refs.bubbleMenu) {
                extensions.push(BubbleMenu.configure({
                    element: this.$refs.bubbleMenu,
                    options: { placement: 'top', offset: 8 },
                    shouldShow: ({ editor, from, to }) =>
                        editor.isEditable &&
                        from !== to &&
                        !editor.isActive('codeBlock') &&
                        !editor.isActive('image'),
                }));
            }

            editor = new Editor({
                element: this.$refs.editorContent,
                extensions,
                content: initialContent ?? '',
                editorProps: {
                    attributes: {
                        class: 'prose prose-lg max-w-none focus:outline-none min-h-[400px] px-6 py-4',
                    },
                },
                onCreate: ({ editor }) => this.refreshCounts(editor),
                onUpdate: ({ editor }) => {
                    this.refreshCounts(editor);
                    this.syncContent();
                },
                // Bump the reactive key so toolbar :class bindings re-evaluate.
                onSelectionUpdate: () => { this.selectionKey++; },
                onTransaction: () => { this.selectionKey++; },
            });
        },

        /** Alpine calls destroy() automatically when the component unmounts. */
        destroy() {
            editor?.destroy();
            editor = null;
        },

        refreshCounts(ed) {
            this.wordCount = ed.storage.characterCount.words();
            this.charCount = ed.storage.characterCount.characters();
        },

        /** Mirror editor HTML into the hidden input so the form submits it. */
        syncContent() {
            if (editor && this.$refs.contentInput) {
                this.$refs.contentInput.value = editor.getHTML();
            }
        },

        isActive(type, opts = {}) {
            // Touch selectionKey so Alpine re-runs this when the selection changes.
            void this.selectionKey;
            return editor?.isActive(type, opts) ?? false;
        },

        /** Run a chained command with focus; no-op until the editor is ready. */
        cmd(fn) {
            if (!editor) return;
            fn(editor.chain().focus());
        },

        toggleBold() { this.cmd(c => c.toggleBold().run()); },
        toggleItalic() { this.cmd(c => c.toggleItalic().run()); },
        toggleUnderline() { this.cmd(c => c.toggleUnderline().run()); },
        toggleStrike() { this.cmd(c => c.toggleStrike().run()); },
        toggleCode() { this.cmd(c => c.toggleCode().run()); },
        toggleCodeBlock() { this.cmd(c => c.toggleCodeBlock().run()); },
        toggleBlockquote() { this.cmd(c => c.toggleBlockquote().run()); },
        toggleBulletList() { this.cmd(c => c.toggleBulletList().run()); },
        toggleOrderedList() { this.cmd(c => c.toggleOrderedList().run()); },
        setHeading(level) { this.cmd(c => c.toggleHeading({ level }).run()); },
        setAlign(align) { this.cmd(c => c.setTextAlign(align).run()); },
        undo() { this.cmd(c => c.undo().run()); },
        redo() { this.cmd(c => c.redo().run()); },

        // Table
        insertTable() { this.cmd(c => c.insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()); },
        addColumnAfter() { this.cmd(c => c.addColumnAfter().run()); },
        addColumnBefore() { this.cmd(c => c.addColumnBefore().run()); },
        deleteColumn() { this.cmd(c => c.deleteColumn().run()); },
        addRowAfter() { this.cmd(c => c.addRowAfter().run()); },
        addRowBefore() { this.cmd(c => c.addRowBefore().run()); },
        deleteRow() { this.cmd(c => c.deleteRow().run()); },
        toggleHeaderRow() { this.cmd(c => c.toggleHeaderRow().run()); },
        deleteTable() { this.cmd(c => c.deleteTable().run()); },

        /** Normalise a user-entered URL, adding https:// when no scheme is given. */
        normalizeUrl(value) {
            const url = (value || '').trim();
            if (!url) return '';
            if (/^(https?:|mailto:|tel:|\/|#)/i.test(url)) return url;
            return `https://${url}`;
        },

        insertLink() {
            const href = this.normalizeUrl(this.linkUrl);
            if (!href) return;
            this.cmd(c => c.extendMarkRange('link').setLink({ href }).run());
            this.linkUrl = '';
            this.showLinkInput = false;
        },

        removeLink() {
            this.cmd(c => c.extendMarkRange('link').unsetLink().run());
            this.linkUrl = '';
            this.showLinkInput = false;
        },

        insertImage() {
            const src = this.normalizeUrl(this.imageUrl);
            if (!src) return;
            this.cmd(c => c.setImage({ src }).run());
            this.imageUrl = '';
            this.showImageInput = false;
        },
    };
}
