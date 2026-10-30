/**
 * Showcase Builder Specific Logic
 *
 * Handles URL hash synchronization and deep-linking for the Builder Navigation.
 * Integrates with lgx-admin-ui.js primitives.
 *
 * @since 6.0.0
 */

/**
 * Task 021: shared alertify wrappers for the Builder's own success/error
 * notifications. alertify.min.js is already enqueued on this screen as a
 * dependency of logo-slider-wp-admin.js (both load for the lgx_lsp_shortcodes
 * post type), so no separate enqueue is needed here — same defensive
 * typeof-guard pattern already used in logo-slider-wp-admin.js.
 */
function lgxNotifySuccess(message) {
    if (typeof alertify !== 'undefined') {
        alertify.success(message);
    }
}
function lgxNotifyError(message) {
    if (typeof alertify !== 'undefined') {
        alertify.error(message);
    }
}

function initShowcaseBuilder() {

    const tablist = document.querySelector('.lgx-ui-tablist');
    if (!tablist) return;

    const tabs = Array.from(tablist.querySelectorAll('.lgx-ui-tab'));

    /**
     * Activate a tab by its ID string (e.g., 'layout').
     */
    function activateTabById(tabId, shouldFocus) {
        const targetTab = tabs.find(tab => tab.id === 'lgx-tab-' + tabId);
        if (targetTab) {
            // Programmatically click the tab. 
            // lgx-admin-ui.js will handle the visual state and panel visibility.
            targetTab.click();
            // Move focus to the tab for accessibility ONLY if explicitly requested, otherwise blur
            if (shouldFocus) {
                targetTab.focus();
            } else {
                targetTab.blur();
            }
        }
    }

    /**
     * Parse the URL hash on load and activate the corresponding tab.
     */
    function handleInitialHash() {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#lgx-tab-')) {
            const tabId = hash.replace('#lgx-tab-', '');
            activateTabById(tabId, false);
            // Clear focus ring if the browser natively focused the button due to the URL hash
            setTimeout(function () {
                if (document.activeElement && document.activeElement.classList.contains('lgx-ui-tab')) {
                    document.activeElement.blur();
                }
            }, 50);
        }
    }

    /**
     * Synchronize URL hash when a user clicks a tab.
     */
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.id.replace('lgx-tab-', '');
            // Update hash without jumping (using history API)
            if (history.replaceState) {
                history.replaceState(null, null, '#lgx-tab-' + tabId);
            } else {
                window.location.hash = '#lgx-tab-' + tabId;
            }
        });
    });

    // Run on load and hash change (for deep links)
    handleInitialHash();
    window.addEventListener('hashchange', handleInitialHash);
}

/**
 * 007R-002: Layout Gallery Controller
 *
 * Manages the "Change Layout" trigger(s), Layout Gallery modal open/close,
 * and updating the hidden input + Current Layout display(s) on selection.
 *
 * Task 008 Phase 2: there can now be TWO live trigger instances on the page at
 * once — the General-tab card and the compact context row on the Layout tab —
 * both opening the same modal. Elements are looked up via querySelectorAll on
 * the shared ".lgx-layout-trigger" class, not getElementById, since ids must be
 * unique per page and two instances of the same component now coexist.
 */
function initLayoutGallery() {

    // 007R-c/Task 008 Phase 2: every ".lgx-layout-trigger" opens the same
    // Layout Gallery modal — the General-tab card and, as of Phase 2, the
    // Layout-tab's compact context row.
    const triggers    = document.querySelectorAll('.lgx-layout-trigger');
    const gallery     = document.getElementById('lgx-layout-gallery');
    const overlay     = document.getElementById('lgx-layout-gallery-overlay');
    const closeBtn    = document.getElementById('lgx-layout-gallery-close');
    const hiddenInput = document.getElementById('lgx_lswp_showcase_type_field');

    // Bail if the gallery elements are not present on this screen.
    if (!triggers.length || !gallery || !overlay || !closeBtn) {
        return;
    }

    // Move to body to avoid overflow hidden/auto issues from parent containers
    if (gallery.parentNode !== document.body) {
        document.body.appendChild(gallery);
    }
    if (overlay.parentNode !== document.body) {
        document.body.appendChild(overlay);
    }

    // Tracks which trigger instance opened the modal, so focus returns to the
    // correct one (General card vs Layout-tab row) on close.
    let lastTrigger = null;

    /**
     * Open the Layout Gallery.
     * @param {Element} trigger The specific trigger element that was activated.
     */
    function openGallery(trigger) {
        lastTrigger = trigger;
        gallery.classList.add('lgx-gallery-open');
        overlay.classList.add('lgx-gallery-open');
        trigger.setAttribute('aria-expanded', 'true');
        closeBtn.focus();
    }

    /**
     * Close the Layout Gallery and return focus to whichever trigger opened it.
     */
    function closeGallery() {
        gallery.classList.remove('lgx-gallery-open');
        overlay.classList.remove('lgx-gallery-open');
        triggers.forEach(t => t.setAttribute('aria-expanded', 'false'));
        if (lastTrigger) {
            lastTrigger.focus();
        }
    }

    /**
     * Apply the selected layout:
     * - Update the hidden input value (for form saving).
     * - Update every trigger instance's label and icon (General card + Layout row).
     * - Update aria-pressed on all cards.
     * - Close the gallery.
     */
    function selectLayout(card) {
        const value = card.dataset.layoutValue;
        const label = card.dataset.layoutLabel;
        const icon  = card.dataset.layoutIcon;

        if (!value) return;

        // Update hidden input
        if (hiddenInput) {
            hiddenInput.value = value;
        }

        // Update every trigger instance in sync (General card, Layout-tab row, Header pill).
        triggers.forEach(trigger => {
            const labelEl = trigger.querySelector('.lgx-current-layout-card__label, .lgx-layout-trigger-label');
            if (labelEl) {
                labelEl.textContent = label || value;
            }
            if (icon) {
                const iconEl = trigger.querySelector('.lgx-current-layout-icon, .lgx-layout-trigger-icon');
                if (iconEl) {
                    // Remove all dashicons-* classes, preserve the base classes.
                    iconEl.className = iconEl.className.replace(/dashicons-\S+/g, '').trim();
                    iconEl.classList.add('dashicons', icon);
                }
            }
        });
        // (007R-c: the layout description no longer displays on any trigger —
        // it's still shown inside the gallery cards themselves via
        // data-layout-desc, unchanged.)

        // Update is-selected state and aria-pressed on gallery cards.
        const allCards = gallery.querySelectorAll('.lgx-layout-gallery-card');
        allCards.forEach(c => {
            const isThis = c.dataset.layoutValue === value;
            c.classList.toggle('is-selected', isThis);
            c.setAttribute('aria-pressed', isThis ? 'true' : 'false');
        });

        closeGallery();

        // Dispatch custom event for header status update (007R-003) and Layout
        // tab conditional visibility (007R-007) — both already listen for this
        // and need no changes for Task 008.
        document.dispatchEvent(new CustomEvent('lgx:status-update'));

        // Task 021: a real, meaningful state change that was previously silent.
        lgxNotifySuccess(lgxText('layoutUpdated', 'Layout updated'));
    }

    // — Event Listeners —

    // Handle Content Source radio button active states
    const sourceRadios = document.querySelectorAll('input[name="meta_lgx_lsp_shortcodes[lgx_lswp_content_source]"]');
    sourceRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const allLabels = document.querySelectorAll('.lgx-source-option');
            allLabels.forEach(label => label.classList.remove('is-active'));
            if (this.checked && this.closest('.lgx-source-option')) {
                this.closest('.lgx-source-option').classList.add('is-active');
            }
        });
    });

    // Header Content Source pill button jump to Content Settings tab.
    // 008 §4: jump-to-tab only while Source has a single option; becomes a
    // true picker when multi-source support ships.
    const headerSourceBtn = document.getElementById('lgx-header-source-btn');
    if (headerSourceBtn) {
        headerSourceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const sourceTab = document.getElementById('lgx-tab-source_settings');
            if (sourceTab) {
                sourceTab.click();
                sourceTab.focus();
            }
        });
    }

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
            openGallery(trigger);
        });
    });
    closeBtn.addEventListener('click', closeGallery);

    // Close on overlay backdrop click.
    overlay.addEventListener('click', closeGallery);

    // Close on Escape key.
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && gallery.classList.contains('lgx-gallery-open')) {
            closeGallery();
        }
    });

    // Select a layout when a gallery card is clicked.
    gallery.addEventListener('click', function (e) {
        const card = e.target.closest('.lgx-layout-gallery-card');
        if (card) {
            selectLayout(card);
        }
    });
}

/**
 * T-226b (B189): a translated text from lgxShowcaseBuilderDefaults (builder-wrapper.php),
 * or the English fallback when the key is missing or empty.
 *
 * @param {string} key      The key in lgxShowcaseBuilderDefaults.
 * @param {string} fallback The English text.
 * @return {string}
 */
function lgxText( key, fallback ) {
    var data = window.lgxShowcaseBuilderDefaults;
    return ( data && typeof data === 'object' && typeof data[ key ] === 'string' && data[ key ] !== '' ) ? data[ key ] : fallback;
}

/**
 * WP1.5: describes any stored content source value for display.
 *
 * JS twin of lgx_ls_describe_source() in includes/lgx-ls-sources.php. The
 * labels and icons are not kept here: they come from the PHP registry through
 * lgxShowcaseBuilderDefaults.sources (and the translated format string through
 * lgxShowcaseBuilderDefaults.unknownSourceFormat). A blank value reads as
 * logo_library; an unknown value is shown as "Unknown source: <value>" (cut at
 * 40 characters) and is never shown as the Logo Library.
 *
 * @param {*} value A stored source value.
 * @return {{id: string, label: string, icon: string, available: boolean, known: boolean}}
 */
function lgxDescribeSource( value ) {
    var data = window.lgxShowcaseBuilderDefaults;
    var sources = ( data && typeof data === 'object' && data.sources && typeof data.sources === 'object' ) ? data.sources : {};
    var format = ( data && typeof data === 'object' && typeof data.unknownSourceFormat === 'string' && data.unknownSourceFormat !== '' ) ? data.unknownSourceFormat : 'Unknown source: %s';
    var warning = 'dashicons-warning';

    function unknownLabel( shown ) {
        // First placeholder only: "%s" or the numbered form "%1$s" that a translation may use.
        return format.replace( /%(?:1\$)?s/, function () { return shown; } );
    }

    // PHP trim() removes exactly: space, tab, newline, carriage return, NUL, vertical tab.
    if ( value === undefined || value === null || ( typeof value === 'string' && /^[ \t\n\r\0\x0B]*$/.test( value ) ) ) {
        value = 'logo_library';
    }

    if ( typeof value !== 'string' ) {
        return { id: '', label: unknownLabel( lgxText( 'notText', '(not text)' ) ), icon: warning, available: false, known: false };
    }

    if ( Object.prototype.hasOwnProperty.call( sources, value ) ) {
        var entry = sources[ value ];
        if ( entry && typeof entry === 'object' ) {
            return {
                id: value,
                label: String( entry.label ),
                icon: String( entry.icon ),
                available: ( entry.available === true ),
                known: true
            };
        }
    }

    // Cut by code points (not UTF-16 units) so a surrogate pair is never split.
    var chars = Array.from( value );
    var shown = value;
    if ( chars.length > 40 ) {
        shown = chars.slice( 0, 40 ).join( '' ) + String.fromCharCode( 0x2026 );
    }
    return { id: value, label: unknownLabel( shown ), icon: warning, available: false, known: false };
}

/**
 * The stored content source value exactly as builder-wrapper.php sent it (escaped in the attribute, never cleaned).
 * No attribute = no stored value (null, read as Logo Library); the nontext flag = a stored value that is not text.
 *
 * @param {Element|null} builderEl The .lgx-builder element.
 * @return {*} A value for lgxDescribeSource().
 */
function lgxStoredSource( builderEl ) {
    if ( ! builderEl ) {
        return 'logo_library';
    }
    if ( builderEl.hasAttribute( 'data-current-source-nontext' ) ) {
        return [];
    }
    return builderEl.hasAttribute( 'data-current-source' ) ? builderEl.getAttribute( 'data-current-source' ) : null;
}

/**
 * 007R-003: Builder Header Status
 *
 * Dynamically updates the "Current Setup" status line in the builder header
 * whenever the Layout or Content Source is changed.
 */
function initBuilderStatus() {

    const statusEl = document.getElementById('lgx-builder-status-summary');
    if (!statusEl) return;

    const layoutInput = document.getElementById('lgx_lswp_showcase_type_field');
    const sourceRadios = document.querySelectorAll('input[name="meta_lgx_lsp_shortcodes[lgx_lswp_content_source]"]');

    // Lookup maps matching the PHP-side definitions
    var layoutMap = {
        'carousel': { icon: 'dashicons-layout', label: 'Carousel' },
        'grid':     { icon: 'dashicons-grid-view', label: 'Grid' },
        'flexbox':  { icon: 'dashicons-columns', label: 'Flexbox' }
    };

    // The source labels and icons are not kept here: lgxDescribeSource() reads
    // them from the PHP registry (lgxShowcaseBuilderDefaults.sources).

    function update() {
        var layoutValue = layoutInput ? layoutInput.value : 'carousel';
        // T-213 (S-05, A1): the pill follows the one posted source input
        // (tab-source_settings.php). While it is disabled (stored value
        // missing, blank or not text, and no pick yet) the pill keeps the
        // true stored value (builder-wrapper.php's data-current-source), so
        // it never relabels a showcase to the wrong source after page load.
        var sourceField = document.getElementById('lgx_lswp_content_source_field');
        var builderEl = document.querySelector('.lgx-builder');
        var trueCurrentSource = lgxStoredSource( builderEl );
        var sourceValue = ( sourceField && ! sourceField.disabled ) ? sourceField.value : trueCurrentSource;

        var layout = layoutMap[layoutValue] || layoutMap['carousel'];
        var source = lgxDescribeSource(sourceValue);

        var layoutBtn = statusEl.querySelector('.lgx-layout-trigger');
        var sourceBtn = statusEl.querySelector('.lgx-source-trigger');

        if (layoutBtn) {
            var iconEl = layoutBtn.querySelector('.lgx-layout-trigger-icon');
            var labelEl = layoutBtn.querySelector('.lgx-layout-trigger-label');
            if (iconEl && layout.icon) {
                iconEl.className = 'lgx-layout-trigger-icon lgx-current-layout-icon dashicons ' + layout.icon;
            }
            if (labelEl) {
                labelEl.textContent = layout.label;
            }
        }

        if (sourceBtn) {
            var srcIconEl = sourceBtn.querySelector('.dashicons:not(.lgx-status-chevron)');
            var strongEl = sourceBtn.querySelector('strong');
            if (srcIconEl && source.icon) {
                srcIconEl.className = 'dashicons ' + source.icon;
            }
            if (strongEl) {
                strongEl.textContent = source.label;
            }
        }
    }

    // Initial update
    update();

    // Update on content source change
    sourceRadios.forEach(function (radio) {
        radio.addEventListener('change', update);
    });

    // Update on layout change via custom event
    document.addEventListener('lgx:status-update', update);
}

/**
 * 004 §5, retired to native-only in Task 014/Commit 4b: showcase naming.
 *
 * Through Commit 4a there was a second, decorative "Showcase Name" input
 * (#lgx-showcase-name-field, tab-general_settings.php) that mirrored every
 * change into the native #title field. Commit 4b unhid #titlediv (see
 * showcase-builder.css) and removed the General tab that hosted the mirror,
 * so this function now writes directly to #title — there is nothing left to
 * mirror into it. Per 002-title-sync-technical-spike-report.md, this needs
 * no additional event dispatch — WordPress's own unsaved-changes check,
 * autosave/Heartbeat, and save-on-publish all read #title's live value at
 * check-time, not a cached "did an input event fire" flag.
 *
 * Auto-generation rule — one signal, no separate "first time" flag (mirrors
 * the isUsingTitleDefault_* freeze pattern in logo-slider-wp-admin.js, used
 * for the logo item's Tooltip/Alt Text-inherit-from-title fields): if
 * #title is empty on load, auto-generate "[Layout] [Source] Showcase #<ID>"
 * (Task 020/Task 6 — the #<ID> suffix, see idSuffix() below) and
 * keep it live-updating on Layout change, until the user types into #title
 * directly — at that point auto-generation freezes permanently for this
 * showcase (client-side for the rest of this page load; once saved, the now
 * non-empty native title makes the freeze permanent on every future load
 * too, since blank-on-load is the only signal this function ever checks —
 * this is also why a Quick-Edit rename or any other out-of-builder title
 * change is never overwritten: the title is non-blank next time this loads,
 * regardless of how it got that way).
 *
 * Content Source has exactly one option today (Logo Library — WooCommerce
 * Brands' UI was removed in RB-2/B52) and no live control anywhere in the
 * Builder to change it, so the source half of the name is read once from
 * .lgx-builder's data-current-source (the same true-stored-value fallback
 * initBuilderStatus() already uses) rather than listened for changes.
 */

/**
 * 010 Part C — Template pill: explicit no-op guard.
 *
 * The Template pill in the persistent header (#lgx-header-template-btn)
 * shows "Default" and must do nothing when clicked — there is no template
 * system yet. A JS handler is registered to document the intention clearly
 * and prevent any future accidental fallback action. Per brief 010 Part C:
 * "When a real template system is eventually built, this control is
 * expanded, not rebuilt."
 *
 * Hiding it later (one toggle) needs no handler change — the brief confirms
 * it is a plain flex sibling with no reserved width (see builder-wrapper.php
 * comment at the pill markup).
 */
function initTemplatePillNoOp() {
    var btn = document.getElementById('lgx-header-template-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
        // Intentional no-op — presentational only until the template system
        // ships. type="button" already prevents form submission; we absorb
        // the click silently and do nothing else.

        // Task 021: deliberately NOT calling lgxNotifySuccess() here — this
        // click doesn't actually select anything (no template system exists
        // yet), so a "success" notification here would be a false signal.
        // Once a real template system replaces this no-op with genuine
        // selection logic, the notification call belongs right here:
        //   lgxNotifySuccess('Template updated');
    });
}

function initShowcaseNameSync() {

    const titleField = document.getElementById('title');
    if (!(titleField instanceof HTMLInputElement)) return;

    const titlewrap = document.getElementById('titlewrap');

    const layoutMap = {
        carousel: 'Carousel',
        grid: 'Grid',
        flexbox: 'Flexbox'
    };

    // Blank title on load is the sole signal — no separate flag needed.
    let isAutoGenerating = titleField.value.trim() === '';

    function currentLayoutLabel() {
        const layoutInput = document.getElementById('lgx_lswp_showcase_type_field');
        const value = layoutInput ? layoutInput.value : 'carousel';
        return layoutMap[value] || layoutMap.carousel;
    }

    function currentSourceLabel() {
        const builderEl = document.querySelector('.lgx-builder');
        const value = lgxStoredSource(builderEl);
        return lgxDescribeSource(value).label;
    }

    // Task 020/Task 6: appends the real post ID so multiple showcases stay
    // distinguishable in the list table. Reuses WP core's own #post_ID
    // hidden field — already real and present the moment this screen loads
    // (WP assigns an auto-draft's ID synchronously before the form ever
    // renders, confirmed live: a brand-new, never-saved showcase already has
    // one) — no new localized value was added for this, per the closing
    // task list's instruction to check for an existing source first.
    // Falls back to no suffix if that field is ever missing or reads "0"
    // rather than showing a misleading id.
    function idSuffix() {
        const idField = document.getElementById('post_ID');
        const id = idField ? idField.value : '';
        return ( id && id !== '0' ) ? ' #' + id : '';
    }

    function generateName() {
        return currentLayoutLabel() + ' ' + currentSourceLabel() + ' Showcase' + idSuffix();
    }

    /*
     * Refactor: the title field shrinks to fit its own text at rest, so the
     * pencil/info icons (initTitleFieldIcons()) sit right after the visible
     * text instead of at the far edge of a full-width input, and expands
     * to fill the row on focus — the field's original full-width editing
     * behavior, now conditional on focus instead of constant. A hidden
     * mirror span measures the rendered pixel width of the current value
     * using the field's own computed font, since a plain <input> has no
     * CSS-only way to size itself to its value.
     */
    let widthMirror = null;
    function measureTextWidth(text) {
        if (!widthMirror) {
            widthMirror = document.createElement('span');
            const style = widthMirror.style;
            style.position = 'absolute';
            style.visibility = 'hidden';
            style.whiteSpace = 'pre';
            style.left = '-9999px';
            style.top = '-9999px';
            const computed = window.getComputedStyle(titleField);
            style.fontFamily = computed.fontFamily;
            style.fontSize = computed.fontSize;
            style.fontWeight = computed.fontWeight;
            style.fontStyle = computed.fontStyle;
            style.letterSpacing = computed.letterSpacing;
            document.body.appendChild(widthMirror);
        }
        widthMirror.textContent = text;
        return widthMirror.getBoundingClientRect().width;
    }

    function applyRestingWidth() {
        if (!titlewrap || document.activeElement === titleField) return;
        const text = titleField.value || '';
        // Room for #title's own padding/border (see showcase-builder.css,
        // 6px 8px + 1px border each side) plus a little caret comfort.
        const width = Math.max(measureTextWidth(text) + 26, 60);
        titleField.style.width = width + 'px';
    }

    /*
     * On load, something outside this file can leave #title genuinely
     * focused for a moment — most likely the browser's own "restore focus
     * on reload" behavior, not any explicit .focus() call on this screen
     * (checked: nothing in this codebase autofocuses #title here, only a
     * different, unrelated post type's redirect-focus in
     * logo-slider-wp-admin.js). Real DOM focus means the CSS :focus ring
     * genuinely paints, so a grace period after load rejects any focus
     * that isn't clearly a real user action, cancelling it before the
     * "editing" look ever renders. Once the grace period ends, focus works
     * normally for a real click/tab into the field.
     */
    let allowFocusEffects = false;
    window.setTimeout(function () {
        allowFocusEffects = true;
    }, 800);

    if (document.activeElement === titleField) {
        titleField.blur();
    }

    if (titlewrap) {
        titleField.addEventListener('focus', function () {
            if (!allowFocusEffects) {
                titleField.blur();
                return;
            }
            titlewrap.classList.add('lgx-title-editing');
            titleField.style.width = ''; // fall back to CSS's 100%, filling the now-expanded row
        });
        titleField.addEventListener('blur', function () {
            titlewrap.classList.remove('lgx-title-editing');
            applyRestingWidth();
        });
    }

    if (isAutoGenerating) {
        titleField.value = generateName();
    }
    applyRestingWidth();

    // Real user keystroke on the native title field freezes auto-generation
    // permanently for this showcase — same distinction the logo-item
    // precedent relies on (a programmatic .value= write, like the one above,
    // never fires 'input', so this only reacts to genuine typing).
    titleField.addEventListener('input', function () {
        isAutoGenerating = false;
    });

    // Layout change (any ".lgx-layout-trigger" instance — header pill,
    // Layout tab context row) → regenerate while still auto-generating.
    // No triggerSave() call here per §5.5 — WordPress's own Heartbeat timer
    // picks up the #title write on its normal schedule, matching what a
    // real user's pacing would produce anyway.
    document.addEventListener('lgx:status-update', function () {
        if (isAutoGenerating) {
            titleField.value = generateName();
            applyRestingWidth();
        }
    });
}

/**
 * Refactor: pencil + info icon pair for the native title field, inline
 * beside the input rather than in a separate label row above it (the prior
 * "Showcase Name" text label and its card container are both removed —
 * title now sits directly on the page background).
 *
 * Injected via JS, not a PHP hook: #titlediv is WordPress core's own
 * title-metabox markup (post_title_meta_box()/the_title_field()), which
 * exposes no filter/action that fires *inside* its own render — the
 * nearest hook (edit_form_after_title) only fires as a sibling after the
 * whole #titlediv box closes, which would render after the input rather
 * than inline beside it. This function runs before initFieldTooltips()
 * (see the two init blocks below) specifically so the info icon it inserts
 * is real DOM before initFieldTooltips()'s own
 * document.querySelectorAll('.lgx-builder-help-icon') runs — that call is
 * document-wide, not scoped to .lgx-builder, so the shared tooltip
 * mechanism picks this icon up with no changes of its own. The pencil icon
 * is decorative (aria-hidden) — the field is always editable, this mostly
 * signals that visually — but is also click-to-focus, a mouse-only
 * convenience alongside clicking the title text itself; the real input
 * stays independently focusable/tabbable for assistive tech either way.
 */
function initTitleFieldIcons() {
    const titlediv = document.getElementById('titlediv');
    const titlewrap = document.getElementById('titlewrap');
    const titleField = document.getElementById('title');
    if (!titlediv || !titlewrap) return;
    if (document.getElementById('lgx-title-label-row')) return; // already inserted

    const row = document.createElement('div');
    row.id = 'lgx-title-label-row';
    row.className = 'lgx-title-label-row';
    row.innerHTML =
        '<span class="dashicons dashicons-edit lgx-title-label-row__pencil" aria-hidden="true"></span>' +
        '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" ' +
        'aria-label="More information" aria-expanded="false" aria-controls="lgx-help-popover" ' +
        'data-help="Internal showcase name for admin reference only."></button>';

    // T-226b (B189): the translated texts are set with setAttribute, which needs no HTML escaping.
    const helpBtn = row.querySelector('.lgx-builder-help-icon');
    if (helpBtn) {
        helpBtn.setAttribute('aria-label', lgxText('moreInfo', 'More information'));
        helpBtn.setAttribute('data-help', lgxText('showcaseNameHelp', 'Internal showcase name for admin reference only.'));
    }

    titlewrap.insertAdjacentElement('afterend', row);

    if (titleField) {
        const pencil = row.querySelector('.lgx-title-label-row__pencil');
        if (pencil) {
            pencil.addEventListener('click', function () {
                titleField.focus();
            });
        }
    }
}

/**
 * 004 §2: "Copy Shortcode" header pill — reuses the persistent-header pill
 * pattern (Change Layout / Change Source, both already shipped) rather than
 * a new control style. Copies via a temporary offscreen textarea +
 * execCommand('copy'), the same technique embed-metabox.php's own copy
 * buttons already use — navigator.clipboard requires a secure context and
 * would silently fail on a plain-HTTP install (this dev site included).
 *
 * Task 021 (revised): the inline "Copied!" label-swap this task briefly
 * removed in favor of the alertify toast alone is restored — the Owner
 * wants both together, not one replacing the other. They're independent DOM
 * changes (button label text vs. a document.body-appended toast), so there
 * is no timing conflict between the 1.5s label reset and the toast's own
 * (longer) auto-dismiss.
 */
function initHeaderShortcodeCopy() {
    const btn = document.getElementById('lgx-header-copy-shortcode-btn');
    if (!btn) return;

    const labelEl = btn.querySelector('.lgx-builder-status__btn-label');
    const defaultLabel = labelEl ? labelEl.textContent : '';
    let resetTimer = null;

    btn.addEventListener('click', function () {
        const shortcode = btn.dataset.shortcode || '';
        if (!shortcode) return;

        const temp = document.createElement('textarea');
        temp.value = shortcode;
        temp.style.position = 'fixed';
        temp.style.top = '0';
        temp.style.left = '0';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);

        lgxNotifySuccess(lgxText('shortcodeCopied', 'Shortcode copied'));

        if (labelEl) {
            labelEl.textContent = lgxText('copiedLabel', 'Copied!');
            btn.classList.add('is-copied');
            if (resetTimer) clearTimeout(resetTimer);
            resetTimer = setTimeout(function () {
                labelEl.textContent = defaultLabel;
                btn.classList.remove('is-copied');
            }, 1500);
        }
    });
}

/**
 * 004 §3/008 §6: Sticky header Save/Update button, and its Save Draft
 * counterpart.
 *
 * Never reimplements saving — triggers the real native #publish button,
 * which is present with that exact id in every post-status state
 * (Publish/Update/Schedule/Submit for Review — confirmed by reading
 * wp-admin/includes/meta-boxes.php's post_submit_meta_box(), only the
 * button's label changes per status). This preserves nonces, autosave,
 * unsaved-change warnings, and any third-party save_post hooks unchanged,
 * because the real submission still goes through WordPress's own button.
 *
 * 008 §6: the `|| document.getElementById('save-post')` fallback this used
 * to have is removed — #publish always exists (confirmed above), so the
 * fallback never actually ran; it was dead code. Save Draft is now its own
 * explicit button/target pair below, not a fallback of this one.
 *
 * The native button's own value ("Publish"/"Update"/"Schedule"/"Submit for
 * Review") is mirrored onto this button on init so the wording is always
 * correct for the current post status without duplicating that logic here.
 */
function initStickySaveButton() {
    const headerBtn = document.getElementById('lgx-builder-header-save-btn');
    const nativeBtn = document.getElementById('publish');
    if (headerBtn && nativeBtn) {
        const labelEl = headerBtn.querySelector('.lgx-builder__header-save-btn-label');
        if (labelEl && nativeBtn.value) {
            // 012 §5: Substitute "Save" for "Publish" specifically, leaving Update etc unchanged.
            const publishText = (window.lgxShowcaseBuilderDefaults && lgxShowcaseBuilderDefaults.publishText) ? lgxShowcaseBuilderDefaults.publishText : 'Publish';
            if (nativeBtn.value !== publishText) {
                labelEl.textContent = nativeBtn.value;
            }
        }

        headerBtn.addEventListener('click', function () {
            nativeBtn.click();
        });
    }
}

/**
 * Task 021: "Showcase saved" notification after a real Save/Update.
 *
 * initStickySaveButton() above triggers WordPress's own native #publish
 * button — a real form submission/page reload, not an AJAX call, so there
 * is no JS success callback to hook. Instead, this detects WordPress's own
 * post-save signal: #message (id="message", classes "notice notice-success
 * ... updated") is rendered into the DOM by WP core whenever the redirect
 * carries its own ?message= query param, and is absent from the DOM
 * entirely otherwise — confirmed live, not assumed. It is normally visible
 * (a green admin notice) but is currently invisible in this custom-chrome
 * screen; its presence alone is what's used here as the "just saved" signal,
 * independent of whether it's shown.
 */
function initSaveSuccessNotice() {
    const message = document.getElementById('message');
    if (message && message.classList.contains('notice-success')) {
        lgxNotifySuccess(lgxText('showcaseSaved', 'Showcase saved'));
    }
}

/**
 * 007R: Layout Settings — show only the settings group(s) relevant to the
 * currently selected Showcase Type (Carousel / Grid / Flexbox).
 *
 * Groups are only visually hidden (CSS), never removed from the DOM, so their
 * field values are still submitted on save — no data loss (per ADR-005).
 */
function initLayoutSettingsVisibility() {

    const panel = document.getElementById('lgx-panel-layout_settings');
    const layoutInput = document.getElementById('lgx_lswp_showcase_type_field');
    if (!panel) return;

    // Maps each settings group's panel id to the Showcase Type it applies to.
    // T-226c: keyed by the panel id (lgx-group-{tab id}-{group key}, from
    // Lgx_Showcase_Renderer::render_group() and the config.php group keys),
    // not by the visible title, which is translated on non-English sites.
    var groupTypeMap = {
        // 007R-c: Carousel Settings / Navigation Settings were split into
        // smaller focused groups — all still map to 'carousel'.
        // B69: 'Timing' dissolved into 'Playback'; 'Navigation Content'
        // retitled to 'Navigation' — both updated here in the same commit
        // as the config.php reorder (see showcase-builder.css's sibling
        // group definitions for the current group set).
        'lgx-group-layout_settings-carousel_general':         'carousel', // Playback
        'lgx-group-layout_settings-carousel_interaction':     'carousel', // Touch & Mouse Interaction
        'lgx-group-layout_settings-carousel_slide_behaviour': 'carousel', // Slide Behaviour
        'lgx-group-layout_settings-carousel_nav_content':     'carousel', // Navigation
        'lgx-group-layout_settings-carousel_navigation':      'carousel', // Navigation Style
        'lgx-group-layout_settings-carousel_nav_border':      'carousel', // Navigation Border
        'lgx-group-layout_settings-carousel_pagination':      'carousel', // Pagination Settings
        'lgx-group-layout_settings-grid_settings':            'grid',     // Grid Settings
        'lgx-group-layout_settings-flexbox_settings':         'flexbox'   // Flexbox Settings
    };

    function update() {
        var currentType = layoutInput ? layoutInput.value : 'carousel';
        var panels = panel.querySelectorAll('.lgx-ui-panelbody');

        panels.forEach(function (panelbody) {
            var appliesTo = Object.prototype.hasOwnProperty.call(groupTypeMap, panelbody.id) ? groupTypeMap[panelbody.id] : '';

            // Groups not present in the map are left as-is (always visible).
            if (!appliesTo) return;

            panelbody.classList.toggle('lgx-panelbody--type-hidden', appliesTo !== currentType);
        });
    }

    // Reflect saved data on load (edit context), then keep in sync on layout change.
    update();
    document.addEventListener('lgx:status-update', update);
}

/**
 * 007R-008: Source Settings — show only the settings relevant to the currently
 * selected Content Source (Logo Library vs WooCommerce Brands).
 *
 * Both panels always exist in the DOM (only visually hidden), so the "Filter by
 * Category" field's value is still submitted on save even while hidden.
 */
function initSourceSettingsVisibility() {

    const panels = document.querySelectorAll('.lgx-source-dependent-panel');
    if (!panels.length) return;

    // T-213 (S-02 to S-06): the Content Source radio cards (name
    // lgx_source_picker, never read by the save) and the one posted hidden
    // source input. The current source is the checked card; the extra
    // "Unknown source" card has the value '' and matches no panel.
    const sourceRadioSelector = 'input[name="lgx_source_picker"]';
    const sourceField = document.getElementById('lgx_lswp_content_source_field');

    function update() {
        var checkedRadio = document.querySelector(sourceRadioSelector + ':checked');
        var currentSource = checkedRadio ? checkedRadio.value : 'logo_library';

        panels.forEach(function (panel) {
            var appliesTo = panel.dataset.sourcePanel;
            panel.classList.toggle('lgx-panelbody--type-hidden', appliesTo !== currentSource);
        });

        document.querySelectorAll(sourceRadioSelector).forEach(function (radio) {
            var card = radio.closest('.lgx-source-card');
            if (card) {
                card.classList.toggle('is-selected', radio.checked);
            }
        });
    }

    // Server-rendered classes already match the saved data; this only keeps
    // them in sync. An explicit pick is the only thing that changes the
    // posted value (A1/S-03): it sets the picked id and enables the input.
    update();
    document.querySelectorAll(sourceRadioSelector).forEach(function (radio) {
        radio.addEventListener('change', function () {
            if (!radio.checked || radio.disabled) return;
            if (sourceField) {
                sourceField.value = radio.value;
                sourceField.disabled = false;
            }
            update();
            // Left menu, header pill and live preview follow this event.
            document.dispatchEvent(new CustomEvent('lgx:status-update'));
        });
    });

    // S-05: the header pill keeps its jump to the Content tab and then
    // scrolls to the Content Source panel through its own left-menu entry.
    document.querySelectorAll('.lgx-source-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            setTimeout(function () {
                var navBtn = document.querySelector('.lgx-builder-nav__subgroup-btn[data-target-group="lgx-group-source_settings-content_source"]');
                if (navBtn) {
                    navBtn.click();
                }
            }, 0);
        });
    });
}

/**
 * T-215 (S-20 to S-24): the WooCommerce Brands picker (tab-source_settings.php).
 *
 * The markup, the texts and the missing-brand list are server-rendered; this
 * only filters, counts and writes the one hidden input. The hidden input keeps
 * the exact stored string until the user changes the selection (tick, untick,
 * Clear selection, Remove); only then is it written, as the save's own clean
 * form (positive whole numbers, no duplicates, ascending, comma-joined), and
 * enabled. A missing (not found) id stays in the value until its Remove
 * button is used. Not the category list's initCheckboxListSync(), which drops
 * unmatched ids. A locked picker (Free) gets no handlers at all: the existing
 * initProFieldLocking() intercepts its clicks.
 */
function initBrandPicker() {
    var row = document.getElementById('lgx-brand-picker-row');
    var picker = row ? row.querySelector('.lgx-brand-picker') : null;
    if (!picker) return;

    // S-23: "All brands" hides the row with CSS only; the hidden input stays in the form.
    var scopeSelect = document.getElementById('lgx_woo_product_brand_scope');
    if (scopeSelect) {
        scopeSelect.addEventListener('change', function () {
            row.classList.toggle('lgx-panelbody--type-hidden', scopeSelect.value === 'all');
        });
    }

    if (picker.getAttribute('data-locked') === '1') return;

    var hiddenInput = document.getElementById(picker.getAttribute('data-picker-for') || '');
    if (!hiddenInput) return;

    var inputs = Array.prototype.slice.call(picker.querySelectorAll('.lgx-brand-picker__input'));
    var counter = picker.querySelector('.lgx-brand-picker__count');
    var search = picker.querySelector('.lgx-brand-picker__search');
    var clearBtn = picker.querySelector('.lgx-brand-picker__clear');
    var missingBox = picker.querySelector('.lgx-brand-picker__missing');
    var missingTitle = picker.querySelector('.lgx-brand-picker__missing-title');

    function fill(format, values) {
        var i = 0;
        return String(format || '').replace(/%(\d)\$d|%d/g, function (match, pos) {
            var index = pos ? parseInt(pos, 10) - 1 : i++;
            return String(values[index]);
        });
    }

    // Ids are compared as digit strings (length first), never as Numbers, so a
    // large stored id keeps every digit.
    function compareIds(a, b) {
        if (a.length !== b.length) return a.length - b.length;
        return a < b ? -1 : (a > b ? 1 : 0);
    }

    function missingItems() {
        return Array.prototype.slice.call(picker.querySelectorAll('.lgx-brand-picker__missing-item'));
    }

    function updateCounts() {
        var ticked = inputs.filter(function (cb) { return cb.checked; }).length;
        if (counter) {
            counter.textContent = fill(picker.getAttribute('data-text-counter'), [ticked, inputs.length]);
        }
        var missing = missingItems().length;
        if (missingTitle) {
            var format = missing === 1 ? picker.getAttribute('data-text-missing-one') : picker.getAttribute('data-text-missing-many');
            missingTitle.textContent = fill(format, [missing]);
        }
        if (missingBox) {
            missingBox.hidden = (missing === 0);
        }
    }

    function writeValue() {
        var seen = {};
        var ids = [];
        function add(id) {
            id = String(id || '').replace(/^0+/, '');
            if (/^[0-9]+$/.test(id) && !seen[id]) {
                seen[id] = true;
                ids.push(id);
            }
        }
        inputs.forEach(function (cb) {
            if (cb.checked) add(cb.value);
        });
        missingItems().forEach(function (item) {
            add(item.getAttribute('data-brand-id'));
        });
        ids.sort(compareIds);

        hiddenInput.value = ids.join(',');
        hiddenInput.disabled = false;
        updateCounts();
        // Live preview and the configured-dot indicator follow form changes.
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    inputs.forEach(function (cb) {
        cb.addEventListener('change', writeValue);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            inputs.forEach(function (cb) { cb.checked = false; });
            writeValue();
        });
    }

    picker.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('.lgx-brand-picker__remove') : null;
        if (!btn) return;
        var item = btn.closest('.lgx-brand-picker__missing-item');
        if (!item) return;
        var next = item.nextElementSibling || item.previousElementSibling;
        item.parentNode.removeChild(item);
        writeValue();
        var focusTarget = next ? next.querySelector('.lgx-brand-picker__remove') : clearBtn;
        if (focusTarget) focusTarget.focus();
    });

    if (search) {
        search.addEventListener('input', function () {
            var query = search.value.trim().toLowerCase();
            picker.querySelectorAll('.lgx-brand-picker__item').forEach(function (item) {
                var name = item.getAttribute('data-brand-name') || '';
                item.hidden = (query !== '' && name.indexOf(query) === -1);
            });
        });
    }
}

/**
 * 007R-d: Click-to-open field help (global, all tabs).
 *
 * Replaces the old hover+click hybrid with pure click-to-open: hover only
 * changes the icon's color (CSS), it never reveals content. A single shared
 * popover is reused for every ⓘ icon and moved to document.body so its
 * position:fixed placement is measured against the viewport, never clipped
 * by the settings column's own overflow:auto scroll container (same
 * escape-the-overflow approach already used for the Layout Gallery). Only
 * one popover is ever open; it flips above/below the icon depending on
 * available space so fields near the top or bottom of a long tab are never
 * clipped. Native <button> activation means Enter/Space already work with
 * no extra key handling — the browser fires a click event for both.
 */
function initFieldTooltips() {

    const icons = document.querySelectorAll('.lgx-builder-help-icon');
    if (!icons.length) return;

    const popover = document.getElementById('lgx-help-popover');
    if (!popover) return;

    if (popover.parentNode !== document.body) {
        document.body.appendChild(popover);
    }

    let activeIcon = null;

    function position(icon) {
        const iconRect = icon.getBoundingClientRect();
        const popRect = popover.getBoundingClientRect();
        const viewportW = window.innerWidth;
        const viewportH = window.innerHeight;
        const margin = 8;

        const spaceAbove = iconRect.top;
        const spaceBelow = viewportH - iconRect.bottom;
        const placeBelow = spaceBelow >= (popRect.height + margin) || spaceBelow > spaceAbove;

        let top = placeBelow
            ? iconRect.bottom + margin
            : iconRect.top - popRect.height - margin;
        top = Math.max(margin, Math.min(top, viewportH - popRect.height - margin));

        let left = iconRect.left;
        left = Math.max(margin, Math.min(left, viewportW - popRect.width - margin));

        popover.style.top = top + 'px';
        popover.style.left = left + 'px';
        popover.classList.toggle('lgx-help-popover--below', placeBelow);
    }

    function open(icon) {
        if (activeIcon && activeIcon !== icon) {
            activeIcon.setAttribute('aria-expanded', 'false');
        }
        // Set content first so the popover's real size is measurable
        // (it's visibility:hidden, not display:none, while closed — still
        // participates in layout, so getBoundingClientRect() is accurate).
        popover.textContent = icon.getAttribute('data-help') || '';
        position(icon);
        popover.classList.add('is-visible');
        icon.setAttribute('aria-expanded', 'true');
        activeIcon = icon;
    }

    function close() {
        if (!activeIcon) return;
        popover.classList.remove('is-visible');
        activeIcon.setAttribute('aria-expanded', 'false');
        activeIcon = null;
    }

    icons.forEach(function (icon) {
        icon.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (activeIcon === icon) {
                close();
            } else {
                open(icon);
            }
        });
    });

    // Closing (rather than repositioning) on scroll keeps this simple and
    // avoids jitter — clicking the icon again reopens it correctly positioned.
    document.addEventListener('scroll', function () {
        if (activeIcon) close();
    }, true); // capture phase: also catches scroll on the settings column

    window.addEventListener('resize', function () {
        if (activeIcon) close();
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.lgx-builder-help-icon') && !e.target.closest('#lgx-help-popover')) {
            close();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            close();
        }
    });
}

/**
 * Task 010 Phase 3 — manual "Refresh Preview" trigger.
 *
 * Forces the Live Preview iframe to reload the same post_id/nonce it already
 * has (a cache-busting param is appended so the browser doesn't no-op on an
 * unchanged src — it is not a different target, just a forced re-fetch of
 * the same one). Shows a loading state (spinning button icon + an overlay
 * on the iframe itself) between click and the iframe's own `load` event.
 * Only ever touches the iframe element — never reads from, writes to, or
 * submits the settings form. Device toggle (Desktop/Tablet/Mobile) is
 * Phase 4 and is not implemented here.
 */
/**
 * Task 010 Phase 3 fix (per explicit Project Owner correction, 2026-07-27):
 * sets the iframe's own height to match its document's actual content
 * height, so there is no leftover iframe space below the showcase. Centering
 * the <iframe> element in the outer admin page cannot affect where content
 * sits inside the SEPARATE document the iframe loads — this is the only
 * mechanism that actually removes the empty space.
 *
 * Same-origin only (the preview endpoint is always on this site's own
 * domain, so contentDocument access is never blocked) — wrapped in try/catch
 * regardless, since a mid-navigation moment can throw before the new
 * document is fully swapped in.
 *
 * Grow-only bug fix (Task iframe-height, 2026-09-02): `scrollHeight` on a
 * document's root element is spec-floored at that element's own current
 * rendered (viewport) height — which, inside an iframe, is the iframe's own
 * CSS box height. Without resetting that box first, a shorter document
 * loading into an iframe still holding a taller previous height always
 * measures back at least that stale height, so the iframe could grow to
 * fit taller content but never shrink back down for shorter content
 * (Carousel -> Flexbox -> Carousel would stick at Flexbox's height).
 * Collapsing to 0 first removes that floor so the new document lays out at
 * its own true natural height before being measured.
 */
function resizePreviewIframe(iframe) {
    try {
        iframe.style.height = '0px';
        var doc = iframe.contentDocument || iframe.contentWindow.document;

        // Measure .lgx-preview-canvas (preview-template.php), not
        // documentElement. The preview document now sets html/body to
        // height:100% so the showcase can be vertically centred inside the
        // frame — which means documentElement.scrollHeight reports the
        // IFRAME VIEWPORT, not the content, and sizing the iframe from it
        // would be circular (the iframe would lock at its current height and
        // never auto-size again).
        //
        // Measuring the canvas is also immune to the viewport floor that
        // min-height:300px imposes on the line above: `height = 0` cannot
        // actually collapse the frame below that floor, so documentElement
        // was already being over-reported for short showcases (298 measured
        // for 282 of real content) even before centring existed.
        //
        // The documentElement branch is a defensive fallback, not a path taken
        // in normal operation: every call site runs on the iframe's `load`
        // event or later (the two safety-net timeouts, the MutationObserver,
        // the container-resize debounce, Focus Mode), by which point the
        // canvas has always parsed. It exists only for the case where the
        // template is served without the wrapper.
        var canvas = doc.querySelector('.lgx-preview-canvas');
        var height = canvas
            ? Math.ceil(canvas.getBoundingClientRect().height)
            : doc.documentElement.scrollHeight;

        // The frame is box-sizing:border-box with a 1px border top and bottom,
        // so an assigned height of N leaves only N-2 for the document. Without
        // adding the border back, a showcase taller than the min-height floor
        // loses 2px off the bottom — measured on a grid showcase: canvas 860,
        // document viewport 858. Pre-dates the canvas measurement (the old
        // documentElement.scrollHeight path had the same arithmetic), but it is
        // corrected here rather than left in place.
        var cs = window.getComputedStyle(iframe);
        if (cs.boxSizing === 'border-box') {
            height += (parseFloat(cs.borderTopWidth) || 0) + (parseFloat(cs.borderBottomWidth) || 0);
        }

        if (height) {
            iframe.style.height = height + 'px';
        }
    } catch (e) {
        // Not yet accessible this tick — leave the iframe at its current height.
    }
}

function initLivePreview() {

    const iframe    = document.getElementById('lgx-live-preview-iframe');
    const refreshBtn = document.getElementById('lgx-preview-refresh');
    const overlay    = document.getElementById('lgx-preview-loading-overlay');

    if (!iframe || !refreshBtn) return;

    // Disconnected and recreated on every load — a MutationObserver only
    // makes sense scoped to the current document inside the iframe; the
    // previous one becomes stale the moment a new document replaces it.
    let resizeObserver = null;

    function setLoading(isLoading) {
        refreshBtn.disabled = isLoading;
        if (overlay) {
            overlay.hidden = !isLoading;
        }
    }

    // Task 011 Phase 5c: the manual Refresh click handler used to live here and
    // did a bare cache-busted reload of the saved state. It now goes through
    // initLivePreviewAutoUpdate()'s triggerPreviewUpdate() instead, so manual and
    // automatic refreshes are one path. setLoading() below is still owned by this
    // function — the `load` listener clears it whichever path triggered the reload.

    // Fires for both the initial page-load-triggered src and every later
    // refresh-triggered reassignment — one listener covers both.
    iframe.addEventListener('load', function () {
        setLoading(false);
        resizePreviewIframe(iframe);

        // Swiper (and other carousel JS inside the preview) can still
        // adjust the document's own height after the iframe's load event
        // fires. Two staggered re-checks, tuned per Project Owner
        // instruction after a visible scrollbar showed 300ms was too
        // short: 800ms covers typical Swiper init timing, 1500ms is a
        // final safety net for slower/lazy-loaded content.
        setTimeout(function () { resizePreviewIframe(iframe); }, 800);
        setTimeout(function () { resizePreviewIframe(iframe); }, 1500);

        // Disconnect any observer left over from the previous document,
        // then attach a fresh one to this document's body — catches
        // post-load DOM changes the timed re-checks above might land
        // between (Swiper appending loop-mode slide duplicates, adding
        // pagination bullets, etc.). childList/subtree only, not
        // attributes — an autoplaying carousel continuously touches
        // transform/style attributes during animation, which doesn't
        // affect scrollHeight but would fire this callback constantly
        // for no reason if attributes were observed too.
        if (resizeObserver) {
            resizeObserver.disconnect();
            resizeObserver = null;
        }
        try {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (doc && doc.body && window.MutationObserver) {
                resizeObserver = new MutationObserver(function () {
                    resizePreviewIframe(iframe);
                });
                resizeObserver.observe(doc.body, {
                    childList: true,
                    subtree: true
                });
            }
        } catch (e) {
            // Not accessible this tick — the timed re-checks above still cover it.
        }
    });

    // Task 010 Phase 4 (ADR-009 Decision 4): device toggle changes the
    // iframe CONTAINER's width only — .lgx-preview-frame-wrap gets an
    // is-device-tablet/is-device-mobile class (CSS max-width values, not
    // hardcoded here — see the --lgx-preview-width-* custom properties in
    // showcase-builder.css). No viewport meta, no media queries, no
    // browser emulation API touched. Guarded independently of the refresh
    // button above so one missing element never breaks the other.
    const deviceButtons = document.querySelectorAll('.lgx-preview-device-btn');
    const frameWrap = document.getElementById('lgx-preview-frame-wrap');

    /**
     * 009: a pure container-width change (e.g. the settings/preview layout
     * reflowing at the 1439px breakpoint, or the admin sidebar collapsing)
     * produces zero iframe.style.height writes — confirmed live in the 007
     * addendum. The existing MutationObserver above only watches the iframe
     * BODY for content mutations; a width-only change on frame-wrap mutates
     * nothing inside the iframe, so it never fires. This ResizeObserver
     * covers that gap by watching frame-wrap itself and re-running the same
     * existing resizePreviewIframe() — no change to its sizing logic.
     *
     * Loop guard: resizePreviewIframe() only ever writes iframe.style.HEIGHT,
     * never frame-wrap's width, so a callback fired solely because our own
     * height write changed frame-wrap's own box height would report an
     * unchanged contentRect.width here. Re-measuring is skipped unless the
     * width itself actually changed, which keeps this from retriggering
     * itself. A short debounce also collapses the burst of callbacks a
     * continuous window drag produces into one resizePreviewIframe() call.
     */
    if (frameWrap && window.ResizeObserver) {
        let lastFrameWrapWidth = frameWrap.getBoundingClientRect().width;
        let containerResizeDebounce = null;

        const containerResizeObserver = new ResizeObserver(function (entries) {
            const entry = entries[0];
            if (!entry) return;

            const newWidth = entry.contentRect.width;
            if (newWidth === lastFrameWrapWidth) return;
            lastFrameWrapWidth = newWidth;

            if (containerResizeDebounce) clearTimeout(containerResizeDebounce);
            containerResizeDebounce = setTimeout(function () {
                resizePreviewIframe(iframe);
            }, 60);
        });
        containerResizeObserver.observe(frameWrap);

        // No in-page teardown currently removes the preview panel, but
        // disconnect on unload anyway rather than relying on GC to catch it.
        window.addEventListener('pagehide', function () {
            containerResizeObserver.disconnect();
        });
    }

    if (deviceButtons.length && frameWrap) {
        deviceButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const device = btn.getAttribute('data-device');

                deviceButtons.forEach(function (b) {
                    const isActive = b === btn;
                    b.classList.toggle('is-active', isActive);
                    b.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                frameWrap.classList.remove('is-device-tablet', 'is-device-mobile');
                if (device === 'tablet') {
                    frameWrap.classList.add('is-device-tablet');
                } else if (device === 'mobile') {
                    frameWrap.classList.add('is-device-mobile');
                }
                // 'desktop' adds no class — frame-wrap stays at its default 100% width.

                // The showcase's own responsive CSS can reflow at a narrower
                // container width (e.g. a grid layout showing fewer columns),
                // which changes the document's height — re-measure rather
                // than leaving the iframe at its previous, now-stale height.
                resizePreviewIframe(iframe);

                // Task 012A — Dispatch custom event for responsive settings sync
                document.dispatchEvent(new CustomEvent('lgx:device-change', {
                    detail: { device: device, trigger: 'preview-toolbar' }
                }));
            });
        });
    }
}

/**
 * Task 011 Phase 2 — automatic Live Preview update on field change.
 *
 * Delegated listener on .lgx-builder__settings (not per-field — per the
 * brief, works for dynamically shown/hidden fields, e.g. layout-type or
 * content-source panels, with no re-registration needed when they toggle).
 * Two-tier debounce per ADR-009 Decision 3, then triggerPreviewUpdate()
 * POSTs the current (possibly unsaved) form state to the Task 011 Phase 1
 * AJAX endpoint and, once it confirms the merge is ready, does the
 * cache-busted iframe src reload from the 2026-07-27 ADR-009 Task 011
 * amendment — the update mechanism itself lives in initLivePreview()'s own
 * `load` handler (unchanged): it already clears the loading overlay
 * whenever the iframe finishes loading, regardless of what triggered the
 * reload, so this function only needs to show the overlay when starting.
 *
 * Also listens for the existing `lgx:status-update` event (Task 008) so
 * layout switches trigger an update too — not debounced, since a layout
 * switch is one discrete user action, not a rapid-fire stream the way
 * typing or dragging is.
 */
function initLivePreviewAutoUpdate() {

    const settingsForm = document.querySelector('.lgx-builder__settings');
    const iframe        = document.getElementById('lgx-live-preview-iframe');
    const overlay       = document.getElementById('lgx-preview-loading-overlay');
    const postForm      = document.getElementById('post');
    const refreshBtn    = document.getElementById('lgx-preview-refresh');

    if (!settingsForm || !iframe || !postForm) return;

    // Clean base URL (post_id + nonce, no cache-bust param) — captured once
    // here, before any update has appended its own &t=, exactly like
    // initLivePreview()'s own baseSrc capture above. Both functions run
    // once at page load, before any user interaction, so both read the
    // same untouched initial src.
    const baseUrl = iframe.getAttribute('src');
    const postId  = new URL(baseUrl, window.location.href).searchParams.get('post_id');

    let debounceTimer = null;

    function setLoading(isLoading) {
        if (overlay) {
            overlay.hidden = !isLoading;
        }
        // Mirrors initLivePreview()'s own button handling, so the button stays
        // disabled during an automatic update too — not just a manual click.
        // Cleared again by that function's `load` listener once the reload lands.
        if (refreshBtn) {
            refreshBtn.disabled = isLoading;
        }
    }

    function triggerPreviewUpdate() {
        const formData = new FormData(postForm);
        formData.append('action', 'lgx_preview_render');
        formData.append('post_id', postId);

        setLoading(true);

        fetch(ajaxurl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (result && result.success) {
                    // The actual visible update: a real navigation, so
                    // Swiper initialises correctly (ADR-009 Task 011
                    // amendment) — not an innerHTML swap.
                    iframe.src = baseUrl + '&t=' + Date.now();
                } else {
                    // Merge request failed server-side (bad nonce,
                    // permissions, etc.) — leave the current preview
                    // showing rather than reload to a stale/errored state.
                    setLoading(false);
                    // Task 021: this used to fail completely silently —
                    // the user had no way to know their change wasn't reflected.
                    lgxNotifyError(lgxText('previewFailed', 'Preview update failed — showing the last successful preview.'));
                }
            })
            .catch(function () {
                setLoading(false);
                lgxNotifyError(lgxText('previewFailed', 'Preview update failed — showing the last successful preview.'));
            });
    }

    function onFieldChanged(e) {
        const isSlider = e.target.matches && e.target.matches('input[type="range"]');
        // .lgx_color_picker is this codebase's actual color-picker input
        // class (admin/js/logo-slider-wp-admin.js calls $('.lgx_color_picker').wpColorPicker())
        // — WordPress's wpColorPicker widget fires 'change' continuously
        // while dragging inside the picker, which is exactly why colour
        // fields need the longer, continuous-interaction debounce tier.
        const isColour = e.target.matches && e.target.matches('.lgx_color_picker');
        const delay = (isSlider || isColour) ? 500 : 300;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(triggerPreviewUpdate, delay);
    }

    // change: toggles, selects, colour pickers (on each colour change).
    // input: text fields and number steppers, while typing.
    settingsForm.addEventListener('change', onFieldChanged);
    settingsForm.addEventListener('input', onFieldChanged);

    document.addEventListener('lgx:status-update', triggerPreviewUpdate);

    // Task 011 Phase 5c — the manual "Refresh Preview" button (Task 010) now
    // shares this exact path rather than doing its own bare src reload. Without
    // this it re-requested the endpoint WITHOUT posting the form, so it showed
    // whatever the 60s override transient happened to still hold — the last
    // auto-updated state, or the saved state once that expired — never reliably
    // the user's current unsaved edits. Not debounced: a click is one discrete
    // deliberate action, same reasoning as the lgx:status-update subscriber.
    if (refreshBtn) {
        refreshBtn.addEventListener('click', triggerPreviewUpdate);
    }

    // RB-1 Part C — a new/unsaved showcase has no saved meta, so
    // display_lgx_logo_slider()'s empty-meta branch (public/class-logo-slider-wp-public.php)
    // returns a static "Loading preview..." placeholder that nothing ever
    // replaces, since no field has changed yet to trigger onFieldChanged().
    // Firing the existing mechanism once here reuses the same AJAX merge
    // path as every other update — this branch stays generically
    // triggerable (not hardcoded to one fixed response) since a future
    // Demo Data feature hooks the same empty-meta branch server-side.
    if (window.lgxShowcaseBuilderDefaults && window.lgxShowcaseBuilderDefaults.isEmptyMeta) {
        triggerPreviewUpdate();
    }
}

/**
 * Task 012A — Responsive Settings & Live Preview Synchronization.
 *
 * Subscribes to 'lgx:device-change' events dispatched by the preview toolbar
 * (or input focus) and highlights the corresponding column field row when
 * the Responsive tab is open.
 */
function initResponsiveSync() {
    const responsiveTabPanel = document.getElementById('lgx-tabpanel-responsive');
    if (!responsiveTabPanel) return;

    // Field mapping per device
    const deviceFieldMap = {
        'mobile': ['lgx_mobile_item'],
        'tablet': ['lgx_tablet_item'],
        'desktop': ['lgx_desktop_item', 'lgx_large_desktop_item']
    };

    let highlightTimeout = null;

    function clearHighlights() {
        const activeFields = responsiveTabPanel.querySelectorAll('.lgx-field--device-active');
        activeFields.forEach(function (el) {
            el.classList.remove('lgx-field--device-active');
        });
        if (highlightTimeout) {
            clearTimeout(highlightTimeout);
            highlightTimeout = null;
        }
    }

    function highlightDeviceFields(device) {
        clearHighlights();
        const fieldIds = deviceFieldMap[device] || [];
        fieldIds.forEach(function (fieldId) {
            const input = document.getElementById(fieldId);
            if (input) {
                const fieldRow = input.closest('.lgx-field');
                if (fieldRow) {
                    fieldRow.classList.add('lgx-field--device-active');
                }
            }
        });

        // Remove highlight smoothly after 1500ms
        highlightTimeout = setTimeout(function () {
            clearHighlights();
        }, 1500);
    }

    document.addEventListener('lgx:device-change', function (e) {
        if (!e.detail || !e.detail.device) return;

        // Only trigger visual highlight if Responsive tab is currently active (visible)
        const responsiveTab = document.getElementById('lgx-tab-responsive');
        const isTabActive = responsiveTab && (responsiveTab.getAttribute('aria-selected') === 'true' || !responsiveTabPanel.hidden);

        if (isTabActive && e.detail.trigger === 'preview-toolbar') {
            highlightDeviceFields(e.detail.device);
        }
    });

    // Focus feedback on column inputs
    const columnFieldIds = ['lgx_large_desktop_item', 'lgx_desktop_item', 'lgx_tablet_item', 'lgx_mobile_item'];
    columnFieldIds.forEach(function (id) {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('focus', function () {
                let device = 'desktop';
                if (id === 'lgx_mobile_item') device = 'mobile';
                else if (id === 'lgx_tablet_item') device = 'tablet';

                document.dispatchEvent(new CustomEvent('lgx:device-change', {
                    detail: { device: device, trigger: 'field-focus' }
                }));
            });
        }
    });
}

/**
 * Task 012B — Responsive Admin Shell Controller.
 * 009: the slide-over Live Preview drawer this used to also control (<1440px,
 * with backdrop/close/Escape/focus-trap) has been removed — the preview is
 * normal-flow at every width now (see initLivePreview()'s ResizeObserver for
 * the iframe-resize half of that change). Remaining responsibilities:
 *
 * 1. Focus Mode controller (Desktop >= 1440px).
 * 2. Mobile navigation <select> fallback (<782px) synchronized with WAI-ARIA tab switching.
 * 3. Breakpoint transition cleanup preserving the live iframe instance (ADR-009).
 */
function initResponsiveShell() {
    const navSelect        = document.getElementById('lgx-builder-nav-select');
    const tablist          = document.querySelector('.lgx-ui-tablist');
    const iframe           = document.getElementById('lgx-live-preview-iframe');

    const focusToggleBtn   = document.getElementById('lgx-builder-focus-toggle');
    const focusCue         = document.getElementById('lgx-builder-focus-cue');
    const focusCueAction   = document.getElementById('lgx-builder-focus-cue-action');
    const focusCueDismiss  = document.getElementById('lgx-builder-focus-cue-dismiss');
    const builderRoot      = document.querySelector('.lgx-builder');
    const postIdInput      = document.getElementById('post_ID');
    const focusStorageKey  = postIdInput ? ('lgx_focus_mode_' + postIdInput.value) : 'lgx_focus_mode';

    // -------------------------------------------------------------------------
    // 1. Focus Mode Controller (Desktop >= 1440px)
    // -------------------------------------------------------------------------
    function setFocusMode(enable) {
        if (!builderRoot) return;
        if (enable) {
            builderRoot.classList.add('is-focus-mode');
            if (focusToggleBtn) {
                focusToggleBtn.setAttribute('aria-pressed', 'true');
            }
            try {
                sessionStorage.setItem(focusStorageKey, '1');
            } catch (e) {}
        } else {
            builderRoot.classList.remove('is-focus-mode');
            if (focusToggleBtn) {
                focusToggleBtn.setAttribute('aria-pressed', 'false');
            }
            if (focusCue) {
                focusCue.style.display = 'none';
            }
            try {
                sessionStorage.removeItem(focusStorageKey);
            } catch (e) {}
        }

        // Re-measure iframe content height once layout transition completes
        setTimeout(function () {
            if (iframe && typeof resizePreviewIframe === 'function') {
                resizePreviewIframe(iframe);
            }
        }, 260);
    }

    // Initial sync on load (in addition to early template script)
    if (builderRoot && window.innerWidth >= 1440) {
        let isFocus = false;
        try {
            isFocus = sessionStorage.getItem(focusStorageKey) === '1';
        } catch (e) {}
        if (isFocus) {
            builderRoot.classList.add('is-focus-mode');
            if (focusToggleBtn) {
                focusToggleBtn.setAttribute('aria-pressed', 'true');
            }
            // Task 009: Show discoverability cue notice specifically when Focus Mode is restored on load
            if (focusCue) {
                focusCue.style.display = 'flex';
            }
        }
    } else if (builderRoot && window.innerWidth < 1440) {
        builderRoot.classList.remove('is-focus-mode');
        if (focusToggleBtn) {
            focusToggleBtn.setAttribute('aria-pressed', 'false');
        }
        if (focusCue) {
            focusCue.style.display = 'none';
        }
    }

    if (focusToggleBtn) {
        focusToggleBtn.addEventListener('click', function () {
            const isCurrentlyFocus = builderRoot && builderRoot.classList.contains('is-focus-mode');
            setFocusMode(!isCurrentlyFocus);
        });
    }

    if (focusCueAction) {
        focusCueAction.addEventListener('click', function (e) {
            e.preventDefault();
            setFocusMode(false);
            if (focusToggleBtn) {
                focusToggleBtn.focus();
            }
        });
    }

    if (focusCueDismiss) {
        focusCueDismiss.addEventListener('click', function (e) {
            e.preventDefault();
            if (focusCue) {
                focusCue.style.display = 'none';
            }
        });
    }

    // Keyboard accessibility: Escape key exits Focus Mode
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (builderRoot && builderRoot.classList.contains('is-focus-mode')) {
                setFocusMode(false);
                if (focusToggleBtn) {
                    focusToggleBtn.focus();
                }
            }
        }
    });

    // Clean up Focus Mode state across the 1440px breakpoint boundary.
    // Debounced to prevent redundant triggers during continuous slow dragging.
    let resizeTimer = null;
    window.addEventListener('resize', function () {
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            // When dropping below 1440px: clear Focus Mode and clear sessionStorage
            // so resizing back up above 1440px does NOT silently reactivate Focus Mode
            if (window.innerWidth < 1440 && builderRoot && builderRoot.classList.contains('is-focus-mode')) {
                setFocusMode(false);
            }
        }, 60);
    });

    // -------------------------------------------------------------------------
    // Task 013 (1c): keep aria-orientation in sync with the 1023px breakpoint
    // (showcase-builder.css) where the tablist visually becomes a horizontal
    // strip. lgx-admin-ui.js's arrow-key handler reads this attribute fresh
    // on every keydown, so updating it here also fixes Left/Right vs Up/Down
    // key direction at this breakpoint, not just the AT-announced semantics.
    // Same debounced-resize pattern as the drawer cleanup above (60ms).
    // -------------------------------------------------------------------------
    if (tablist) {
        const LGX_TABLIST_HORIZONTAL_BREAKPOINT = 1023;

        function syncTablistOrientation() {
            const isHorizontal = window.innerWidth <= LGX_TABLIST_HORIZONTAL_BREAKPOINT;
            tablist.setAttribute('aria-orientation', isHorizontal ? 'horizontal' : 'vertical');
        }

        syncTablistOrientation();

        let orientationResizeTimer = null;
        window.addEventListener('resize', function () {
            if (orientationResizeTimer) clearTimeout(orientationResizeTimer);
            orientationResizeTimer = setTimeout(syncTablistOrientation, 60);
        });
    }

    // -------------------------------------------------------------------------
    // 2. Mobile <select> Navigation Fallback Controller (<782px)
    // -------------------------------------------------------------------------
    if (navSelect && tablist) {
        const tabs = Array.from(tablist.querySelectorAll('.lgx-ui-tab'));

        // When user changes the mobile <select> dropdown, trigger tab activation
        navSelect.addEventListener('change', function () {
            const selectedTabId = this.value;
            const targetTab = tabs.find(function (tab) {
                return tab.id === 'lgx-tab-' + selectedTabId;
            });
            if (targetTab) {
                targetTab.click();
            }
        });

        // When tabs are activated, keep the <select> dropdown in sync
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const tabId = tab.id.replace('lgx-tab-', '');
                if (navSelect.value !== tabId) {
                    navSelect.value = tabId;
                }
            });
        });
    }
}

/**
 * Task 013 Phase 4, Item 1 — per-tab "configured" indicator dot.
 *
 * Diffs each field's live DOM value against its registry default (localized
 * server-side as `lgxShowcaseBuilderDefaults`, see builder-wrapper.php) and
 * toggles the tab's hidden .lgx-ui-tab-indicator span when any field inside
 * that tab's panel no longer matches its default.
 */
function initConfiguredDotIndicators() {
    if (typeof lgxShowcaseBuilderDefaults === 'undefined' || !lgxShowcaseBuilderDefaults.fields) {
        return;
    }

    const settingsSection = document.querySelector('.lgx-builder__settings');
    if (!settingsSection) return;

    const fieldDefaults = lgxShowcaseBuilderDefaults.fields;

    // Most field types render with id="<field id>" (confirmed across every
    // Lgx_Meta_Form field type). The one exception is card_select, whose
    // radio inputs share a `name` but carry no id — read the checked radio
    // by name instead. IMG uses getAttribute('src'), not the .src property:
    // an empty src attribute resolves .src to the current page URL in some
    // browsers (the same trap task011g Phase 4 hit for upload-field testing).
    function getCurrentValue(fieldId, name) {
        const el = document.getElementById(fieldId);
        if (el) {
            if (el.tagName === 'IMG') {
                return el.getAttribute('src') || '';
            }
            if (el.tagName === 'INPUT' && el.type === 'checkbox') {
                return el.checked ? 'yes' : 'no';
            }
            if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                return el.value;
            }
        }
        if (name) {
            const checkedRadio = settingsSection.querySelector('input[name="' + CSS.escape(name) + '"]:checked');
            if (checkedRadio) {
                return checkedRadio.value;
            }
        }
        return null; // Field isn't rendered in this DOM at all — skip it.
    }

    function refreshDots() {
        const dirtyPanelIds = Object.create(null);

        Object.keys(fieldDefaults).forEach(function (fieldId) {
            const entry = fieldDefaults[fieldId];
            const currentValue = getCurrentValue(fieldId, entry.name);
            if (currentValue === null) return;

            if (String(currentValue) !== String(entry.default || '')) {
                const el = document.getElementById(fieldId) ||
                    (entry.name ? settingsSection.querySelector('input[name="' + CSS.escape(entry.name) + '"]') : null);
                const panel = el && el.closest ? el.closest('[role="tabpanel"]') : null;
                if (panel) {
                    dirtyPanelIds[panel.id] = true;
                }
            }
        });

        document.querySelectorAll('.lgx-ui-tablist .lgx-ui-tab').forEach(function (tab) {
            const panelId = tab.getAttribute('aria-controls');
            const indicator = tab.querySelector('.lgx-ui-tab-indicator');
            if (!indicator) return;
            indicator.hidden = !dirtyPanelIds[panelId];
        });
    }

    refreshDots();
    settingsSection.addEventListener('change', refreshDots);
    settingsSection.addEventListener('input', refreshDots);
}

/**
 * B53 — syncs a checkbox_list() field's visible checkboxes into its paired
 * hidden input (comma-joined value, matching the frontend's explode(',')
 * expectation). Generic: works for any current or future .lgx-checkbox-list
 * container, not hardcoded to lgx_from_category. Locked checkboxes are
 * covered by the existing initProFieldLocking() delegation (.lgx-pro-locked-field
 * / .lgx-field__control) — no lock-specific code needed here.
 *
 * 2026-09-07: extended with mutual exclusion between the "All" checkbox
 * ([data-checkbox-list-all], rendered by Lgx_Meta_Form::checkbox_list() when
 * the options array carries an 'all' key) and every individual category
 * checkbox. Owner-confirmed behavior: selecting All instantly clears every
 * individual selection; selecting any individual category instantly clears
 * All — no confirmation step, the checkboxes visibly changing is the
 * feedback. Also re-checks All if the user unchecks the last remaining
 * individual category, so the UI is never left with nothing visibly
 * checked while the hidden value silently falls back to 'all' below — that
 * silent-default gap is exactly what this feature exists to close. Same
 * reasoning applies if the user clicks the already-checked All box directly
 * to turn it off: that's re-checked immediately too. All only ever becomes
 * unchecked as a side effect of checking an individual category — never by
 * being clicked off on its own.
 */
function initCheckboxListSync() {
    document.querySelectorAll('.lgx-checkbox-list').forEach(function (container) {
        const targetId = container.getAttribute('data-checkbox-list-for');
        const hiddenInput = targetId ? document.getElementById(targetId) : null;
        if (!hiddenInput) return;

        container.addEventListener('change', function (e) {
            if (!e.target.matches || !e.target.matches('.lgx-checkbox-list__input')) return;

            const allInput = container.querySelector('.lgx-checkbox-list__input[data-checkbox-list-all]');
            const isAllOption = allInput === e.target;

            if (allInput) {
                const otherInputs = Array.prototype.slice
                    .call(container.querySelectorAll('.lgx-checkbox-list__input'))
                    .filter(function (cb) { return cb !== allInput; });

                if (isAllOption && e.target.checked) {
                    otherInputs.forEach(function (cb) { cb.checked = false; });
                } else if (isAllOption && !e.target.checked) {
                    // All can't be turned off by clicking it directly -- only
                    // by checking an individual category (handled below).
                    // Otherwise this reproduces the exact "nothing visibly
                    // checked" ambiguity the feature exists to close.
                    allInput.checked = true;
                } else if (!isAllOption && e.target.checked) {
                    allInput.checked = false;
                } else if (!isAllOption && !e.target.checked) {
                    const anyOtherChecked = otherInputs.some(function (cb) { return cb.checked; });
                    if (!anyOtherChecked) {
                        allInput.checked = true;
                    }
                }
            }

            const checked = Array.prototype.slice
                .call(container.querySelectorAll('.lgx-checkbox-list__input:checked'))
                .map(function (cb) { return cb.value; });

            hiddenInput.value = checked.length ? checked.join(',') : 'all';

            // Bubbling change+input so initLivePreviewAutoUpdate()'s delegated
            // listener on .lgx-builder__settings picks this up exactly like a
            // real field edit (it reads the form via FormData at trigger time,
            // not e.target, so it only needs the hidden input's value to already
            // be current when the event fires — true here since we set it above).
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
}

/**
 * Group Navigation Controller
 *
 * Implements WAI-ARIA accessible sidebar subgroup navigation and
 * IntersectionObserver-driven scroll synchronization for settings groups.
 *
 * Requirements (per Master Plan §1 & Final Implementation Brief):
 * - Option 2: all currently applicable groups remain visible; nav changes scroll position only.
 * - Tab Label click: navigates to tab panel, sets tab ACTIVE.
 * - Chevron click: toggles that tab's subgroup list EXPANDED (single-expanded policy).
 * - Subgroup button click: smooth-scrolls to target group, marks subgroup ACTIVE immediately (optimistic).
 * - Visibility sync: synchronizes hidden state with .lgx-panelbody--type-hidden on lgx:status-update.
 * - Active fallback: if active subgroup becomes hidden, moves active to first visible group (no scroll).
 * - IntersectionObserver: Heuristic B (scrollspy activation-line band) inside .lgx-builder__settings.
 */
function initGroupNavigation() {
    const nav = document.querySelector('.lgx-builder__navigation');
    const settingsSection = document.querySelector('.lgx-builder__settings');
    const tablist = document.querySelector('.lgx-ui-tablist');
    if (!nav || !settingsSection || !tablist) return;

    const tabs = Array.from(tablist.querySelectorAll('.lgx-ui-tab'));
    const subgroupBtns = Array.from(nav.querySelectorAll('.lgx-builder-nav__subgroup-btn'));

    let currentObserver = null;
    let lastActiveSubgroupId = null;

    /**
     * Set a subgroup button active by its target ID string (e.g. 'lgx-group-layout_settings-carousel_general').
     * @param {string} targetId DOM ID of the target group.
     */
    function setActiveSubgroup(targetId) {
        if (!targetId) return;
        lastActiveSubgroupId = targetId;

        subgroupBtns.forEach(function (btn) {
            const isMatch = btn.getAttribute('data-target-group') === targetId;
            btn.classList.toggle('is-active', isMatch);
            if (isMatch) {
                btn.setAttribute('aria-current', 'true');
            } else {
                btn.removeAttribute('aria-current');
            }
        });
    }

    /**
     * Expand or collapse a tab's subgroup accordion list.
     * Enforces the single-expanded policy: opening one tab closes any other.
     * @param {Element} tab The tab button.
     * @param {boolean} [forceState] Optional forced boolean state (true = open, false = closed).
     */
    function toggleTabSubgroups(tab, forceState) {
        const tabId = tab.id.replace('lgx-tab-', '');
        const targetList = document.getElementById('lgx-subgroups-' + tabId);
        const parentItem = tab.closest('.lgx-builder-nav__item');
        if (!targetList || !parentItem) return;

        const isCurrentlyExpanded = tab.getAttribute('aria-expanded') === 'true';
        const willExpand = (typeof forceState === 'boolean') ? forceState : !isCurrentlyExpanded;

        // Close any other open tab's group list (single-expanded policy)
        tabs.forEach(function (otherTab) {
            if (otherTab !== tab) {
                if (otherTab.hasAttribute('aria-expanded')) {
                    otherTab.setAttribute('aria-expanded', 'false');
                }
                const otherTabId = otherTab.id.replace('lgx-tab-', '');
                const otherList = document.getElementById('lgx-subgroups-' + otherTabId);
                if (otherList) {
                    otherList.setAttribute('aria-hidden', 'true');
                }
                const otherItem = otherTab.closest('.lgx-builder-nav__item');
                if (otherItem) otherItem.classList.remove('is-expanded');
            }
        });

        // Apply state to this tab
        if (willExpand) {
            tab.setAttribute('aria-expanded', 'true');
            targetList.setAttribute('aria-hidden', 'false');
            parentItem.classList.add('is-expanded');
        } else {
            tab.setAttribute('aria-expanded', 'false');
            targetList.setAttribute('aria-hidden', 'true');
            parentItem.classList.remove('is-expanded');
        }
    }

    let isProgrammaticScroll = false;
    let programmaticScrollTimer = null;

    const tabScrollPositions = {};

    let scrollSaveTimer = null;
    settingsSection.addEventListener('scroll', function () {
        if (scrollSaveTimer) clearTimeout(scrollSaveTimer);
        scrollSaveTimer = setTimeout(function () {
            if (currentActiveTabId) {
                tabScrollPositions[currentActiveTabId] = settingsSection.scrollTop;
            }
        }, 30);
    }, { passive: true });

    /**
     * Handle subgroup item click interactions.
     */
    subgroupBtns.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = btn.getAttribute('data-target-group');
            if (!targetId) return;

            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                // Optimistic active highlight immediately
                setActiveSubgroup(targetId);

                isProgrammaticScroll = true;
                if (programmaticScrollTimer) clearTimeout(programmaticScrollTimer);
                programmaticScrollTimer = setTimeout(function () {
                    isProgrammaticScroll = false;
                }, 700);

                const targetTop = targetEl.getBoundingClientRect().top - settingsSection.getBoundingClientRect().top + settingsSection.scrollTop;
                if (currentActiveTabId) {
                    tabScrollPositions[currentActiveTabId] = targetTop;
                }
                settingsSection.scrollTo({
                    top: targetTop,
                    behavior: 'smooth'
                });
            }
        });
    });

    /**
     * Disconnect previous observer and setup IntersectionObserver for the newly active tab panel.
     * @param {string} tabKey Registry tab ID (e.g. 'layout_settings', 'style').
     */
    function setupTabObserver(tabKey) {
        if (currentObserver) {
            currentObserver.disconnect();
            currentObserver = null;
        }

        // Task 014/Commit 4b: the 'general_settings' exclusion is deleted here,
        // not renamed to 'source_settings' — General never had real subgroups
        // to scroll-sync against (its one registry field was never rendered),
        // but 'source_settings' (Content, the new default tab) has real
        // subgroups and must be observed normally. Keeping any hardcoded
        // exclusion here would silently break Content's own scroll-sync.
        if (!tabKey) {
            return;
        }

        const panel = document.getElementById('lgx-panel-' + tabKey);
        if (!panel) return;

        const visiblePanels = Array.from(panel.querySelectorAll('.lgx-ui-panelbody:not(.lgx-panelbody--type-hidden)'));
        if (!visiblePanels.length) return;

        let bandHits = {};

        // Heuristic B: scrollspy activation-line band near container top (top + 60px)
        const bottomMargin = Math.max(10, settingsSection.clientHeight - 65);
        const rootMarginStr = '-60px 0px -' + bottomMargin + 'px 0px';

        currentObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                bandHits[entry.target.id] = entry.isIntersecting;
            });

            if (isProgrammaticScroll) {
                return; // Guard against intermediate active flashes during smooth scroll
            }

            // Find intersecting groups in DOM order
            const intersecting = visiblePanels.filter(function (el) {
                return bandHits[el.id];
            });

            if (intersecting.length > 0) {
                const activeEl = intersecting[intersecting.length - 1];
                setActiveSubgroup(activeEl.id);
            }
            // If momentary transition gap, persist last-known-active (no clear)
        }, {
            root: settingsSection,
            rootMargin: rootMarginStr,
            threshold: 0
        });

        visiblePanels.forEach(function (el) {
            currentObserver.observe(el);
        });
    }

    /**
     * Synchronize subgroup items hidden state with .lgx-panelbody--type-hidden on layout changes.
     * Enforces active-state fallback rule: if active subgroup becomes hidden, move active to first visible group (no scroll).
     */
    function syncVisibilityAndFallback() {
        const activeTab = tabs.find(function (t) { return t.getAttribute('aria-selected') === 'true'; });
        const activeTabId = activeTab ? activeTab.id.replace('lgx-tab-', '') : 'source_settings';

        subgroupBtns.forEach(function (btn) {
            const targetId = btn.getAttribute('data-target-group');
            const targetEl = targetId ? document.getElementById(targetId) : null;
            const parentLi = btn.closest('.lgx-builder-nav__subgroup-item');

            if (parentLi) {
                const isHidden = targetEl ? targetEl.classList.contains('lgx-panelbody--type-hidden') : false;
                parentLi.classList.toggle('is-type-hidden', isHidden);
            }
        });

        // Check if currently active subgroup item in the active tab became hidden
        const activeSubgroupList = document.getElementById('lgx-subgroups-' + activeTabId);

        if (activeSubgroupList) {
            const activeBtn = activeSubgroupList.querySelector('.lgx-builder-nav__subgroup-btn[aria-current="true"]');
            const isCurrentActiveHidden = activeBtn ? activeBtn.closest('.lgx-builder-nav__subgroup-item').classList.contains('is-type-hidden') : false;

            if (isCurrentActiveHidden || !activeBtn) {
                const firstVisibleBtn = activeSubgroupList.querySelector('.lgx-builder-nav__subgroup-item:not(.is-type-hidden) .lgx-builder-nav__subgroup-btn');
                if (firstVisibleBtn) {
                    const fallbackTargetId = firstVisibleBtn.getAttribute('data-target-group');
                    setActiveSubgroup(fallbackTargetId);
                    // Crucial: NO scroll trigger on fallback
                }
            }
        }

        // Reconnect observer for current tab
        setupTabObserver(activeTabId);
    }

    // Listen for layout status updates to sync type visibility and fallback
    document.addEventListener('lgx:status-update', syncVisibilityAndFallback);

    const initialActiveTab = tabs.find(function (t) { return t.getAttribute('aria-selected') === 'true'; });
    const initialTabId = initialActiveTab ? initialActiveTab.id.replace('lgx-tab-', '') : 'source_settings';
    let currentActiveTabId = initialTabId;

    // Tab click listener — handles simultaneous panel activation, subgroup expansion, and scroll restoration
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const tabId = tab.id.replace('lgx-tab-', '');
            const hasSubgroups = !!document.getElementById('lgx-subgroups-' + tabId);
            const wasAlreadyActive = (currentActiveTabId === tabId);
            currentActiveTabId = tabId;

            if (wasAlreadyActive) {
                // Toggle accordion on already-active tab
                if (hasSubgroups) {
                    toggleTabSubgroups(tab);
                }
            } else {
                // 1. Switching to this tab: expand its subgroup list and collapse other tabs
                if (hasSubgroups) {
                    toggleTabSubgroups(tab, true);
                } else {
                    // Childless tab (e.g. General): close all other open subgroups
                    tabs.forEach(function (otherTab) {
                        if (otherTab.hasAttribute('aria-expanded')) {
                            otherTab.setAttribute('aria-expanded', 'false');
                        }
                        const otherTabId = otherTab.id.replace('lgx-tab-', '');
                        const otherList = document.getElementById('lgx-subgroups-' + otherTabId);
                        if (otherList) {
                            otherList.setAttribute('aria-hidden', 'true');
                        }
                        const otherItem = otherTab.closest('.lgx-builder-nav__item');
                        if (otherItem) otherItem.classList.remove('is-expanded');
                    });
                }

                // 2. Restore incoming tab's saved scroll position
                const savedScroll = (typeof tabScrollPositions[tabId] === 'number') ? tabScrollPositions[tabId] : 0;
                requestAnimationFrame(function () {
                    const maxScroll = Math.max(0, settingsSection.scrollHeight - settingsSection.clientHeight);
                    const targetScroll = Math.min(savedScroll, maxScroll);
                    settingsSection.scrollTop = targetScroll;

                    setupTabObserver(tabId);

                    // If switching to a tab with subgroups, set active to first visible subgroup if none active
                    const subgroupList = document.getElementById('lgx-subgroups-' + tabId);
                    if (subgroupList) {
                        const visibleBtns = Array.from(subgroupList.querySelectorAll('.lgx-builder-nav__subgroup-item:not(.is-type-hidden) .lgx-builder-nav__subgroup-btn'));
                        const hasActive = visibleBtns.some(function (b) { return b.getAttribute('aria-current') === 'true'; });
                        if (!hasActive && visibleBtns.length) {
                            setActiveSubgroup(visibleBtns[0].getAttribute('data-target-group'));
                        }
                    }
                });
            }
        });
    });

    // Initial setup on load
    if (initialActiveTab && document.getElementById('lgx-subgroups-' + initialTabId)) {
        toggleTabSubgroups(initialActiveTab, true);
    }
    syncVisibilityAndFallback();
    setupTabObserver(initialTabId);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initShowcaseBuilder();
        initLayoutGallery();
        initBuilderStatus();
        initTemplatePillNoOp();
        initShowcaseNameSync();
        initTitleFieldIcons();
        initHeaderShortcodeCopy();
        initStickySaveButton();
        initSaveSuccessNotice();
        initLayoutSettingsVisibility();
        initSourceSettingsVisibility();
        initBrandPicker();
        initFieldTooltips();
        initLivePreview();
        initLivePreviewAutoUpdate();
        initResponsiveSync();
        initResponsiveShell();
        initProFieldLocking();
        initProUpgradeModal();
        initUsageModal();
        initTickerContextNote();
        initFieldDependencyEngine();
        initConfiguredDotIndicators();
        initCheckboxListSync();
        initGroupNavigation();
        initLogoDimensionTypeFollowsValue();
    });
} else {
    initShowcaseBuilder();
    initLayoutGallery();
    initBuilderStatus();
    initTemplatePillNoOp();
    initShowcaseNameSync();
    initTitleFieldIcons();
    initHeaderShortcodeCopy();
    initStickySaveButton();
    initSaveSuccessNotice();
    initLayoutSettingsVisibility();
    initSourceSettingsVisibility();
    initBrandPicker();
    initFieldTooltips();
    initLivePreview();
    initLivePreviewAutoUpdate();
    initResponsiveSync();
    initResponsiveShell();
    initProFieldLocking();
    initProUpgradeModal();
    initUsageModal();
    initTickerContextNote();
    initFieldDependencyEngine();
    initConfiguredDotIndicators();
    initCheckboxListSync();
    initGroupNavigation();
    initLogoDimensionTypeFollowsValue();
}

/**
 * Handle interactions on Pro-locked fields.
 * Intercepts click/keydown to prevent edits and trigger the upgrade flow.
 */
function initProFieldLocking() {
    function handleLockedInteraction(e) {
        const lockedWrapper = e.target.closest('.lgx-pro-locked-field');
        if (!lockedWrapper) return;

        if (!e.target.closest('.lgx-field__control')) return;

        // B60: a select() Model A field (the current/default option stays
        // genuinely selectable) must not have this whole-field interceptor
        // swallow clicks meant to open the dropdown — it already has its
        // own separate, correctly-scoped per-option interceptor below (the
        // data-pro-locked-option change-event handler). Narrowly scoped to
        // selects that actually carry that per-option protection — a
        // group2SelectText() field's own <select> sub-control shares the
        // .lgx-pro-locked-field wrapper but has no per-option marking at
        // all, so it must stay caught by this whole-field block or it
        // would become freely changeable with nothing else guarding it.
        const targetSelect = e.target.closest('select');
        if (targetSelect && targetSelect.querySelector('option[data-pro-locked-option="true"]')) {
            return;
        }

        if (e.type === 'keydown' && e.key === 'Tab') {
            return;
        }
        
        if (e.type === 'click' || e.type === 'mousedown' || (e.type === 'keydown' && (e.key === 'Enter' || e.key === ' '))) {
            e.preventDefault();
            e.stopPropagation();
            
            if (e.target.tagName === 'INPUT' && (e.target.type === 'checkbox' || e.target.type === 'radio')) {
                setTimeout(() => e.target.focus(), 0);
            }
            
            if (e.type !== 'mousedown') {
                const event = new CustomEvent('lgx:require-pro-upgrade', { 
                    detail: { trigger: e.target },
                    bubbles: true
                });
                document.dispatchEvent(event);
            }
        }
    }

    const container = document.querySelector('.lgx-builder') || document;
    container.addEventListener('click', handleLockedInteraction, true);
    container.addEventListener('keydown', handleLockedInteraction, true);
    container.addEventListener('mousedown', handleLockedInteraction, true);

    // Option-level gating (payload interception)
    // If a user selects an <option> with data-pro-locked-option="true", 
    // we revert to the previous value and open the Pro modal.
    const selects = container.querySelectorAll('select.lgx_app_meta_select, select.lgx-input');
    selects.forEach(select => {
        // Store initial value on focus or click
        select.addEventListener('focus', function() {
            this.dataset.previousValue = this.value;
        });
        select.addEventListener('change', function(e) {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.proLockedOption === 'true') {
                e.preventDefault();
                // Revert to previous value
                const prevValue = this.dataset.previousValue;
                if (prevValue !== undefined) {
                    this.value = prevValue;
                } else {
                    // Fallback to first non-locked option if no prev value recorded
                    for (let i = 0; i < this.options.length; i++) {
                        if (this.options[i].dataset.proLockedOption !== 'true') {
                            this.value = this.options[i].value;
                            break;
                        }
                    }
                }
                
                // Trigger the upgrade modal
                const event = new CustomEvent('lgx:require-pro-upgrade', { 
                    detail: { trigger: this },
                    bubbles: true
                });
                document.dispatchEvent(event);
            } else {
                // Update previous value on valid change
                this.dataset.previousValue = this.value;
            }
        });
    });
}

/**
 * Height/Width merge: Type-follows-Value for the Logo Image Height/Width
 * compound fields. Mirrors the title auto-generation pattern elsewhere in
 * this file (isAutoGenerating, ~line 397-536): a per-instance flag,
 * initialised from "is the Value field currently at this Type's own
 * default", permanently disabled the moment a genuine input event (not a
 * programmatic write) touches the Value field. Reads its Type->default
 * lookup from config.php via lgxShowcaseBuilderDefaults (localized in
 * builder-wrapper.php) rather than hardcoding it a second time.
 */
function initLogoDimensionTypeFollowsValue() {
    if (typeof lgxShowcaseBuilderDefaults === 'undefined' || !lgxShowcaseBuilderDefaults.fields) {
        return;
    }

    Object.keys(lgxShowcaseBuilderDefaults.fields).forEach(function (selectId) {
        const meta = lgxShowcaseBuilderDefaults.fields[selectId];
        if (!meta.valueDefaultsByOption || !meta.pairedValueFieldId) {
            return;
        }

        const selectEl = document.getElementById(selectId);
        const valueEl = document.getElementById(meta.pairedValueFieldId);
        if (!selectEl || !valueEl) {
            return;
        }

        // Blank/at-current-Type's-default is the only signal -- same shape
        // as isAutoGenerating's "blank title" heuristic.
        let isValueFollowingType = (valueEl.value === (meta.valueDefaultsByOption[selectEl.value] || ''));

        valueEl.addEventListener('input', function (e) {
            // e.isTrusted distinguishes a real keystroke from the
            // synthetic dispatchEvent() below (needed so the live-preview
            // form-level 'input' listener sees this change) -- without this
            // check, the auto-fill below would immediately freeze itself on
            // its own dispatch, working for exactly one Type switch and
            // never again.
            if (e.isTrusted) {
                isValueFollowingType = false; // real keystroke freezes it permanently
            }
        });

        selectEl.addEventListener('change', function () {
            if (!isValueFollowingType) {
                return;
            }
            const next = meta.valueDefaultsByOption[selectEl.value];
            if (next === undefined) {
                return;
            }
            valueEl.value = next;
            valueEl.dispatchEvent(new Event('input', { bubbles: true })); // live preview picks it up
        });
    });
}

/**
 * Handle the Pro Upgrade Modal lifecycle and interactions.
 * Listens for lgx:require-pro-upgrade custom event to trigger.
 */
function initProUpgradeModal() {
    const modal = document.getElementById('lgx-pro-modal');
    const overlay = document.getElementById('lgx-pro-modal-overlay');
    const closeBtn = document.getElementById('lgx-pro-modal-close');
    const maybeLaterBtn = document.getElementById('lgx-pro-modal-maybe-later');
    const featureNameEl = document.getElementById('lgx-pro-modal-feature-name');
    
    if (!modal || !overlay || !closeBtn) return;

    // Move to body to ensure fixed positioning works correctly
    if (modal.parentNode !== document.body) document.body.appendChild(modal);
    if (overlay.parentNode !== document.body) document.body.appendChild(overlay);

    let lastTrigger = null;

    function openModal(triggerElement) {
        lastTrigger = triggerElement;
        
        // Find feature name for context
        // T-226b (B189): the default name is translated; the check below compares with the same value.
        const defaultFeatureName = lgxText('proFeature', 'Pro Feature');
        let featureName = defaultFeatureName;
        if (triggerElement) {
            // First try to find standard row label
            const rowLabel = triggerElement.closest('.lgx-field--row') || triggerElement.closest('tr');
            if (rowLabel) {
                const labelEl = rowLabel.querySelector('label');
                if (labelEl) featureName = labelEl.textContent;
            }
            // Fallback to group label or ID
            if (featureName === defaultFeatureName && triggerElement.id) {
                featureName = triggerElement.id.replace('lgx_', '').replace(/_/g, ' ');
                // Capitalize
                featureName = featureName.charAt(0).toUpperCase() + featureName.slice(1);
            }
        }
        
        if (featureNameEl) {
            featureNameEl.textContent = featureName;
        }

        modal.classList.add('lgx-gallery-open');
        overlay.classList.add('lgx-gallery-open');
        closeBtn.focus();
    }

    function closeModal() {
        modal.classList.remove('lgx-gallery-open');
        overlay.classList.remove('lgx-gallery-open');
        if (lastTrigger) {
            lastTrigger.focus();
        }
    }

    document.addEventListener('lgx:require-pro-upgrade', function(e) {
        openModal(e.detail.trigger);
    });

    closeBtn.addEventListener('click', closeModal);
    if (maybeLaterBtn) maybeLaterBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('lgx-gallery-open')) {
            closeModal();
        }
    });
}

/**
 * Task 014/Commit 1 — Usage modal controller.
 *
 * Informational cards only — no selection state, nothing persisted (see the
 * 014 Phase 1 report §B: the old "Use with:" select this modal replaces had
 * no name attribute and was never submitted either). Reuses the Layout
 * Gallery / Pro Upgrade modal's open/close/backdrop/Escape mechanics, but
 * adds a real Tab-cycling focus trap, which neither of those two implement —
 * Task 013's accessibility bar requires one here specifically, and this is
 * the first modal in this file that needs it (the other two only ever
 * contain a close button plus, for the Pro modal, one link and one button —
 * short enough that Tab naturally stays inside in practice, but this modal
 * has multiple inputs/buttons/links where an untrapped Tab could escape to
 * the page behind the overlay).
 */
function initUsageModal() {
    const trigger     = document.getElementById('lgx-header-usage-trigger');
    const modal       = document.getElementById('lgx-usage-modal');
    const overlay     = document.getElementById('lgx-usage-modal-overlay');
    const closeBtn    = document.getElementById('lgx-usage-modal-close');
    const copyStatus  = document.getElementById('lgx-usage-modal-copy-status');

    if (!trigger || !modal || !overlay || !closeBtn) return;

    // Move to body so position:fixed works regardless of parent overflow —
    // same precedent as the Layout Gallery / Pro Upgrade modals above.
    if (modal.parentNode !== document.body) document.body.appendChild(modal);
    if (overlay.parentNode !== document.body) document.body.appendChild(overlay);

    const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    function getFocusable() {
        return Array.from(modal.querySelectorAll(FOCUSABLE_SELECTOR)).filter(function (el) {
            return el.offsetParent !== null;
        });
    }

    function trapKeydown(e) {
        if (e.key !== 'Tab') return;
        const focusable = getFocusable();
        if (!focusable.length) return;
        const first = focusable[0];
        const last  = focusable[focusable.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    }

    function openModal() {
        modal.classList.add('lgx-gallery-open');
        overlay.classList.add('lgx-gallery-open');
        trigger.setAttribute('aria-expanded', 'true');
        closeBtn.focus();
        document.addEventListener('keydown', trapKeydown);
    }

    function closeModal() {
        modal.classList.remove('lgx-gallery-open');
        overlay.classList.remove('lgx-gallery-open');
        trigger.setAttribute('aria-expanded', 'false');
        document.removeEventListener('keydown', trapKeydown);
        trigger.focus();
    }

    trigger.addEventListener('click', function (e) {
        e.preventDefault();
        openModal();
    });
    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('lgx-gallery-open')) {
            closeModal();
        }
    });

    // Copy buttons — same offscreen-textarea + execCommand('copy') technique
    // already used by initHeaderShortcodeCopy() above and by the General
    // tab's own Usage card: navigator.clipboard requires a secure context
    // and would silently fail on a plain-HTTP install (this dev site
    // included). Disabled buttons (unsaved-showcase state, see
    // builder-wrapper.php's $lgx_is_empty_meta gate) never receive a click,
    // so no extra guard is needed here beyond the native disabled attribute.
    const copyButtons = modal.querySelectorAll('.lgx-usage-modal-copy-btn');
    copyButtons.forEach(function (btn) {
        // Task 021 (revised): icon-only equivalent of the header pill's
        // "Copied!" label-swap — there's no text label here to swap, so the
        // copy icon itself swaps to a checkmark instead (dashicons-yes-alt,
        // already this codebase's established "success" icon — see the
        // Dashboard onboarding-step-complete treatment). One timer per
        // button (closure-scoped here, not shared) so copying two different
        // fields in quick succession doesn't cancel either button's flash.
        const iconEl = btn.querySelector('.lgx-embed-panel__copy-icon');
        const defaultIconClass = iconEl ? iconEl.className : '';
        let resetTimer = null;

        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-copy-target');
            const field = targetId ? document.getElementById(targetId) : null;
            if (!field || !field.value) return;

            const temp = document.createElement('textarea');
            temp.value = field.value;
            temp.style.position = 'fixed';
            temp.style.top = '0';
            temp.style.left = '0';
            temp.style.opacity = '0';
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);

            if (copyStatus) {
                // Clear first so a second copy of the same field still fires
                // a DOM mutation and re-announces via aria-live.
                copyStatus.textContent = '';
                window.setTimeout(function () {
                    copyStatus.textContent = lgxText('copiedToClipboard', 'Copied to clipboard.');
                }, 10);
            }

            // Task 021: visible feedback for sighted users, on top of the
            // aria-live region above (which stays — that's for screen
            // readers, not a duplicate of this). Per-button message via
            // data-copy-success-msg, set in builder-wrapper.php per card.
            lgxNotifySuccess(btn.getAttribute('data-copy-success-msg') || lgxText('copiedToClipboard', 'Copied to clipboard'));

            if (iconEl) {
                iconEl.className = defaultIconClass.replace('dashicons-admin-page', 'dashicons-yes-alt');
                btn.classList.add('is-copied');
                if (resetTimer) clearTimeout(resetTimer);
                resetTimer = setTimeout(function () {
                    iconEl.className = defaultIconClass;
                    btn.classList.remove('is-copied');
                }, 1500);
            }
        });
    });
}

/**
 * Handle dynamic notes for Ticker Mode in the Carousel tab.
 */
function initTickerContextNote() {
    const tickerToggle = document.getElementById('lgx_carousel_ticker_en');
    const speedInput = document.getElementById('lgx_carousel_transition_speed');
    if (!tickerToggle || !speedInput) return;

    const note = document.createElement('div');
    note.className = 'lgx-ticker-mode-note';
    note.innerHTML =
        '<span class="dashicons dashicons-info lgx-builder-help-icon" ' +
        'style="color:#007cba;margin-top:-3px;margin-right:4px;' +
        'font-size:16px;width:16px;height:16px;" aria-hidden="true"></span>' +
        ' Controls scroll duration in Ticker Mode &mdash; ' +
        '2000-6000 all scroll smoothly in testing; lower values scroll faster, ' +
        'higher values scroll slower.';
    note.style.marginTop = '8px';
    note.style.color = '#646970';
    note.style.fontSize = '12px';
    note.style.fontStyle = 'italic';
    note.style.display = 'none';
    note.style.alignItems = 'center';

    const wrapper = speedInput.closest('.lgx-field__control') || speedInput.parentNode;
    if (wrapper) wrapper.appendChild(note);

    function update() {
        note.style.display = tickerToggle.checked ? 'flex' : 'none';
    }

    tickerToggle.addEventListener('change', update);
    update();
}

/**
 * Dependency Field Locking Engine (task011g, Phase 1).
 */
const FIELD_DEPENDENCIES = [
    // ---- Phase 1 entries ----
    // B69: the lgx_brand_name_en / lgx_brand_desc_en entries that used to
    // grey-lock their 10 Style-tab dependents were removed here (Owner
    // decision, Task 68 §1.3 — safety confirmed by
    // docs/release/task68/plan/008-brand-toggle-dependency-removal-check.md).
    // Those 10 Style fields are now independently editable at all times.
    // 2026-09-06 (Owner decision): the lgx_company_url_en entry that grey-locked
    // lgx_target_type and lgx_nofollow_en was removed here. Both are now
    // independently editable at all times, and each carries its own `desc` in
    // config.php instead of relying on the dependency note for explanation.
    // Same shape as the B69/Task 68 §1.3 removal of the brand-toggle entries
    // directly above.
    // ---- Phase 2 NEW: Layout / Carousel — Mode A ----
    {
        parent: 'lgx_carousel_autoplay_en',
        children: ['lgx_carousel_autoplay_delay', 'lgx_carousel_pause_mouse_enter_en'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_carousel_lazy_load_en',
        children: ['lgx_carousel_lazy_spinner_color'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_carousel_nav_en',
        children: [
            'lgx_carousel_nav_hover_en',
            'lgx_carousel_nav_position',
            'lgx_carousel_nav_ver_pos_bottom',
            'lgx_carousel_nav_ver_pos_left_right',
            'lgx_carousel_nav_color',
            'lgx_carousel_nav_color_hover',
            'lgx_carousel_nav_bg_color',
            'lgx_carousel_nav_bg_color_hover',
            'lgx_carousel_nav_btn_font_size',
            'lgx_carousel_nav_btn_width',
            'lgx_carousel_nav_btn_height',
            'lgx_carousel_nav_btn_margin',
            'lgx_carousel_nav_btn_padding',
            'lgx_carousel_nav_border_en',
            'lgx_carousel_nav_border_color',
            'lgx_carousel_nav_border_color_hover',
            'lgx_carousel_nav_border_width',
            'lgx_carousel_nav_border_style',
            'lgx_carousel_nav_btn_type',
            'lgx_carousel_nav_icon',
            'lgx_carousel_nav_btn_text_prev',
            'lgx_carousel_nav_btn_text_next',
            'lgx_carousel_nav_mobile_en',
        ],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_carousel_nav_border_en',
        children: [
            'lgx_carousel_nav_border_color',
            'lgx_carousel_nav_border_color_hover',
            'lgx_carousel_nav_border_width',
            'lgx_carousel_nav_border_style',
        ],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_carousel_pagination_en',
        children: [
            'lgx_carousel_pagination_color',
            'lgx_carousel_pagination_color_active',
            'lgx_carousel_dynamic_bullets_en',
            'lgx_carousel_dynamic_bullets_no',
            'lgx_carousel_pagi_mobile_en',
        ],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_carousel_dynamic_bullets_en',
        children: ['lgx_carousel_dynamic_bullets_no'],
        mode: 'grey-when-parent-off',
    },
    // ---- Phase 2 NEW: Layout / Carousel — Mode B (Ticker Mode migration) ---
    {
        parent: 'lgx_carousel_ticker_en',
        children: ['lgx_carousel_autoplay_delay'],
        mode: 'force-value-when-parent-on',
        forcedValue: '0',
    },
    {
        parent: 'lgx_carousel_ticker_en',
        children: ['lgx_carousel_infinite_en'],
        mode: 'force-checked-when-parent-on',
    },
    // ---- Task 014 NEW: Ticker Pause Control only matters while Ticker is on ----
    {
        parent: 'lgx_carousel_ticker_en',
        children: ['lgx_carousel_ticker_pause_control_en'],
        mode: 'grey-when-parent-off',
    },
    // ---- Phase 3 NEW: Style tab — Mode A ----
    {
        parent: 'lgx_img_border_color_en',
        children: ['lgx_img_border_color', 'lgx_img_border_color_hover', 'lgx_img_border_width', 'lgx_img_border_radius'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_border_color_en',
        children: ['lgx_item_border_color', 'lgx_item_border_color_hover', 'lgx_item_border_width', 'lgx_item_border_radius'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_item_bg_color_en',
        children: ['lgx_item_bg_color', 'lgx_item_bg_color_hover'],
        mode: 'grey-when-parent-off',
    },
    // ---- Phase 4 NEW: Section tab — Mode A ----
    {
        parent: 'lgx_section_bg_img_en',
        children: ['lgx_section_bg_img', 'lgx_section_bg_img_attachment', 'lgx_section_bg_img_size'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_section_bg_color_en',
        children: ['lgx_section_bg_color'],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_preloader_en',
        children: ['lgx_preloader_icon', 'lgx_preloader_bg_color'],
        mode: 'grey-when-parent-off',
    },
    // ---- Phase 5 NEW: Advanced tab — Mode A ----
    {
        parent: 'lgx_tooltip_en',
        children: [
            'lgx_tooltip_content_type',
            'lgx_tooltip_position',
            'lgx_tooltip_min_width',
            'lgx_tooltip_max_width',
            'lgx_tooltip_anim',
            'lgx_tooltip_anim_duration',
            'lgx_tooltip_anim_delay',
            'lgx_tooltip_trigger_type',
            'lgx_tooltip_distance',
            'lgx_tooltip_text_color',
            'lgx_tooltip_padding',
            'lgx_tooltip_bg_color',
            'lgx_tooltip_border_color',
            'lgx_tooltip_border_width',
            'lgx_tooltip_border_radius',
            'lgx_tooltip_min_intersection',
            'lgx_tooltip_timer',
            'lgx_tooltip_arrow_en',
            'lgx_tooltip_arrow_border_color',
            'lgx_tooltip_arrow_bg_color',
        ],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_tooltip_arrow_en',
        children: [
            'lgx_tooltip_arrow_border_color',
            'lgx_tooltip_arrow_bg_color',
        ],
        mode: 'grey-when-parent-off',
    },
    {
        parent: 'lgx_header_en',
        children: [
            'lgx_header_align',
            'lgx_header_title',
            'lgx_header_title_font_size',
            'lgx_header_title_color',
            'lgx_header_title_font_weight',
            'lgx_header_title_bottom_margin',
            'lgx_header_subtitle',
            'lgx_header_subtitle_font_size',
            'lgx_header_subtitle_color',
            'lgx_header_subtitle_font_weight',
            'lgx_header_subtitle_bottom_margin',
        ],
        mode: 'grey-when-parent-off',
    },
];


// T-226b (B189): translated through lgxShowcaseBuilderDefaults.depLockNote; the English text is the fallback.
const DEP_LOCK_NOTE_TEMPLATE = lgxText('depLockNote', 'Enable <strong>%s</strong> to use this setting.');
const DEP_NOTE_CLASS = 'lgx-dep-lock-note';
const DEP_LOCK_CLASS = 'lgx-dep-locked-field';

/**
 * T-226c: escapes text for use inside HTML (& < > " ').
 *
 * @param {string} text Plain text.
 * @return {string}
 */
function lgxEscapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function getParentLabel(parentId) {
    const el = document.getElementById(parentId);
    if (!el) return '';
    const fieldRow = el.closest('.lgx-field');
    if (!fieldRow) return '';
    const label = fieldRow.querySelector('.lgx-field__label label, .lgx-field__title');
    return label ? label.textContent.trim() : '';
}

function lockNoteIdForChild(parentId, childId) {
    return 'lgx-dep-note-' + parentId + '-' + childId;
}

function getOrCreateLockNoteForChild(depEntry, childId) {
    const noteId = lockNoteIdForChild(depEntry.parent, childId);
    const child = document.getElementById(childId);
    if (!child) return null;
    const fieldRow = child.closest('.lgx-field');
    if (!fieldRow) return null;
    const control = fieldRow.querySelector('.lgx-field__control');
    if (!control) return null;

    var note = control.querySelector('#' + noteId);
    if (note) return note;

    note = document.createElement('div');
    note.id = noteId;
    note.className = DEP_NOTE_CLASS;
    note.hidden = true;
    note.innerHTML =
        '<span class="dashicons dashicons-info-outline" aria-hidden="true"></span>' +
        DEP_LOCK_NOTE_TEMPLATE.replace('%s', lgxEscapeHtml(getParentLabel(depEntry.parent)));
    control.appendChild(note);
    return note;
}

function lockFieldRow(fieldRow, locked) {
    if (!fieldRow) return;
    fieldRow.classList.toggle(DEP_LOCK_CLASS, locked);

    const inputs = fieldRow.querySelectorAll('input, select, textarea, button');
    inputs.forEach(function (input) {
        if (locked) {
            if (input.tabIndex !== -1) {
                input.dataset.depOriginalTabIndex = input.tabIndex;
            }
            input.tabIndex = -1;
            if (input.tagName !== 'BUTTON') {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.setAttribute('aria-disabled', 'true');
                } else {
                    input.readOnly = true;
                }
            }
        } else {
            delete input.dataset.depOriginalTabIndex;
            input.removeAttribute('tabindex');
            if (input.tagName !== 'BUTTON') {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.removeAttribute('aria-disabled');
                } else {
                    input.removeAttribute('readonly');
                }
            }
        }
    });
}

function lockNotesFor(depEntry, locked) {
    depEntry.children.forEach(function (childId) {
        const note = getOrCreateLockNoteForChild(depEntry, childId);
        if (note) note.hidden = !locked;
    });
}

function applyLockToChildrenOf(depEntry, locked) {
    depEntry.children.forEach(function (childId) {
        const child = document.getElementById(childId);
        if (!child) return;
        lockFieldRow(child.closest('.lgx-field'), locked);
    });

    lockNotesFor(depEntry, locked);

    const nestedEntries = FIELD_DEPENDENCIES.filter(function (d) {
        return depEntry.children.includes(d.parent);
    });

    nestedEntries.forEach(function (nested) {
        if (locked) {
            applyLockToChildrenOf(nested, true);
        } else {
            updateDependencyLock(nested);
        }
    });
}

function forceNoteIdForChild(parentId, childId) {
    return 'lgx-dep-force-' + parentId + '-' + childId;
}

function getOrCreateForceNote(depEntry, childId) {
    const noteId = forceNoteIdForChild(depEntry.parent, childId);
    const child = document.getElementById(childId);
    if (!child) return null;
    const fieldRow = child.closest('.lgx-field');
    if (!fieldRow) return null;
    const control = fieldRow.querySelector('.lgx-field__control');
    if (!control) return null;

    let note = control.querySelector('#' + noteId);
    if (note) return note;
    note = document.createElement('div');
    note.id = noteId;
    note.className = DEP_NOTE_CLASS + ' lgx-dep-lock-note--required';
    note.hidden = true;
    note.innerHTML =
        '<span class="dashicons dashicons-info-outline" aria-hidden="true"></span> ' +
        'Required for <strong>' + lgxEscapeHtml(getParentLabel(depEntry.parent)) + '</strong>.';
    control.appendChild(note);
    return note;
}

function forceChildValue(child, rawValue) {
    if (!child) return;
    child.value = rawValue;
    child.readOnly = true;
    child.setAttribute('aria-disabled', 'true');
    child.dataset.depForceActive = 'true';
}

function restoreChildValue(child) {
    if (!child) return;
    if (child.dataset.depForceActive !== 'true') return;
    delete child.dataset.depForceActive;
    if (child.dataset.depOriginalValue !== undefined) {
        child.value = child.dataset.depOriginalValue;
        delete child.dataset.depOriginalValue;
    }
    child.readOnly = false;
    child.removeAttribute('aria-disabled');
}

function applyForceValue(depEntry, isParentOn) {
    depEntry.children.forEach(function (childId) {
        const child = document.getElementById(childId);
        if (!child) return;
        const note = getOrCreateForceNote(depEntry, childId);

        if (isParentOn) {
            if (child.dataset.depOriginalValue === undefined) {
                child.dataset.depOriginalValue = child.value;
            }
            forceChildValue(child, depEntry.forcedValue);
            lockFieldRow(child.closest('.lgx-field'), true);
            child.closest('.lgx-field').classList.add('lgx-dep-locked-field--force');
            if (note) note.hidden = false;
        } else {
            restoreChildValue(child);
            lockFieldRow(child.closest('.lgx-field'), false);
            child.closest('.lgx-field').classList.remove('lgx-dep-locked-field--force');
            if (note) note.hidden = true;
        }
    });
}

function forceChildChecked(child) {
    if (!child) return;
    child.checked = true;
    child.setAttribute('aria-checked', 'true');
    child.setAttribute('aria-disabled', 'true');
    child.tabIndex = -1;
    child.dataset.depForceActive = 'true';
}

function restoreChildChecked(child) {
    if (!child) return;
    if (child.dataset.depForceActive !== 'true') return;
    delete child.dataset.depForceActive;
    if (child.dataset.depOriginalChecked !== undefined) {
        child.checked = child.dataset.depOriginalChecked === 'true';
        delete child.dataset.depOriginalChecked;
    }
    child.removeAttribute('aria-checked');
    child.removeAttribute('aria-disabled');
    child.removeAttribute('tabindex');
}

function applyForceChecked(depEntry, isParentOn) {
    depEntry.children.forEach(function (childId) {
        const child = document.getElementById(childId);
        if (!child) return;
        const note = getOrCreateForceNote(depEntry, childId);

        if (isParentOn) {
            if (child.dataset.depOriginalChecked === undefined) {
                child.dataset.depOriginalChecked = child.checked ? 'true' : 'false';
            }
            forceChildChecked(child);
            lockFieldRow(child.closest('.lgx-field'), true);
            child.closest('.lgx-field').classList.add('lgx-dep-locked-field--force');
            if (note) note.hidden = false;
        } else {
            restoreChildChecked(child);
            lockFieldRow(child.closest('.lgx-field'), false);
            child.closest('.lgx-field').classList.remove('lgx-dep-locked-field--force');
            if (note) note.hidden = true;
        }
    });
}

function updateDependencyLock(depEntry) {
    if (!depEntry) return;

    const parentEl = document.getElementById(depEntry.parent);
    if (!parentEl) return;

    let isParentOn;
    if (parentEl.type === 'checkbox') {
        isParentOn = parentEl.checked;
    } else if (parentEl.type === 'radio' || parentEl.tagName === 'SELECT') {
        isParentOn = parentEl.value === 'yes';
    } else {
        return;
    }

    if (depEntry.mode === 'grey-when-parent-off') {
        applyLockToChildrenOf(depEntry, !isParentOn);
        return;
    }
    if (depEntry.mode === 'force-value-when-parent-on') {
        applyForceValue(depEntry, isParentOn);
        return;
    }
    if (depEntry.mode === 'force-checked-when-parent-on') {
        applyForceChecked(depEntry, isParentOn);
        return;
    }
}

function updateAllDependencies() {
    FIELD_DEPENDENCIES.forEach(updateDependencyLock);
}

function depEntry_LoopRecaptureClick(e) {
    const toggle = e.target.closest('input[type="checkbox"]');
    if (!toggle) return;
    const childId = toggle.id;
    const matches = FIELD_DEPENDENCIES.filter(d =>
        childId && d.children.includes(childId) &&
        d.mode === 'force-checked-when-parent-on');
    matches.forEach(d => {
        const parentEl = document.getElementById(d.parent);
        if (parentEl && parentEl.checked) {
            forceChildChecked(toggle);
        }
    });
}

function initFieldDependencyEngine() {
    FIELD_DEPENDENCIES.forEach(function (depEntry) {
        if (depEntry.mode === 'grey-when-parent-off') {
            depEntry.children.forEach(function (childId) {
                getOrCreateLockNoteForChild(depEntry, childId);
            });
        }
    });

    FIELD_DEPENDENCIES.forEach(function (depEntry) {
        if (depEntry.mode === 'force-value-when-parent-on' ||
            depEntry.mode === 'force-checked-when-parent-on') {
            depEntry.children.forEach(function (childId) {
                getOrCreateForceNote(depEntry, childId);
            });
        }
    });

    updateAllDependencies();

    const builderContainer = document.querySelector('.lgx-builder') || document;

    builderContainer.addEventListener('change', function (e) {
        if (!e.target.id) return;
        const matchingEntries = FIELD_DEPENDENCIES.filter(function (d) { return d.parent === e.target.id; });
        matchingEntries.forEach(function(depEntry) {
            updateDependencyLock(depEntry);
        });
    });

    builderContainer.addEventListener('click', function (e) {
        const lockedRow = e.target.closest('.lgx-dep-locked-field');
        if (!lockedRow) return;
        if (!e.target.closest('.lgx-field__control')) return;
        
        if (lockedRow.classList.contains('lgx-dep-locked-field--force')) {
            depEntry_LoopRecaptureClick(e);
            return;
        }

        e.preventDefault();
        e.stopPropagation();
    }, true);

    builderContainer.addEventListener('keydown', function (e) {
        const lockedRow = e.target.closest('.lgx-dep-locked-field');
        if (!lockedRow) return;
        if (e.key === 'Tab') return;
        if (!e.target.closest('.lgx-field__control')) return;
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);
}

