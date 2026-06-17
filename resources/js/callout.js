import { Node, mergeAttributes } from '@tiptap/core';

/**
 * A Notion-style callout: a highlighted block that can contain any block
 * content. The `type` attribute (info | warning | success | danger) drives the
 * colour + icon via CSS (see `.callout` rules in app.css).
 */
const Callout = Node.create({
    name: 'callout',
    group: 'block',
    content: 'block+',
    defining: true,

    addAttributes() {
        return {
            type: {
                default: 'info',
                parseHTML: (el) => el.getAttribute('data-type') || 'info',
                renderHTML: (attrs) => ({ 'data-type': attrs.type }),
            },
        };
    },

    parseHTML() {
        return [{ tag: 'div[data-callout]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(HTMLAttributes, { 'data-callout': '', class: 'callout' }), 0];
    },

    addCommands() {
        return {
            setCallout: (attrs = {}) => ({ commands }) => commands.wrapIn(this.name, attrs),
            toggleCallout: (attrs = {}) => ({ commands }) => commands.toggleWrap(this.name, attrs),
            unsetCallout: () => ({ commands }) => commands.lift(this.name),
        };
    },
});

export default Callout;
