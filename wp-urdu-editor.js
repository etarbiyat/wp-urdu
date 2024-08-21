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

// Urdu Phonetic Keyboard Function for contentEditable elements
function UrduPhoneticKeyboard(element) {
    let typingMode = 'urdu'; // Default to Urdu typing mode

    document.addEventListener('keydown', function (e) {
        if (e.altKey && e.key === 't') { // Toggle with Alt+T
            typingMode = (typingMode === 'urdu') ? 'english' : 'urdu';
            e.preventDefault();
            console.log(`Typing mode switched to: ${typingMode}`);
        }
    });

    element.addEventListener('beforeinput', function (e) {
        if (typingMode === 'urdu') {
            // Check if the input is Urdu based on mapping
            if (urduPhoneticMap[e.data]) {
                e.preventDefault();
                insertAtCursor(element, urduPhoneticMap[e.data]);
            }
        } else if (typingMode === 'english') {
            // Allow English text input
        }
    });

    function insertAtCursor(element, text) {
        const selection = window.getSelection();
        const range = selection.getRangeAt(0);

        // Create a new text node and insert it
        const textNode = document.createTextNode(text);
        range.deleteContents();
        range.insertNode(textNode);

        // Move the cursor to the end of the inserted text
        range.setStartAfter(textNode);
        range.setEndAfter(textNode);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}

// Run the Urdu Phonetic Keyboard setup on WP DOM ready
wp.domReady(() => {
    wp.blocks.registerBlockStyle('core/paragraph', {
        name: 'wp-urdu',
        label: 'WP Urdu',
    });

    const applyRTLAndKeyboard = () => {
        const blocks = document.querySelectorAll('.is-style-wp-urdu');
        blocks.forEach(block => {
            block.style.direction = 'rtl';
            block.style.textAlign = 'right';

            if (!block.hasAttribute('data-urdu-keyboard')) {
                block.setAttribute('data-urdu-keyboard', 'true');
                UrduPhoneticKeyboard(block);
            }
        });
    };

    applyRTLAndKeyboard();

    const observer = new MutationObserver(applyRTLAndKeyboard);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
