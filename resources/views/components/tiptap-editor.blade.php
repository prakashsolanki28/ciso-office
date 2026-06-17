@props([
    'label' => 'Content',
    'readTime' => false,
])

{{--
    Reusable TipTap editor card (toolbar + surface + selection bubble menu).
    Must be placed inside an element with x-data="tiptapEditor(...)" and a sibling
    hidden input bound to x-ref="contentInput". Shared by the Blog and Project
    edit screens — see resources/js/editor.js for the Alpine component.
--}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</span>
        @if ($readTime)
            <span class="text-xs text-gray-400"
                  x-text="wordCount + ' words · ' + Math.ceil(wordCount / 200) + ' min read'"></span>
        @else
            <span class="text-xs text-gray-400" x-text="wordCount + ' words'"></span>
        @endif
    </div>

    {{-- Toolbar — the delegated mousedown handler stops buttons from stealing
         focus (which would collapse the selection); popover inputs stay focusable. --}}
    <div @mousedown="$event.target.closest('button') && $event.preventDefault()"
        class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-0.5 bg-gray-50 dark:bg-gray-900/20">
        {{-- History --}}
        <button type="button" @click="undo()" title="Undo" class="toolbar-btn"><i data-lucide="undo" class="h-4"></i></button>
        <button type="button" @click="redo()" title="Redo" class="toolbar-btn"><i data-lucide="redo" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>

        {{-- Headings --}}
        <button type="button" @click="setHeading(1)" :class="isActive('heading', { level: 1 }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Heading 1"><i data-lucide="heading-1" class="h-4"></i></button>
        <button type="button" @click="setHeading(2)" :class="isActive('heading', { level: 2 }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Heading 2"><i data-lucide="heading-2" class="h-4"></i></button>
        <button type="button" @click="setHeading(3)" :class="isActive('heading', { level: 3 }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Heading 3"><i data-lucide="heading-3" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>

        {{-- Inline marks --}}
        <button type="button" @click="toggleBold()" :class="isActive('bold') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Bold"><i data-lucide="bold" class="h-4"></i></button>
        <button type="button" @click="toggleItalic()" :class="isActive('italic') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Italic"><i data-lucide="italic" class="h-4"></i></button>
        <button type="button" @click="toggleUnderline()" :class="isActive('underline') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Underline"><i data-lucide="underline" class="h-4"></i></button>
        <button type="button" @click="toggleStrike()" :class="isActive('strike') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Strikethrough"><i data-lucide="strikethrough" class="h-4"></i></button>
        <button type="button" @click="toggleCode()" :class="isActive('code') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Inline code"><i data-lucide="code-xml" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>

        {{-- Alignment --}}
        <button type="button" @click="setAlign('left')" :class="isActive({ textAlign: 'left' }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Align left"><i data-lucide="text-align-start" class="h-4"></i></button>
        <button type="button" @click="setAlign('center')" :class="isActive({ textAlign: 'center' }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Align center"><i data-lucide="text-align-center" class="h-4"></i></button>
        <button type="button" @click="setAlign('right')" :class="isActive({ textAlign: 'right' }) ? 'toolbar-btn-active' : 'toolbar-btn'" title="Align right"><i data-lucide="text-align-end" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>

        {{-- Lists & blocks --}}
        <button type="button" @click="toggleBulletList()" :class="isActive('bulletList') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Bullet list">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        </button>
        <button type="button" @click="toggleOrderedList()" :class="isActive('orderedList') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Numbered list">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h14M7 12h14M7 17h14M3 7h.01M3 12h.01M3 17h.01"/></svg>
        </button>
        <button type="button" @click="toggleBlockquote()" :class="isActive('blockquote') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Blockquote">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
        </button>
        <button type="button" @click="toggleCodeBlock()" :class="isActive('codeBlock') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Code block">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10l-3 3 3 3m8-6l3 3-3 3"/></svg>
        </button>
        <button type="button" @click="insertTable()" class="toolbar-btn" title="Insert table">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
        </button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>

        {{-- Link --}}
        <div class="relative" x-data>
            <button type="button" @click="showLinkInput = !showLinkInput; showImageInput = false"
                :class="isActive('link') ? 'toolbar-btn-active' : 'toolbar-btn'" title="Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </button>
            <div x-show="showLinkInput" x-cloak @click.outside="showLinkInput = false"
                class="absolute top-full left-0 mt-1 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg p-2 flex gap-2 min-w-64">
                <input type="text" x-model="linkUrl" placeholder="https://…" @keydown.enter.prevent="insertLink()"
                    class="flex-1 text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-1 focus:ring-primary">
                <button type="button" @click="insertLink()" class="px-2 py-1 bg-primary text-white text-xs rounded">Set</button>
                <button type="button" @click="removeLink()" class="px-2 py-1 text-red-500 text-xs rounded hover:bg-red-50">Remove</button>
            </div>
        </div>

        {{-- Image --}}
        <div class="relative" x-data>
            <button type="button" @click="showImageInput = !showImageInput; showLinkInput = false" class="toolbar-btn" title="Image">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </button>
            <div x-show="showImageInput" x-cloak @click.outside="showImageInput = false"
                class="absolute top-full left-0 mt-1 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg p-2 flex gap-2 min-w-64">
                <input type="text" x-model="imageUrl" placeholder="https://…" @keydown.enter.prevent="insertImage()"
                    class="flex-1 text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded focus:ring-1 focus:ring-primary">
                <button type="button" @click="insertImage()" class="px-2 py-1 bg-primary text-white text-xs rounded">Insert</button>
            </div>
        </div>

        {{-- Contextual table controls (only while the cursor is inside a table) --}}
        <template x-if="isActive('table')">
            <div class="flex items-center gap-0.5">
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></div>
                <button type="button" @click="addColumnAfter()" class="toolbar-btn" title="Add column">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4H5a1 1 0 00-1 1v14a1 1 0 001 1h6M11 4h0v16M18 9v6m3-3h-6"/></svg>
                </button>
                <button type="button" @click="addRowAfter()" class="toolbar-btn" title="Add row">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 11V5a1 1 0 011-1h14a1 1 0 011 1v6M4 11h16M4 11v0M9 18h6m-3-3v6"/></svg>
                </button>
                <button type="button" @click="deleteColumn()" class="toolbar-btn" title="Delete column">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4H5a1 1 0 00-1 1v14a1 1 0 001 1h4M9 4h6a1 1 0 011 1v3M9 4v16m6 0h0M15 13l6 6m0-6l-6 6"/></svg>
                </button>
                <button type="button" @click="deleteRow()" class="toolbar-btn" title="Delete row">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 9V5a1 1 0 011-1h14a1 1 0 011 1v4M4 9h16M4 9v6a1 1 0 001 1h3M20 9v0M13 15l6 6m0-6l-6 6"/></svg>
                </button>
                <button type="button" @click="deleteTable()" class="toolbar-btn text-red-500" title="Delete table">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M4 7h16"/></svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Editor surface --}}
    <div x-ref="editorContent" class="min-h-[500px] blog-content"></div>

    {{-- Notion-style selection bubble menu --}}
    <div x-ref="bubbleMenu" x-cloak style="visibility:hidden"
        class="flex items-center gap-0.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl px-1 py-1">
        <button type="button" @mousedown.prevent @click="toggleBold()" :class="isActive('bold') ? 'bubble-btn-active' : 'bubble-btn'" title="Bold"><i data-lucide="bold" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="toggleItalic()" :class="isActive('italic') ? 'bubble-btn-active' : 'bubble-btn'" title="Italic"><i data-lucide="italic" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="toggleUnderline()" :class="isActive('underline') ? 'bubble-btn-active' : 'bubble-btn'" title="Underline"><i data-lucide="underline" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="toggleStrike()" :class="isActive('strike') ? 'bubble-btn-active' : 'bubble-btn'" title="Strikethrough"><i data-lucide="strikethrough" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="toggleCode()" :class="isActive('code') ? 'bubble-btn-active' : 'bubble-btn'" title="Inline code"><i data-lucide="code-xml" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-0.5"></div>

        <button type="button" @mousedown.prevent @click="setHeading(1)" :class="isActive('heading', { level: 1 }) ? 'bubble-btn-active' : 'bubble-btn'" title="Heading 1"><i data-lucide="heading-1" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="setHeading(2)" :class="isActive('heading', { level: 2 }) ? 'bubble-btn-active' : 'bubble-btn'" title="Heading 2"><i data-lucide="heading-2" class="h-4"></i></button>
        <button type="button" @mousedown.prevent @click="setHeading(3)" :class="isActive('heading', { level: 3 }) ? 'bubble-btn-active' : 'bubble-btn'" title="Heading 3"><i data-lucide="heading-3" class="h-4"></i></button>

        <div class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-0.5"></div>

        <button type="button" @mousedown.prevent @click="toggleBulletList()" :class="isActive('bulletList') ? 'bubble-btn-active' : 'bubble-btn'" title="Bullet list">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="toggleBlockquote()" :class="isActive('blockquote') ? 'bubble-btn-active' : 'bubble-btn'" title="Quote">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
        </button>
        <button type="button" @mousedown.prevent @click="showLinkInput = true; showImageInput = false"
            :class="isActive('link') ? 'bubble-btn-active' : 'bubble-btn'" title="Link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </button>
    </div>
</div>
