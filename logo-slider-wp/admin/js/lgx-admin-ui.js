/**
 * LogicHunt Admin UI Kit Primitives
 * Handles WAI-ARIA accessible interactions for reusable UI components.
 */

function initLgxAdminUi() {

    /* ==========================================================================
       WAI-ARIA Tablist
       ========================================================================== */
    const tablists = document.querySelectorAll('.lgx-ui-tablist');

    tablists.forEach(tablist => {
        const tabs = Array.from(tablist.querySelectorAll('.lgx-ui-tab'));

        // Task 013 (1a): initialize roving tabindex on load. Without this,
        // every tab button keeps its native default focusability (all are
        // independently Tab-stoppable) until the first click/keydown runs
        // activateTab() below — this establishes the single-tab-stop state
        // immediately, matching the WAI-ARIA APG tabs pattern from page load.
        const initialIndex = Math.max(0, tabs.findIndex(tab => tab.getAttribute('aria-selected') === 'true'));
        if (tabs.length) {
            activateTab(tablist, tabs, initialIndex);
        }

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                activateTab(tablist, tabs, index);
            });

            tab.addEventListener('keydown', (e) => {
                let newIndex = index;
                const isVertical = tablist.getAttribute('aria-orientation') === 'vertical';

                switch (e.key) {
                    case 'ArrowLeft':
                        if (!isVertical) newIndex = index - 1;
                        break;
                    case 'ArrowRight':
                        if (!isVertical) newIndex = index + 1;
                        break;
                    case 'ArrowUp':
                        if (isVertical) newIndex = index - 1;
                        break;
                    case 'ArrowDown':
                        if (isVertical) newIndex = index + 1;
                        break;
                    case 'Home':
                        newIndex = 0;
                        break;
                    case 'End':
                        newIndex = tabs.length - 1;
                        break;
                    default:
                        return; // Exit if it's not a navigation key
                }

                // Handle looping
                if (newIndex < 0) {
                    newIndex = tabs.length - 1;
                } else if (newIndex >= tabs.length) {
                    newIndex = 0;
                }

                if (newIndex !== index) {
                    e.preventDefault();
                    tabs[newIndex].focus();
                    activateTab(tablist, tabs, newIndex);
                }
            });
        });
    });

    function activateTab(tablist, tabs, activeIndex) {
        tabs.forEach((tab, index) => {
            const isSelected = index === activeIndex;
            tab.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            tab.setAttribute('tabindex', isSelected ? '0' : '-1');

            // Handle corresponding TabPanel visibility
            const controlsId = tab.getAttribute('aria-controls');
            if (controlsId) {
                const panel = document.getElementById(controlsId);
                if (panel) {
                    if (isSelected) {
                        panel.classList.add('is-active');
                    } else {
                        panel.classList.remove('is-active');
                    }
                }
            }
        });
    }


    /* ==========================================================================
       PanelBody Accordion
       ========================================================================== */
    const panelHeaders = document.querySelectorAll('.lgx-ui-panelbody-header');

    panelHeaders.forEach(header => {
        header.addEventListener('click', (e) => {
            e.preventDefault();
            const isExpanded = header.getAttribute('aria-expanded') === 'true';
            
            // Toggle state
            header.setAttribute('aria-expanded', !isExpanded);
        });

        // Ensure keyboard accessibility (Enter/Space on button elements is natively handled,
        // but if they are generic divs with role="button", we need this)
        header.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                header.click();
            }
        });
    });

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLgxAdminUi);
} else {
    initLgxAdminUi();
}
