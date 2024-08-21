// Urdu Phonetic Mapping
const urduPhoneticMap = {
    'a': 'ا',
    'b': 'ب',
    'c': 'چ',
    'd': 'د',
    'e': 'ع',
    'f': 'ف',
    'g': 'گ',
    'h': 'ح',
    'i': 'ی',
    'j': 'ج',
    'k': 'ک',
    'l': 'ل',
    'm': 'م',
    'n': 'ن',
    'o': 'و',
    'p': 'پ',
    'q': 'ق',
    'r': 'ر',
    's': 'س',
    't': 'ت',
    'u': 'ء',
    'v': 'ٹ',
    'w': 'و',
    'x': 'ش',
    'y': 'ے',
    'z': 'ز',
    'A': 'آ',
    'B': 'ب',
    'C': 'چ',
    'D': 'ڈ',
    'E': 'ع',
    'F': 'ف',
    'G': 'گ',
    'H': 'ھ',
    'I': 'ی',
    'J': 'ژ',
    'K': 'خ',
    'L': 'ل',
    'M': 'م',
    'N': 'ں',
    'O': 'ؤ',
    'P': 'پ',
    'Q': 'ق',
    'R': 'ڑ',
    'S': 'ص',
    'T': 'ٹ',
    'U': 'ئ',
    'V': 'ض',
    'W': 'و',
    'X': 'ث',
    'Y': 'ي',
    'Z': 'ذ',
    ' ': ' ', // Space
    ',': '،',
    '.': '۔',
    ';': '؛',
    '?': '؟',
    '1': '۱', // Number 1
    '2': '۲', // Number 2
    '3': '۳', // Number 3
    '4': '۴', // Number 4
    '5': '۵', // Number 5
    '6': '۶', // Number 6
    '7': '۷', // Number 7
    '8': '۸', // Number 8
    '9': '۹', // Number 9
    '0': '۰', // Number 0
};

// Urdu Phonetic Keyboard Function
function UrduPhoneticKeyboard(element, isTitle) {
    let typingMode = 'urdu'; // Default to Urdu typing mode
    const shortcutKey = wp_urdu_settings.wp_urdu_shortcut || 't'; // Use localized shortcut key
    const blockStyleShortcutKey = wp_urdu_settings.wp_urdu_block_style_shortcut || 'b'; // Use localized block style shortcut
    const applyToTitles = wp_urdu_settings.wp_urdu_title_option === 'yes'; // Use localized setting

    // Check if the element should support Urdu typing
    const shouldApplyUrdu = !isTitle || applyToTitles;

    document.addEventListener('keydown', function (e) {
        if (e.altKey && e.key === shortcutKey) { // Toggle with user-defined shortcut
            typingMode = (typingMode === 'urdu') ? 'english' : 'urdu';
            e.preventDefault();
            console.log(`Typing mode switched to: ${typingMode}`);
        }

        if (e.altKey && e.key === blockStyleShortcutKey) { // Apply Urdu style to selected blocks
            e.preventDefault();
            const selectedBlocks = wp.data.select('core/block-editor').getSelectedBlock();
            if (selectedBlocks) {
                wp.data.dispatch('core/block-editor').updateBlockAttributes(selectedBlocks.clientId, {
                    className: selectedBlocks.attributes.className ? selectedBlocks.attributes.className + ' is-style-wp-urdu' : 'is-style-wp-urdu'
                });
            }
        }
    });

    if (shouldApplyUrdu) {
        element.addEventListener('beforeinput', function (e) {
            if (typingMode === 'urdu') {
                if (urduPhoneticMap[e.data]) {
                    e.preventDefault();
                    insertAtCursor(element, urduPhoneticMap[e.data]);
                }
            }
        });
    }

    function insertAtCursor(element, text) {
        const selection = window.getSelection();
        const range = selection.getRangeAt(0);

        const textNode = document.createTextNode(text);
        range.deleteContents();
        range.insertNode(textNode);

        range.setStartAfter(textNode);
        range.setEndAfter(textNode);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

// Run Urdu Phonetic Keyboard setup on WP DOM ready
wp.domReady(() => {
    const applyRTLAndKeyboard = () => {
        const blocks = document.querySelectorAll('.is-style-wp-urdu');
        blocks.forEach(block => {
            block.style.direction = 'rtl';
            block.style.textAlign = 'right';

            if (!block.hasAttribute('data-urdu-keyboard')) {
                block.setAttribute('data-urdu-keyboard', 'true');
                UrduPhoneticKeyboard(block, false); // Not a title
            }
        });

        // Apply Urdu typing conditionally in the post title field
        const titleField = document.querySelector('.editor-post-title__input');
        if (titleField) {
            const urduMode = titleField.getAttribute('data-urdu-keyboard') === 'true';
            titleField.style.direction = urduMode ? 'rtl' : 'ltr';
            titleField.style.textAlign = urduMode ? 'right' : 'left';

            if (!titleField.hasAttribute('data-urdu-keyboard')) {
                titleField.setAttribute('data-urdu-keyboard', 'true');
                UrduPhoneticKeyboard(titleField, true); // It's a title
            }
        }
    };

    applyRTLAndKeyboard();

    const observer = new MutationObserver(applyRTLAndKeyboard);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
