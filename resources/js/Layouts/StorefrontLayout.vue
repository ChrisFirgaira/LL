<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();

const announcement = computed(() => page.props.announcement ?? null);
const navigation = computed(() => page.props.navigation ?? {});
const cartSummary = computed(() => page.props.cartSummary ?? { itemCount: 0, total: null });
const cartPreview = computed(() => page.props.cartPreview ?? { lines: [], itemCount: 0, total: null, subTotal: null });
const flash = computed(() => page.props.flash ?? {});
const contact = computed(() => page.props.contact ?? {});
const auth = computed(() => page.props.auth ?? {});
const featuredCollections = computed(() => (navigation.value.collections ?? []).slice(0, 6));
const stockLocatorUrl = computed(() => navigation.value.stockLocatorUrl ?? null);
const brandName = 'POP ATTACK';
const headerContactEmail = computed(() => contact.value.onlineEmail || contact.value.email || null);
const isLocationsPage = computed(() => (page.url ?? '').startsWith('/locations'));
const isShopPage = computed(() => (page.url ?? '').startsWith('/shop'));
const initialDarkMode = typeof window !== 'undefined' && window.localStorage.getItem('storefront-theme') === 'dark';
const mobileMenuOpen = ref(false);
const globalSearch = ref('');
const subscriptionEmail = ref('');
const searchSuggestions = ref([]);
const searchLoading = ref(false);
const showSearchDropdown = ref(false);
const activeSuggestionIndex = ref(-1);
const darkMode = ref(initialDarkMode);
const miniCartOpen = ref(false);
const miniCartBusy = ref(false);
const cartButtonAnchor = ref(null);
const miniCartStyle = ref({
    top: '7.15rem',
    right: '1.25rem',
});

let searchDebounceTimer = null;
let searchBlurTimer = null;
let searchAbortController = null;
let miniCartRepositionHandler = null;

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

const applyThemeMode = (isDark) => {
    if (typeof document === 'undefined') {
        return;
    }

    document.body.dataset.theme = isDark ? 'dark' : 'light';
};

if (typeof document !== 'undefined') {
    applyThemeMode(initialDarkMode);
}

onMounted(() => {
    applyThemeMode(darkMode.value);
});

watch(darkMode, (value) => {
    if (typeof window === 'undefined') {
        return;
    }

    applyThemeMode(value);
    window.localStorage.setItem('storefront-theme', value ? 'dark' : 'light');
});

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
};

const updateMiniCartAnchor = () => {
    if (typeof window === 'undefined') {
        return;
    }

    const fallbackTop = 114;
    const fallbackRight = 20;
    const anchorRect = cartButtonAnchor.value?.getBoundingClientRect?.();

    if (!anchorRect || anchorRect.width === 0) {
        miniCartStyle.value = {
            top: `${fallbackTop}px`,
            right: `${fallbackRight}px`,
        };
        return;
    }

    const top = Math.max(anchorRect.bottom + 10, 70);
    const right = Math.max(window.innerWidth - anchorRect.right, 8);

    miniCartStyle.value = {
        top: `${top}px`,
        right: `${right}px`,
    };
};

watch(
    () => flash.value.success,
    (message) => {
        if (typeof message === 'string' && message.toLowerCase().includes('item added to cart')) {
            miniCartOpen.value = true;
        }
    },
);

watch(miniCartOpen, (isOpen) => {
    if (isOpen) {
        requestAnimationFrame(() => updateMiniCartAnchor());
    }
});

const closeMiniCart = () => {
    miniCartOpen.value = false;
};

const updateMiniCartLine = (line, nextQuantity) => {
    const quantity = Math.max(1, Math.min(20, nextQuantity));
    if (!line?.id || quantity === line.quantity || miniCartBusy.value) {
        return;
    }

    miniCartBusy.value = true;
    router.patch(`/cart/lines/${line.id}`, { quantity }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            miniCartOpen.value = true;
        },
        onFinish: () => {
            miniCartBusy.value = false;
        },
    });
};

const removeMiniCartLine = (lineId) => {
    if (!lineId || miniCartBusy.value) {
        return;
    }

    miniCartBusy.value = true;
    router.delete(`/cart/lines/${lineId}`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            miniCartOpen.value = true;
        },
        onFinish: () => {
            miniCartBusy.value = false;
        },
    });
};

const closeSearchDropdown = () => {
    showSearchDropdown.value = false;
    activeSuggestionIndex.value = -1;
};

const fetchSearchSuggestions = async (query) => {
    if (import.meta.env.SSR || query.length < 2) {
        searchSuggestions.value = [];
        closeSearchDropdown();
        return;
    }

    if (searchAbortController) {
        searchAbortController.abort();
    }

    searchAbortController = new AbortController();
    searchLoading.value = true;

    try {
        const response = await fetch(`/search/suggest?q=${encodeURIComponent(query)}`, {
            signal: searchAbortController.signal,
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Search preview failed');
        }

        const payload = await response.json();
        searchSuggestions.value = payload?.data ?? [];
        showSearchDropdown.value = searchSuggestions.value.length > 0;
        activeSuggestionIndex.value = -1;
    } catch (error) {
        if (error.name !== 'AbortError') {
            searchSuggestions.value = [];
            closeSearchDropdown();
        }
    } finally {
        searchLoading.value = false;
    }
};

watch(globalSearch, (value) => {
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }

    const query = value.trim();
    if (query.length < 2) {
        searchSuggestions.value = [];
        closeSearchDropdown();
        return;
    }

    searchDebounceTimer = setTimeout(() => {
        fetchSearchSuggestions(query);
    }, 100);
});

onBeforeUnmount(() => {
    if (searchDebounceTimer) {
        clearTimeout(searchDebounceTimer);
    }

    if (searchBlurTimer) {
        clearTimeout(searchBlurTimer);
    }

    if (searchAbortController) {
        searchAbortController.abort();
    }

    if (miniCartRepositionHandler && typeof window !== 'undefined') {
        window.removeEventListener('resize', miniCartRepositionHandler);
        window.removeEventListener('scroll', miniCartRepositionHandler, true);
        miniCartRepositionHandler = null;
    }
});

const visitSuggestedProduct = (url) => {
    closeSearchDropdown();
    closeMobileMenu();
    router.visit(url);
};

const onSearchInputFocus = () => {
    if (searchSuggestions.value.length > 0) {
        showSearchDropdown.value = true;
    }
};

const onSearchInputBlur = () => {
    searchBlurTimer = setTimeout(() => {
        closeSearchDropdown();
    }, 140);
};

const onSearchInputKeydown = (event) => {
    if (!searchSuggestions.value.length) {
        return;
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        showSearchDropdown.value = true;
        activeSuggestionIndex.value = Math.min(activeSuggestionIndex.value + 1, searchSuggestions.value.length - 1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeSuggestionIndex.value = Math.max(activeSuggestionIndex.value - 1, 0);
    } else if (event.key === 'Escape') {
        closeSearchDropdown();
    } else if (event.key === 'Enter' && activeSuggestionIndex.value >= 0) {
        event.preventDefault();
        visitSuggestedProduct(searchSuggestions.value[activeSuggestionIndex.value].url);
    }
};

const runGlobalSearch = () => {
    if (activeSuggestionIndex.value >= 0 && searchSuggestions.value[activeSuggestionIndex.value]) {
        visitSuggestedProduct(searchSuggestions.value[activeSuggestionIndex.value].url);
        return;
    }

    closeSearchDropdown();
    router.get(navigation.value.shopUrl ?? '/shop', {
        search: globalSearch.value || undefined,
    });
    closeMobileMenu();
};

const subscribe = () => {
    const inbox = contact.value.onlineEmail || contact.value.email;
    if (!inbox || !subscriptionEmail.value) {
        return;
    }

    const subject = encodeURIComponent('Newsletter subscription request');
    const body = encodeURIComponent(`Please subscribe this email address: ${subscriptionEmail.value}`);
    window.location.href = `mailto:${inbox}?subject=${subject}&body=${body}`;
    subscriptionEmail.value = '';
};

onMounted(() => {
    miniCartRepositionHandler = () => updateMiniCartAnchor();
    window.addEventListener('resize', miniCartRepositionHandler, { passive: true });
    window.addEventListener('scroll', miniCartRepositionHandler, true);
    updateMiniCartAnchor();
});
</script>

<template>
    <div class="relative min-h-screen">
        <div v-if="announcement || contact.phone || headerContactEmail" class="border-b border-slate-200 bg-slate-50 text-sm text-slate-600">
            <div class="header-shell flex flex-col gap-2 py-2 md:flex-row md:items-center md:justify-between">
                <p class="text-sm text-slate-700">{{ announcement }}</p>
                <div class="flex flex-wrap items-center gap-4 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                    <span v-if="contact.phone">{{ contact.phone }}</span>
                    <span v-if="headerContactEmail">{{ headerContactEmail }}</span>
                </div>
            </div>
        </div>

        <header :class="isLocationsPage ? 'sticky top-0 z-30 border-b border-transparent bg-white shadow-[0_8px_30px_rgba(15,23,42,0.08)]' : 'sticky top-0 z-30 border-b border-slate-200 bg-white shadow-[0_8px_30px_rgba(15,23,42,0.08)]'">
            <div class="header-shell py-4">
                <div class="grid gap-4 lg:grid-cols-[auto_1fr_auto] lg:items-center">
                    <div class="flex items-center justify-between gap-4">
                        <Link href="/" class="flex items-center gap-3" @click="closeMobileMenu">
                            <img :src="'/images/popattack-logo.png'" alt="Pop Attack logo" class="h-[5.25rem] w-auto shrink-0 object-contain">
                            <span>
                                <span class="block text-xl font-black uppercase tracking-[0.16em] text-slate-950">{{ brandName }}</span>
                                <span class="block text-xs font-medium uppercase tracking-[0.14em] text-slate-500">Trading Cards and Collectibles</span>
                            </span>
                        </Link>
                        <button
                            type="button"
                            class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-950 shadow-sm lg:hidden"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            {{ mobileMenuOpen ? 'Close' : 'Menu' }}
                        </button>
                    </div>

                    <form class="relative hidden lg:block" @submit.prevent="runGlobalSearch">
                        <label class="sr-only" for="global-search">Search products</label>
                        <div class="flex items-center rounded-full border border-slate-300 bg-white px-2 py-2 shadow-sm">
                            <input
                                id="global-search"
                                v-model="globalSearch"
                                type="search"
                                placeholder="Search products, categories, sets..."
                                class="w-full bg-transparent px-4 text-sm text-slate-900 outline-none placeholder:text-slate-400"
                                @focus="onSearchInputFocus"
                                @blur="onSearchInputBlur"
                                @keydown="onSearchInputKeydown"
                            >
                            <button type="submit" class="primary-button !px-4 !py-2">Search</button>
                        </div>
                        <div v-if="showSearchDropdown || searchLoading" class="absolute left-1/2 top-[calc(100%+0.55rem)] z-50 w-[min(54rem,96vw)] -translate-x-1/2 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.2)]">
                            <div v-if="searchLoading" class="px-6 py-5 text-base text-slate-500">
                                Searching products...
                            </div>
                            <div v-else-if="searchSuggestions.length">
                                <button
                                    v-for="(item, index) in searchSuggestions"
                                    :key="item.id"
                                    type="button"
                                    class="flex w-full items-center gap-4 border-b border-slate-100 px-6 py-5 text-left last:border-0"
                                    :class="index === activeSuggestionIndex ? 'bg-slate-50' : 'bg-white hover:bg-slate-50'"
                                    @mousedown.prevent="visitSuggestedProduct(item.url)"
                                >
                                    <img
                                        v-if="item.image"
                                        :src="item.image"
                                        :alt="item.name"
                                        class="h-14 w-14 rounded-xl border border-slate-200 object-cover"
                                    >
                                    <div v-else class="h-14 w-14 rounded-xl border border-slate-200 bg-slate-100" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-lg font-semibold text-slate-900">{{ item.name }}</p>
                                        <p class="text-sm text-slate-500">
                                            {{ item.price || 'Price pending' }}
                                            <span v-if="item.isOnSale" class="ml-1 font-semibold text-rose-600">On sale</span>
                                        </p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="hidden items-center justify-end gap-2 lg:flex">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-50"
                            @click="toggleDarkMode"
                        >
                            {{ darkMode ? 'Light Mode' : 'Dark Mode' }}
                        </button>
                        <template v-if="auth.user">
                            <Link :href="navigation.accountUrl ?? '/account'" class="secondary-button !px-4 !py-2">Account</Link>
                            <Link href="/logout" method="post" as="button" class="secondary-button !px-4 !py-2">Log out</Link>
                        </template>
                        <template v-else>
                            <Link href="/login" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Log in</Link>
                            <Link href="/register" class="primary-button !px-4 !py-2">Register</Link>
                        </template>
                        <div ref="cartButtonAnchor">
                            <Link :href="navigation.cartUrl ?? '/cart'" class="primary-button !px-4 !py-2">
                                Cart ({{ cartSummary.itemCount ?? 0 }})
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <nav :class="isLocationsPage ? 'border-t border-brand-500 bg-brand-500' : 'border-t border-slate-200 bg-white'">
                <div class="hidden h-12 items-center justify-between bg-brand-500 lg:flex">
                    <div class="header-shell flex h-full items-center gap-6">
                        <div class="flex items-center gap-6 text-sm font-semibold uppercase tracking-[0.14em] text-rose-50">
                            <Link href="/" class="transition hover:text-white">Home</Link>
                            <Link :href="navigation.shopUrl ?? '/shop'" class="transition hover:text-white">Catalog</Link>
                            <Link :href="navigation.locationsUrl ?? '/locations'" class="transition hover:text-white">Locations</Link>
                            <Link v-if="stockLocatorUrl" :href="stockLocatorUrl" class="transition hover:text-white">Stock Locator</Link>
                            <Link :href="navigation.contactUrl ?? '/contact'" class="transition hover:text-white">Contact</Link>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div
            v-if="mobileMenuOpen"
            class="fixed inset-0 z-40 bg-slate-950/20 backdrop-blur-sm lg:hidden"
            @click="closeMobileMenu"
        />

        <aside
            class="fixed inset-y-0 right-0 z-50 w-full max-w-sm transform overflow-y-auto bg-white px-5 py-6 shadow-[0_24px_80px_rgba(15,23,42,0.24)] transition duration-200 lg:hidden"
            :class="mobileMenuOpen ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">Menu</p>
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-950" @click="closeMobileMenu">
                    Close
                </button>
            </div>

            <form class="mt-6" @submit.prevent="runGlobalSearch">
                <label class="sr-only" for="mobile-global-search">Search products</label>
                <div class="flex items-center rounded-full border border-slate-200 px-2 py-2">
                    <input
                        id="mobile-global-search"
                        v-model="globalSearch"
                        type="search"
                        placeholder="Search the catalog"
                        class="w-full bg-transparent px-3 py-1 text-sm text-slate-900 outline-none placeholder:text-slate-400"
                    >
                    <button type="submit" class="primary-button !px-3 !py-2">Go</button>
                </div>
            </form>

            <div class="mt-6 space-y-3">
                <Link href="/" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Home</Link>
                <Link :href="navigation.shopUrl ?? '/shop'" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Shop</Link>
                <Link :href="navigation.locationsUrl ?? '/locations'" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Locations</Link>
                <Link v-if="stockLocatorUrl" :href="stockLocatorUrl" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Stock Locator</Link>
                <Link :href="navigation.contactUrl ?? '/contact'" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Contact</Link>
                <Link :href="navigation.checkoutUrl ?? '/checkout'" class="block rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-950" @click="closeMobileMenu">Checkout</Link>
            </div>

            <div class="mt-8 space-y-3 border-t border-slate-200 pt-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Account</p>
                <button type="button" class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm font-semibold text-slate-950" @click="toggleDarkMode">
                    {{ darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}
                </button>
                <template v-if="auth.user">
                    <Link :href="navigation.accountUrl ?? '/account'" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-950" @click="closeMobileMenu">
                        Account
                    </Link>
                    <Link href="/logout" method="post" as="button" class="block w-full rounded-2xl border border-slate-200 px-4 py-3 text-left text-sm font-semibold text-slate-950">
                        Log out
                    </Link>
                </template>
                <template v-else>
                    <Link href="/login" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-950" @click="closeMobileMenu">
                        Log in
                    </Link>
                    <Link href="/register" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-950" @click="closeMobileMenu">
                        Create account
                    </Link>
                </template>
            </div>

            <div v-if="featuredCollections.length" class="mt-8 space-y-3 border-t border-slate-200 pt-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Popular collections</p>
                <Link
                    v-for="collection in featuredCollections"
                    :key="collection.id"
                    :href="collection.url"
                    class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-800"
                    @click="closeMobileMenu"
                >
                    {{ collection.name }}
                </Link>
            </div>

            <div class="mt-8 space-y-2 border-t border-slate-200 pt-6 text-sm text-slate-600">
                <p v-if="contact.phone"><strong class="text-slate-950">Phone:</strong> {{ contact.phone }}</p>
                <p v-if="contact.email"><strong class="text-slate-950">Email:</strong> {{ contact.email }}</p>
            </div>
        </aside>

        <main :class="isLocationsPage ? 'relative z-10 w-full' : 'store-shell relative z-10 pb-24 pt-6 md:pt-8 lg:pb-8'">
            <div v-if="isLocationsPage">
                <slot />
            </div>
            <div v-else class="rounded-[2rem] bg-white px-5 py-6 shadow-[0_24px_100px_rgba(15,23,42,0.10)] md:px-8 md:py-8">
                <div
                    v-if="(flash.success && !isShopPage) || flash.error"
                    class="mb-6 rounded-2xl border px-4 py-3 text-sm backdrop-blur-md"
                    :class="flash.success ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'"
                >
                    {{ flash.success || flash.error }}
                </div>

                <slot />
            </div>
        </main>

        <div
            v-if="miniCartOpen"
            class="fixed z-[80] w-[min(28rem,calc(100vw-1.5rem))] rounded-2xl border border-slate-200 bg-white p-4 shadow-[0_26px_80px_rgba(15,23,42,0.24)]"
            :style="miniCartStyle"
        >
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Cart updated</p>
                    <p class="text-sm font-semibold text-slate-900">{{ cartPreview.itemCount ?? 0 }} item<span v-if="(cartPreview.itemCount ?? 0) !== 1">s</span></p>
                </div>
                <button type="button" class="rounded-full border border-slate-200 px-2.5 py-1 text-sm font-semibold text-slate-600 hover:bg-slate-50" @click="closeMiniCart">
                    ×
                </button>
            </div>

            <div v-if="(cartPreview.lines ?? []).length" class="max-h-[18rem] space-y-3 overflow-y-auto py-3">
                <div
                    v-for="line in cartPreview.lines"
                    :key="line.id"
                    class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                >
                    <div class="flex items-start gap-3">
                        <img
                            v-if="line.image"
                            :src="line.image"
                            :alt="line.name"
                            class="h-12 w-12 rounded-lg border border-slate-200 object-cover"
                        >
                        <div v-else class="h-12 w-12 rounded-lg border border-slate-200 bg-white" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ line.name }}</p>
                            <p class="text-xs text-slate-500">{{ line.total || line.unitPrice || '-' }}</p>
                        </div>
                        <button type="button" class="text-xs font-semibold text-rose-600 hover:text-rose-700" @click="removeMiniCartLine(line.id)">
                            Remove
                        </button>
                    </div>

                    <div class="mt-2 inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2 py-1">
                        <button type="button" class="h-7 w-7 rounded-md border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="updateMiniCartLine(line, line.quantity - 1)">
                            -
                        </button>
                        <span class="min-w-8 text-center text-sm font-semibold text-slate-900">{{ line.quantity }}</span>
                        <button type="button" class="h-7 w-7 rounded-md border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="updateMiniCartLine(line, line.quantity + 1)">
                            +
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="py-4 text-sm text-slate-500">
                Your cart is currently empty.
            </div>

            <div class="border-t border-slate-200 pt-3">
                <div class="mb-3 flex items-center justify-between text-sm">
                    <span class="text-slate-500">Subtotal (inc GST)</span>
                    <span class="font-semibold text-slate-900">{{ cartPreview.total || cartPreview.subTotal || '-' }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <Link :href="navigation.cartUrl ?? '/cart'" class="secondary-button !rounded-xl !px-3 !py-2 text-center" @click="closeMiniCart">
                        View cart
                    </Link>
                    <Link :href="navigation.checkoutUrl ?? '/checkout'" class="primary-button !rounded-xl !px-3 !py-2 text-center" @click="closeMiniCart">
                        Checkout
                    </Link>
                </div>
            </div>
        </div>

        <section v-if="!isLocationsPage && featuredCollections.length" class="border-t border-slate-200 bg-white">
            <div class="store-shell flex flex-wrap items-center gap-2 py-6">
                <span class="divider-label pr-2 text-slate-500">Popular collections</span>
                <Link
                    v-for="collection in featuredCollections"
                    :key="collection.id"
                    :href="collection.url"
                    class="pill transition hover:border-brand-400/40 hover:text-slate-950"
                >
                    {{ collection.name }}
                </Link>
            </div>
        </section>

        <footer v-if="!isLocationsPage" class="mt-20 border-t border-slate-200 bg-white text-slate-950">
            <div class="store-shell grid gap-10 py-12 lg:grid-cols-[1.2fr_0.9fr_1.2fr]">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img :src="'/images/popattack-logo.png'" alt="Pop Attack logo" class="h-14 w-auto shrink-0 object-contain">
                        <div>
                            <p class="text-lg font-semibold text-slate-950">{{ brandName }}</p>
                            <p class="text-sm text-slate-500">Contact details</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-slate-600">
                        <p v-if="contact.phone"><strong class="text-slate-900">Phone:</strong> {{ contact.phone }}</p>
                        <p v-if="contact.email"><strong class="text-slate-900">Support:</strong> {{ contact.email }}</p>
                        <p v-if="contact.onlineEmail"><strong class="text-slate-900">Online:</strong> {{ contact.onlineEmail }}</p>
                        <p v-if="contact.partnershipsEmail"><strong class="text-slate-900">Partnerships:</strong> {{ contact.partnershipsEmail }}</p>
                    </div>
                </div>

                <div>
                    <p class="divider-label mb-4">Account</p>
                    <div class="space-y-3 text-sm text-slate-600">
                        <Link :href="navigation.accountUrl ?? '/account'" class="block transition hover:text-slate-950">My account</Link>
                        <Link :href="navigation.cartUrl ?? '/cart'" class="block transition hover:text-slate-950">Cart</Link>
                        <Link :href="navigation.checkoutUrl ?? '/checkout'" class="block transition hover:text-slate-950">Checkout</Link>
                        <Link v-if="stockLocatorUrl" :href="stockLocatorUrl" class="block transition hover:text-slate-950">Stock locator</Link>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="divider-label">Subscribe</p>
                    <p class="text-sm text-slate-600">
                        Get launch alerts, restock drops, and event notices from the online team.
                    </p>
                    <form class="flex items-center rounded-full border border-slate-300 bg-white px-2 py-2" @submit.prevent="subscribe">
                        <label class="sr-only" for="subscription-email">Subscription email</label>
                        <input
                            id="subscription-email"
                            v-model="subscriptionEmail"
                            type="email"
                            required
                            placeholder="you@example.com"
                            class="w-full bg-transparent px-3 text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        >
                        <button type="submit" class="primary-button !px-4 !py-2">Join</button>
                    </form>
                </div>
            </div>
        </footer>

    </div>
</template>
