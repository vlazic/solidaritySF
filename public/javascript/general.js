function bindSchool() {
    $('#registration_delegate_city').on('change', function () {
        const id = $(this).val();

        $.get('/schools', {'city-id': id}, function (data) {
            let options = '<option value="" selected="selected">Izaberite školu</option>';

            for (let i = 0; i < data.length; i++) {
                options += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
            }

            $('#registration_delegate_school').html(options);
        });
    });
}

function loadDriverInfo() {
    if(!$('.js-info-button').is(':visible')){
        return false;
    }

    if(localStorage.getItem("info-button-already-shown")){
        return false;
    }

    const driver = window.driver.js.driver;
    const driverObj = driver();

    driverObj.highlight({
        element: ".js-info-button",
        popover: {
            title: "Uputstva za korišćenje",
            description: "Na svakoj stranici na kojoj je dostupna ova opcija, klikom na dugme pokrećete jednostavan vodič koji će vam pomoći da brže i lakše razumete šta se prikazuje na stranici i koje sve akcije možete preduzeti."
        }
    });

    localStorage.setItem("info-button-already-shown", true);
}

function loadDriver(steps){
    const driver = window.driver.js.driver;
    const driverObj = driver({
        showProgress: true,
        doneBtnText: 'Završi',
        closeBtnText: 'Zatvori',
        nextBtnText: 'Sledeće',
        prevBtnText: 'Prethodno',
        steps: steps,
    });

    $('.js-info-button').on('click', function () {
        driverObj.drive();
    });
}

function loadSelect2() {
    $('.js-select2').select2();
}

function initCopyTooltip() {
    // Check if page has copyable elements
    const copyableElements = document.querySelectorAll('[data-copyable]');
    if (copyableElements.length === 0) return;

    // Check if device supports hover
    const isTouch = window.matchMedia('(hover: none)').matches;

    // Constants
    const HOVER_DELAY = 1000;
    const RESET_DELAY = 1500;
    const TAP_TIMEOUT = 3000;

    // Labels
    const LABELS = {
        desktop: {
            default: 'Kliknite da kopirate',
            success: 'Kopirano!',
            error: 'Greška pri kopiranju'
        },
        mobile: {
            default: 'Kliknite ponovo da kopirate',
            initial: 'Kliknite ponovo da kopirate',
            success: 'Kopirano!',
            error: 'Greška pri kopiranju'
        }
    };

    // Helper function for copying text
    async function copyText(text, labels, isMobile = false) {
        try {
            // Try using Clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
            } else {
                // Fallback for non-secure context
                const textArea = document.createElement('textarea');
                textArea.value = text;

                const isIOS = /ipad|iphone/i.test(navigator.userAgent);

                // Make editable only on iOS where we need it
                textArea.contentEditable = isIOS;
                textArea.readOnly = !isIOS;

                // Add styling
                textArea.className = 'copy-textarea';
                textArea.setAttribute('aria-hidden', 'true');

                document.body.appendChild(textArea);

                // Handle selection based on device
                if (isIOS) {
                    // iOS needs focus and selection UI
                    textArea.focus();
                    textArea.scrollIntoView(false);

                    // Force iOS to show selection UI
                    textArea.addEventListener('click', function() {
                        textArea.setSelectionRange(0, textArea.value.length);
                    });
                    textArea.click();

                    const range = document.createRange();
                    range.selectNodeContents(textArea);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                    textArea.setSelectionRange(0, textArea.value.length);
                } else {
                    // Other devices just need selection without focus
                    textArea.select();
                }

                // Small delay to ensure selection is complete
                await new Promise(resolve => setTimeout(resolve, 100));

                try {
                    document.execCommand('copy');
                } finally {
                    textArea.remove();
                }
            }

            // Show success message
            tooltip.textContent = labels.success;
            if (isMobile) {
                showTooltip(null, true);
                setTimeout(hideTooltip, RESET_DELAY);
            } else {
                setTimeout(() => {
                    tooltip.textContent = labels.default;
                }, RESET_DELAY);
            }
        } catch (err) {
            console.error('Failed to copy text:', err);
            tooltip.textContent = labels.error;
            if (isMobile) {
                showTooltip(null, true);
                setTimeout(hideTooltip, RESET_DELAY);
            } else {
                setTimeout(() => {
                    tooltip.textContent = labels.default;
                }, RESET_DELAY);
            }
        }
    }

    // Create and append tooltip element
    const tooltip = document.createElement('div');
    tooltip.className = 'copy-tooltip';
    tooltip.textContent = isTouch ? LABELS.mobile.initial : LABELS.desktop.default;
    document.body.appendChild(tooltip);

    // Create backdrop for mobile
    let backdrop;
    if (isTouch) {
        backdrop = document.createElement('div');
        backdrop.className = 'copy-tooltip-backdrop';
        document.body.appendChild(backdrop);
    }

    // Add copy functionality to elements
    let hoverTimeout;
    let tapTimeout;
    let selectedElement = null;

    function showTooltip(element, immediate = false) {
        if (isTouch) {
            backdrop.classList.add('show');
            tooltip.classList.add('show');
        } else {
            const rect = element.getBoundingClientRect();
            tooltip.style.top = `${rect.top - 30}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
            tooltip.classList.add('show');
        }
    }

    function hideTooltip() {
        tooltip.classList.remove('show');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
        selectedElement = null;
    }

    copyableElements.forEach(element => {
        if (!isTouch) {
            // Desktop behavior
            element.addEventListener('mouseover', () => {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    showTooltip(element);
                }, HOVER_DELAY);
            });

            element.addEventListener('mouseout', () => {
                clearTimeout(hoverTimeout);
                hideTooltip();
            });

            element.addEventListener('click', async () => {
                const textToCopy = element.textContent.trim();
                copyText(textToCopy, LABELS.desktop);
            });
        } else {
            // Mobile behavior
            element.addEventListener('click', async () => {
                clearTimeout(tapTimeout);

                if (selectedElement === element) {
                    // Second tap - copy
                    const textToCopy = element.textContent.trim();
                    copyText(textToCopy, LABELS.mobile, true);
                    selectedElement = null;
                } else {
                    // First tap - show tooltip
                    if (selectedElement) {
                        hideTooltip();
                    }
                    selectedElement = element;
                    tooltip.textContent = LABELS.mobile.default;
                    showTooltip(element, true);
                    tapTimeout = setTimeout(hideTooltip, TAP_TIMEOUT);
                }
            });
        }
    });

    // Hide tooltip when clicking outside
    if (isTouch) {
        document.addEventListener('click', (e) => {
            if (!e.target.hasAttribute('data-copyable')) {
                hideTooltip();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initCopyTooltip();
});
