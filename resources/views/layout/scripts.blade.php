<script>
    document.addEventListener('alpine:init', () => {
        document.addEventListener('blur', function (event) {
            if (event.target.tagName === 'INPUT' && event.target.type === 'text') {
                event.target.value = event.target.value.trim();
                event.target.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }, true);
    });

    const observer = new MutationObserver(function(mutationsList) {
        for (const mutation of mutationsList) {
            for (const node of mutation.addedNodes) {
                if (
                    node.nodeType === 1 &&
                    node.classList.contains('relative') &&
                    node.classList.contains('min-h-full')
                ) {
                    // Wait a tick to ensure inner content is rendered
                    requestAnimationFrame(() => {
                        const callModal = node.querySelector('.call-modal');

                        if (callModal) {
                            // Optional: Find scrollable content inside modal
                            const grandparent = callModal.parentElement?.parentElement;
                            if (grandparent) {
                                setTimeout(() => {
                                    grandparent.scrollTop = 0;
                                }, 100);

                            }
                        }
                    });
                }
            }
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
</script>
