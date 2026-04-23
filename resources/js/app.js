import { marked } from 'marked'
import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import { Markdown } from '@tiptap/markdown'

document.addEventListener('DOMContentLoaded', () => {
    const previewMap = {
        'preview-readme':  'readme',
        'preview-conduct': 'conduct',
        'preview-law':     'law',
    }
    const files = window.__projectFiles || {}
    Object.entries(previewMap).forEach(([id, key]) => {
        const el = document.getElementById(id)
        if (!el) return
        new Editor({
            element: el,
            extensions: [StarterKit, Markdown],
            content: files[key] || '',
            contentType: 'markdown',
            editable: false,
        })
    })
})

document.addEventListener('DOMContentLoaded', () => {
    const editorEl = document.getElementById('editor')
    if (!editorEl) return

    const savedContent = window.__editorContent || ''

    const editor = new Editor({
        element: editorEl,
        extensions: [
            StarterKit,
            Markdown,
        ],
        content: savedContent,
        contentType: 'markdown',
        editorProps: {
            attributes: {
                class: 'min-h-full',
            },
        },
    })

    const toolbarActions = {
        bold: () => editor.chain().focus().toggleBold().run(),
        italic: () => editor.chain().focus().toggleItalic().run(),
        strike: () => editor.chain().focus().toggleStrike().run(),
        code: () => editor.chain().focus().toggleCode().run(),
        h1: () => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        h2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        h3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        bulletList: () => editor.chain().focus().toggleBulletList().run(),
        orderedList: () => editor.chain().focus().toggleOrderedList().run(),
        blockquote: () => editor.chain().focus().toggleBlockquote().run(),
        codeBlock: () => editor.chain().focus().toggleCodeBlock().run(),
        horizontalRule: () => editor.chain().focus().setHorizontalRule().run(),
        undo: () => editor.chain().focus().undo().run(),
        redo: () => editor.chain().focus().redo().run(),
    }

    document.querySelectorAll('[data-action]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action
            if (toolbarActions[action]) toolbarActions[action]()
            updateActiveStates()
        })
    })

    const activeChecks = {
        bold: () => editor.isActive('bold'),
        italic: () => editor.isActive('italic'),
        strike: () => editor.isActive('strike'),
        code: () => editor.isActive('code'),
        h1: () => editor.isActive('heading', { level: 1 }),
        h2: () => editor.isActive('heading', { level: 2 }),
        h3: () => editor.isActive('heading', { level: 3 }),
        bulletList: () => editor.isActive('bulletList'),
        orderedList: () => editor.isActive('orderedList'),
        blockquote: () => editor.isActive('blockquote'),
        codeBlock: () => editor.isActive('codeBlock'),
    }

    const updateActiveStates = () => {
        document.querySelectorAll('[data-action]').forEach((btn) => {
            const check = activeChecks[btn.dataset.action]
            if (check) btn.classList.toggle('is-active', check())
        })
    }

    const markdownOutput = document.getElementById('markdown-output')
    const wordCountEl = document.getElementById('word-count')

    const syncSidePanel = () => {
        const markdown = typeof editor.getMarkdown === 'function' ? editor.getMarkdown() : ''
        if (markdownOutput) markdownOutput.value = markdown

        if (wordCountEl) {
            const text = editor.getText().trim()
            const count = text ? text.split(/\s+/).length : 0
            wordCountEl.textContent = `${count} ${count === 1 ? 'parola' : 'parole'}`
        }
    }

    editor.on('selectionUpdate', updateActiveStates)
    editor.on('transaction', () => {
        updateActiveStates()
        syncSidePanel()
    })

    syncSidePanel()

    // Save
    const saveBtn = document.getElementById('save-btn')
    const saveStatus = document.getElementById('save-status')

    const save = async () => {
        saveBtn.disabled = true
        saveStatus.textContent = 'Salvataggio...'

        try {
            const res = await fetch(window.__saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ markdown: typeof editor.getMarkdown === 'function' ? editor.getMarkdown() : '' }),
            })

            if (res.ok) {
                saveStatus.textContent = 'Salvato ✓'
                setTimeout(() => { saveStatus.textContent = '' }, 2000)
            } else {
                saveStatus.textContent = 'Errore nel salvataggio'
            }
        } catch (e) {
            saveStatus.textContent = 'Errore di rete'
        } finally {
            saveBtn.disabled = false
        }
    }

    saveBtn.addEventListener('click', save)

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault()
            save()
        }
    })
})

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-markdown]').forEach((el) => {
        el.innerHTML = marked.parse(el.dataset.markdown)
    })
})

window.toggleEdit = (id) => {
    const el = document.getElementById(id)
    if (el) el.classList.toggle('hidden')
}

window.mdTabSwitch = (editorId, tab) => {
    const editor = document.getElementById(editorId)
    if (!editor) return

    const writePanel = editor.querySelector('.md-panel-write')
    const previewPanel = editor.querySelector('.md-panel-preview')
    const writeTab = editor.querySelector('.md-tab-write')
    const previewTab = editor.querySelector('.md-tab-preview')

    if (tab === 'preview') {
        const textarea = writePanel.querySelector('textarea')
        previewPanel.querySelector('.md-rendered').innerHTML = marked.parse(textarea.value || '')
        writePanel.classList.add('hidden')
        previewPanel.classList.remove('hidden')
        writeTab.classList.remove('border-white/40', 'text-white/60')
        writeTab.classList.add('border-transparent', 'text-white/25')
        previewTab.classList.remove('border-transparent', 'text-white/25')
        previewTab.classList.add('border-white/40', 'text-white/60')
    } else {
        writePanel.classList.remove('hidden')
        previewPanel.classList.add('hidden')
        writeTab.classList.remove('border-transparent', 'text-white/25')
        writeTab.classList.add('border-white/40', 'text-white/60')
        previewTab.classList.remove('border-white/40', 'text-white/60')
        previewTab.classList.add('border-transparent', 'text-white/25')
    }
}
