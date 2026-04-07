// Page content templates
const pageTemplates = {
    home: `
        <!-- Main content -->
        <main class="main-container">
            <!-- Page header -->
            <div class="page-header">
                <h1 class="page-title">Collectable Vending Machines Across the UK</h1>
                <p class="page-description">Discover Pop Attack UK's network of premium vending machines delivering Pokemon and other collectible trading cards. Check locations, see live stock, and enjoy convenient 24/7 access near you.</p>
            </div>
            <!-- Features section -->
                         <section class="features">
                                 <div class="feature-card">
                    <span class="feature-icon">🛒</span>
                    <h3>Fresh Products</h3>
                    <p>Our premium vending machines deliver the latest Pokemon and collectibles directly to UK collectors with unmatched convenience.</p>
                </div>
                
                <div class="feature-card">
                    <span class="feature-icon">🎮</span>
                    <h3>24/7 Vending Technology</h3>
                    <p>Access Pokemon, Magic the Gathering, Yu-Gi-Oh!, Dragon Ball Super, and more collectible cards anytime through our innovative vending machine technology.</p>
                </div>
                
                <div class="feature-card">
                    <span class="feature-icon">🚀</span>
                    <h3>Premium Quality</h3>
                    <p>Experience our unique vending machine technology delivering high-quality collectibles throughout the United Kingdom!</p>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="cta-section">
                <h2>🎉 Premium Vending Technology Now Live!</h2>
                <p>Be among the first to experience our cutting-edge vending machine technology in the UK</p>
                <a href="/stock-locator" onclick="navigateToPage('stock-locator'); return false;" class="btn btn-primary" style="background: white; color: rgba(255, 0, 0, 1);">Check What's Available Now</a>
            </section>
        </main>
    `,
    
    'stock-locator': `
        <!-- Main content -->
        <main class="main-container">
            <!-- Page header -->
            <div class="page-header">
                <h1 class="page-title">Realtime Stock Locator</h1>
                <p class="page-description">See what's in stock across Pop Attack vending machines before you travel. Check live availability for Pokemon and other collectible trading cards in seconds.</p>
            </div>
            <!-- Stock locator section -->
            <section class="stock-locator-section">
                <div class="section-header">
                    <h2>Realtime Stock Locator</h2>
                </div>
                
                <div class="app-container" id="stock-locator-container">
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                        <p>Loading stock information...</p>
                    </div>
                    <!-- Iframe will be dynamically inserted here -->
                </div>
            </section>
        </main>
    `,
    
    about: `
        <!-- Main content -->
        <main class="main-container">
            <!-- Page header -->
            <div class="page-header">
                <h1 class="page-title">About Pop Attack UK</h1>
                <p class="page-description">
                    Discover the story behind our mission to bring premium vending machine technology to British collectors.
                </p>
            </div>

            <!-- About Content -->
            <section class="info-cards">
                <div class="info-card">
                    <span class="info-card-icon">🎯</span>
                    <h3>Our Story</h3>
                    <p>Pop Attack UK was founded with a simple mission: to bring innovative vending machine technology to British collectors. Our premium machines mark the beginning of a new era for collectible card game enthusiasts.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">🚀</span>
                    <h3>Our Mission</h3>
                    <p>We're committed to providing both the best products and customer experience possible. Through our premium vending technology, we offer high-quality collectible card games with authentic quality and competitive pricing.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">💎</span>
                    <h3>Quality Promise</h3>
                    <p>Every product in our vending machines is carefully selected and sourced from trusted suppliers, ensuring British collectors get a premium, high-quality collectible card experience.</p>
                </div>
            </section>

            <!-- Company Info -->
            <section class="cta-section">
                <h2>🏢 Company Information</h2>
                <div style="margin-top: 2rem; text-align: left;">
                    <p><strong>Company Name:</strong> Pop Attack UK LTD</p>
                    <p><strong>Company Registration Number:</strong> 16286941</p>
                    <p><strong>Phone:</strong> 033 078 90801</p>
                    <p><strong>Email:</strong> chris@vendables.com</p>
                    <p><strong>Location:</strong> United Kingdom</p>
                </div>
            </section>
        </main>
    `,
    
    contact: `
        <!-- Main content -->
        <main class="main-container">
            <!-- Page header -->
            <div class="page-header">
                <h1 class="page-title">Contact Us</h1>
                <p class="page-description">
                    Have questions or need support? We're here to help!
                </p>
            </div>

            <!-- Contact Information -->
            <section class="info-cards">
                <div class="info-card">
                    <span class="info-card-icon">📞</span>
                    <h3>Phone Support</h3>
                    <p><strong>Phone:</strong> 033 078 90801</p>
                    <p>Call us for immediate assistance with your questions or concerns.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">✉️</span>
                    <h3>Email Support</h3>
                    <p><strong>Email:</strong> chris@vendables.com</p>
                    <p>Send us an email for detailed inquiries or support requests.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">💬</span>
                    <h3>Discord Community</h3>
                    <p><strong>Discord:</strong> <a href="https://discord.gg/popattack" target="_blank">Join Our Discord</a></p>
                    <p>Connect with other collectors and get real-time support from our community.</p>
                </div>
            </section>

            <!-- Company Details -->
            <section class="cta-section">
                <h2>🏢 Company Details</h2>
                <div style="margin-top: 2rem; text-align: left;">
                    <p><strong>Company Name:</strong> Pop Attack UK LTD</p>
                    <p><strong>Company Registration Number:</strong> 16286941</p>
                    <p><strong>Location:</strong> United Kingdom</p>
                    <p><strong>Business Type:</strong> Collectible Card Game Vending</p>
                </div>
            </section>
        </main>
    `,
    
    privacy: `
        <!-- Main content -->
        <main class="main-container">
            <!-- Page header -->
            <div class="page-header">
                <h1 class="page-title">Privacy Policy</h1>
                <p class="page-description">
                    Learn how Pop Attack UK protects and handles your personal information.
                </p>
            </div>

            <!-- Privacy Content -->
            <section class="info-cards">
                <div class="info-card">
                    <span class="info-card-icon">🔒</span>
                    <h3>Information We Collect</h3>
                    <p>We collect information you provide directly to us, such as when you contact us for support, join our Discord community, or use our vending machines. This may include your name, email address, phone number, and any other information you choose to provide.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">🛡️</span>
                    <h3>How We Use Your Information</h3>
                    <p>We use the information we collect to provide, maintain, and improve our services, communicate with you, and ensure the security of our operations. We do not sell, trade, or otherwise transfer your personal information to third parties.</p>
                </div>
                
                <div class="info-card">
                    <span class="info-card-icon">📋</span>
                    <h3>Your Rights</h3>
                    <p>You have the right to access, correct, or delete your personal information. You can also request information about how we process your data. Contact us at chris@vendables.com to exercise these rights.</p>
                </div>
            </section>

            <!-- Legal Information -->
            <section class="cta-section">
                <h2>📄 Legal Information</h2>
                <div style="margin-top: 2rem; text-align: left;">
                    <p><strong>Last Updated:</strong> January 2025</p>
                    <p><strong>Company:</strong> Pop Attack UK LTD</p>
                    <p><strong>Contact:</strong> chris@vendables.com</p>
                    <p><strong>Phone:</strong> 033 078 90801</p>
                    <p>This privacy policy is subject to change. Please check back periodically for updates.</p>
                </div>
            </section>
        </main>
    `,
    
    locations: `
        <!-- Full Screen Store Locator -->
        <div class="fullscreen-store-locator">
            <!-- Floating Search & Controls -->
            <div class="floating-controls">
                <div class="search-panel">
                    <div class="search-header">
                        <h1 class="page-title">Vending Machine Locations</h1>
                        <button class="toggle-panel" onclick="toggleStorePanel()">
                            <span id="panel-toggle-text">Show Stores</span>
                        </button>
                    </div>
                    <div class="search-bar">
                        <input type="text" id="store-search" placeholder="Search by location, postcode..." class="search-input">
                        <button onclick="searchStores()" class="search-btn">🔍</button>
                    </div>
                </div>
            </div>

            <!-- Floating Store List Panel -->
            <div class="floating-store-panel" id="floating-store-panel">
                <div class="panel-header">
                    <h3>Locations</h3>
                    <button class="close-panel" onclick="toggleStorePanel()">✕</button>
                </div>
                <div class="store-list" id="store-list">
                    <!-- Store listings will be populated here -->
                </div>
            </div>

            <!-- Full Screen Map -->
            <div class="fullscreen-map-container">
                <div id="map" class="mapbox-map"></div>
            </div>
        </div>
    `
};

// Flag to prevent popstate from interfering with programmatic navigation
let isProgrammaticNavigation = false;

// Page navigation system
function navigateToPage(pageId) {
    console.log('=== NAVIGATION START ===');
    console.log('Navigating to page:', pageId);
    console.log('Page templates available:', Object.keys(pageTemplates));
    
    // Check if we're in development (localhost) or production
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    console.log('Is localhost:', isLocalhost);
    
    if (isLocalhost) {
        // For localhost, load the page content directly without hash changes
        // This avoids conflicts with the hashchange event listener
        console.log('Loading page content directly for localhost');
        loadPageContent(pageId);
        
        // Don't set hash for localhost to avoid hashchange conflicts
        // The page content is already loaded, so we don't need the hash
        console.log('Skipping hash setting for localhost to avoid conflicts');
    } else {
        // For production, use clean URLs
        const newUrl = pageId === 'home' ? '/' : `/${pageId}`;
        console.log('Setting URL to:', newUrl);
        
        // Set flag to prevent popstate interference
        isProgrammaticNavigation = true;
        window.history.pushState({ page: pageId }, '', newUrl);
        
        // Load page content immediately for production
        loadPageContent(pageId);
        
        // Reset flag after a short delay
        setTimeout(() => {
            isProgrammaticNavigation = false;
        }, 100);
    }
    console.log('=== NAVIGATION END ===');
}

// Load page content
function loadPageContent(pageId) {
    console.log('=== LOAD PAGE CONTENT START ===');
    console.log('Loading page content for:', pageId);
    console.log('Available templates:', Object.keys(pageTemplates));
    
    try {
        const mainContent = document.getElementById('main-content');
        if (!mainContent) {
            console.error('Main content container not found');
            return;
        }
        console.log('Main content container found');
        
        // Try to get preloaded content first (disable DOM caching temporarily to fix issues)
        let template = getPreloadedContent(pageId);
        let isPreloaded = !!template;
        
        // Fallback to regular template if not preloaded
        if (!template) {
            template = pageTemplates[pageId];
            if (!template) {
                console.error('Page template not found:', pageId);
                console.log('Available templates:', Object.keys(pageTemplates));
                return;
            }
            console.log(`📥 Loading "${pageId}" from template (not preloaded)`);
        }
        
        console.log('Template found, length:', template?.length);
        console.log('Template preview (first 100 chars):', template?.substring(0, 100));
        
        // Fade out current content (faster for preloaded content)
        const fadeOutDuration = isPreloaded ? 75 : 150;
        mainContent.style.opacity = '0';
        
        setTimeout(() => {
            // Clear existing content properly and reset any page state
            mainContent.innerHTML = '';
            
            // Clean up any orphaned stock locator iframes
            if (pageId !== 'stock-locator') {
                // Remove any leftover stock locator iframes that might be hanging around
                const existingIframes = document.querySelectorAll('iframe[src*="vendables.retool.com"]');
                existingIframes.forEach(iframe => {
                    // Only remove if not in the proper stock locator container
                    const container = iframe.closest('#stock-locator-container');
                    if (!container) {
                        console.log('🗑️ Cleaning up orphaned stock locator iframe');
                        iframe.remove();
                    }
                });
            }
            
            // Update content with fresh template
            mainContent.innerHTML = template;
            
            // Update page title
            updatePageTitle(pageId);
            
            // Update active navigation
            setActiveNavigation(pageId);
            
            // Track page view in Google Analytics
            trackPageView(pageId);
            
            // Fade in new content
            mainContent.style.opacity = '1';
            
            // Scroll to top
            window.scrollTo({top: 0, behavior: 'smooth'});
            
            // Initialize page-specific functionality
            initializePage(pageId);
            
            // Preload related pages based on current page (immediate)
            setTimeout(() => preloadRelatedPages(pageId), 50);
            
        }, fadeOutDuration);
    } catch (error) {
        console.error('Error in loadPageContent:', error);
    }
    console.log('=== LOAD PAGE CONTENT END ===');
}

// Initialize page-specific functionality
function initializePage(pageId) {
    // Remove any existing page classes and clean up state
    document.body.classList.remove('locations-page', 'stock-locator-page');
    
    // Clear any page-specific intervals or timeouts
    if (window.pageCleanupFunctions) {
        window.pageCleanupFunctions.forEach(cleanup => cleanup());
        window.pageCleanupFunctions = [];
    } else {
        window.pageCleanupFunctions = [];
    }
    
    if (pageId === 'stock-locator') {
        document.body.classList.add('stock-locator-page');
        
        // Create iframe (resources should be prefetched for faster loading)
        setTimeout(() => {
            const container = document.getElementById('stock-locator-container');
            if (container && !container.querySelector('iframe')) {
                console.log('⚡ Creating stock locator iframe (with prefetched resources)');
                const iframe = document.createElement('iframe');
                iframe.src = 'https://vendables.retool.com/embedded/public/a5fade68-1cff-41b7-b9c0-5f7e419eecc0';
                iframe.title = 'Pop Attack Stock Locator';
                iframe.allow = 'geolocation';
                iframe.style.cssText = 'height: calc(100vh - 200px); min-height: 600px; width: 100%; border: none;';
                iframe.onload = () => {
                    console.log('✅ Stock locator iframe loaded');
                    if (typeof hideLoading === 'function') {
                        hideLoading();
                    }
                };
                iframe.onerror = () => {
                    console.error('❌ Stock locator iframe failed to load');
                };
                container.appendChild(iframe);
            } else if (container.querySelector('iframe')) {
                console.log('📦 Stock locator iframe already exists');
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }
            }
        }, 50);
    } else if (pageId === 'locations') {
        // Add locations page class for styling
        document.body.classList.add('locations-page');
        
        // Initialize Mapbox store locator
        console.log('Initializing locations page...');
        setTimeout(() => {
            if (typeof window.initializeStoreLocator === 'function') {
                initializeStoreLocator();
            } else {
                console.error('Store locator initialization function not found');
            }
        }, 200);
    }
}

function updatePageTitle(pageId) {
    const titles = {
        'home': 'Pop Attack - Pokemon & Collectables Delivered To Your Door',
        'stock-locator': 'Realtime Stock Locator - Pop Attack',
        'locations': 'Vending Machine Locations - Pop Attack',
        'about': 'About Us - Pop Attack',
        'contact': 'Contact Us - Pop Attack',
        'privacy': 'Privacy Policy - Pop Attack'
    };

    const descriptions = {
        'home': 'Discover Pop Attack UK\'s premium vending machines delivering Pokemon and collectible trading cards across the UK. Find locations, check live stock, and enjoy 24/7 access.',
        'stock-locator': 'Check live stock availability across Pop Attack vending machines before you travel. See what\'s in stock for Pokemon and other collectibles in seconds.',
        'locations': 'Browse all Pop Attack vending machine locations in the UK. Get directions, see nearby machines, and plan your next visit.',
        'about': 'Learn about Pop Attack UK and our mission to deliver premium collectible experiences through vending technology.',
        'contact': 'Contact Pop Attack UK for support, business inquiries, and general questions.',
        'privacy': 'Read Pop Attack UK\'s privacy policy and how we handle your data.'
    };

    const canonicalPaths = {
        'home': '/',
        'stock-locator': '/stock-locator',
        'locations': '/locations',
        'about': '/about',
        'contact': '/contact',
        'privacy': '/privacy'
    };

    document.title = titles[pageId] || 'Pop Attack';

    // Update meta description
    const metaDesc = document.querySelector('meta[name="description"]#meta-description') || document.querySelector('meta[name="description"]');
    if (metaDesc) {
        metaDesc.setAttribute('content', descriptions[pageId] || descriptions['home']);
    }

    // Update canonical link
    const canonicalEl = document.querySelector('link[rel="canonical"]#canonical-link') || document.querySelector('link[rel="canonical"]');
    const origin = window.location.origin || 'https://popattack.co.uk';
    const path = canonicalPaths[pageId] || '/';
    if (canonicalEl) {
        canonicalEl.setAttribute('href', origin + path);
    }
}

// Shared component templates
const headerTemplate = `
<!-- Header -->
<header class="header-wrapper">
    <!-- Top White Section with Logo -->
    <div class="header-top">
        <div class="header-top-container">
            <a href="/" onclick="navigateToPage('home'); return false;" class="logo">
                <img src="images/popattack-logo.png" alt="Pop Attack Logo" />
            </a>
            <button class="dark-mode-toggle" onclick="toggleDarkMode()" aria-label="Toggle dark mode">
                <span class="toggle-icon">🌙</span>
                <span class="toggle-text">Dark Mode</span>
            </button>
        </div>
    </div>
    
    <!-- Bottom Red Navigation Bar -->
    <nav class="header-nav">
        <div class="header-nav-container">
            <ul class="nav-menu">
                <li><a href="/" onclick="navigateToPage('home'); return false;" id="nav-home">HOME</a></li>
                <li><a href="/stock-locator" onclick="navigateToPage('stock-locator'); return false;" id="nav-stock-locator">STOCK LOCATOR</a></li>
                <li><a href="/locations" onclick="navigateToPage('locations'); return false;" id="nav-locations">LOCATIONS</a></li>
                <li><a href="https://discord.gg/popattack" target="_blank">DISCORD</a></li>
            </ul>
        </div>
    </nav>
</header>
`;

const footerTemplate = `
<!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Pop Attack UK</h4>
                <p>🎉 Our premium vending machines are now live in the UK! We're bringing top-quality Pokemon & collectibles directly to British collectors through innovative vending technology.</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul class="footer-nav">
                                    <li><a href="/stock-locator" onclick="navigateToPage('stock-locator'); return false;">Stock Locator</a></li>
                <li><a href="/about" onclick="navigateToPage('about'); return false;">About Us</a></li>
                    <li><a href="https://discord.gg/popattack" target="_blank">Join Discord</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact Info</h4>
                <p><strong>Company:</strong> Pop Attack UK LTD</p>
                <p><strong>Phone:</strong> 033 078 90801</p>
                <p><strong>Email:</strong> chris@vendables.com</p>
                <p><strong>CRN:</strong> 16286941</p>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul class="footer-nav">
                                    <li><a href="/contact" onclick="navigateToPage('contact'); return false;">Contact Us</a></li>
                <li><a href="/privacy" onclick="navigateToPage('privacy'); return false;">Privacy Policy</a></li>
                    <li><a href="https://discord.gg/popattack" target="_blank">Join Discord</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Pop Attack UK. All rights reserved.</p>
        </div>
    </div>
</footer>
`;

// Load shared components
function loadSharedComponents() {
    console.log('Loading shared components...');
    
    // Load header
    const headerPlaceholder = document.getElementById('header-placeholder');
    if (headerPlaceholder) {
        headerPlaceholder.innerHTML = headerTemplate;
        console.log('Header loaded successfully');
    } else {
        console.warn('Header placeholder not found');
    }
    
    // Load footer
    const footerPlaceholder = document.getElementById('footer-placeholder');
    if (footerPlaceholder) {
        footerPlaceholder.innerHTML = footerTemplate;
        console.log('Footer loaded successfully');
    } else {
        console.warn('Footer placeholder not found');
    }
    
    // Set active navigation based on current page
    setActiveNavigation();
}

// Set active navigation based on current page
function setActiveNavigation(pageId) {
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const linkPageId = link.getAttribute('onclick')?.match(/navigateToPage\('([^']+)'\)/)?.[1];
        if (linkPageId === pageId) {
            link.classList.add('active');
        }
    });
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (!isLocalhost) {
        // Check if this is programmatic navigation
        if (isProgrammaticNavigation) {
            console.log('Ignoring popstate event during programmatic navigation');
            return;
        }
        
        // For production, handle popstate events
        const pageId = event.state?.page || 'home';
        console.log('Popstate event - loading page:', pageId);
        
        // Only load page if we have a valid state or if the path indicates a specific page
        if (event.state?.page) {
            loadPageContent(pageId);
        } else {
            // If no state, check the current path
            const path = window.location.pathname;
            const cleanPath = path.replace(/\.html$/, '').replace(/\/$/, '');
            
            if (cleanPath === '' || cleanPath === '/index') {
                loadPageContent('home');
            } else if (cleanPath === '/stock-locator') {
                loadPageContent('stock-locator');
            } else if (cleanPath === '/locations') {
                loadPageContent('locations');
            } else if (cleanPath === '/contact') {
                loadPageContent('contact');
            } else if (cleanPath === '/privacy') {
                loadPageContent('privacy');
            } else {
                loadPageContent('home');
            }
        }
    }
    // For localhost, no popstate handling needed
});

// Google Analytics SPA Tracking
function trackPageView(pageId) {
    if (typeof gtag !== 'undefined') {
        // Get the clean URL for this page
        const pageUrl = getCleanUrlForPage(pageId);
        const pageTitle = getPageTitle(pageId);
        
        console.log(`📊 Tracking page view: ${pageId} -> ${pageUrl}`);
        
        // Send page view event to Google Analytics
        gtag('config', 'G-MJGSS89G79', {
            page_location: window.location.origin + pageUrl,
            page_path: pageUrl,
            page_title: pageTitle
        });
        
        // Also send as a custom event for better tracking
        gtag('event', 'page_view', {
            page_title: pageTitle,
            page_location: window.location.origin + pageUrl,
            page_path: pageUrl,
            page_id: pageId
        });
        
        console.log(`✅ Analytics tracked: ${pageTitle} (${pageUrl})`);
    } else {
        console.warn('⚠️ Google Analytics not available');
    }
}

function getCleanUrlForPage(pageId) {
    switch (pageId) {
        case 'home': return '/';
        case 'stock-locator': return '/stock-locator';
        case 'locations': return '/locations';
        case 'about': return '/about';
        case 'contact': return '/contact';
        case 'privacy': return '/privacy';
        default: return '/';
    }
}

function getPageTitle(pageId) {
    switch (pageId) {
        case 'home': return 'Pop Attack - Pokemon & Collectables Delivered To Your Door';
        case 'stock-locator': return 'Stock Locator - Pop Attack';
        case 'locations': return 'Vending Machine Locations - Pop Attack';
        case 'about': return 'About Us - Pop Attack';
        case 'contact': return 'Contact - Pop Attack';
        case 'privacy': return 'Privacy Policy - Pop Attack';
        default: return 'Pop Attack - Pokemon & Collectables';
    }
}

function trackCustomEvent(eventName, category, label, value = null) {
    if (typeof gtag !== 'undefined') {
        const eventData = {
            'event_category': category,
            'event_label': label
        };
        
        if (value !== null) {
            eventData.value = value;
        }
        
        gtag('event', eventName, eventData);
        console.log(`📊 Custom event tracked: ${eventName} (${category}/${label})`);
    }
}

// Content Preloading System
let preloadedContent = new Map();
let preloadingInProgress = false;
let cachedIframes = new Map(); // Cache iframe instances to prevent reloading
let cachedPageDOM = new Map(); // Cache fully rendered DOM elements
let preloadedResources = new Set(); // Track preloaded resources

function preloadAllPages() {
    if (preloadingInProgress) return;
    preloadingInProgress = true;
    
    console.log('🚀 Starting immediate content preloading...');
    
    // Start preloading immediately - no delay
    preloadCriticalPages();
}

function preloadCriticalPages() {
    // Priority order: most likely to be visited first
    const priorityPages = [
        { id: 'stock-locator', priority: 1, delay: 0 },
        { id: 'locations', priority: 2, delay: 100 },
        { id: 'about', priority: 3, delay: 200 },
        { id: 'contact', priority: 4, delay: 300 },
        { id: 'privacy', priority: 5, delay: 400 }
    ];
    
    console.log('📦 Preloading all pages with minimal delays...');
    
    priorityPages.forEach(page => {
        setTimeout(() => {
            preloadPageContent(page.id, page.priority);
        }, page.delay);
    });
}

function preloadPageContent(pageId, priority = 1) {
    // Skip if already preloaded
    if (preloadedContent.has(pageId)) {
        console.log(`📦 Page "${pageId}" already preloaded`);
        return;
    }
    
    console.log(`⏳ Preloading page "${pageId}" (Priority: ${priority})`);
    
    try {
        // Get the template
        const template = pageTemplates[pageId];
        if (!template) {
            console.warn(`❌ Template not found for page: ${pageId}`);
            return;
        }
        
        // Create a hidden container to preload and cache DOM elements
        const preloadContainer = document.createElement('div');
        preloadContainer.style.display = 'none';
        preloadContainer.innerHTML = template;
        
        // Add to document temporarily to ensure all resources load
        document.body.appendChild(preloadContainer);
        
        // Cache the preloaded content
        preloadedContent.set(pageId, {
            template: template,
            container: preloadContainer,
            timestamp: Date.now(),
            priority: priority
        });
        
        // Wait a moment for DOM to settle, then clean up (disable DOM caching for now)
        setTimeout(() => {
            // Remove from document after preloading resources
            if (preloadContainer.parentNode) {
                preloadContainer.parentNode.removeChild(preloadContainer);
            }
            
            console.log(`✅ Page "${pageId}" preloaded (template cached)`);
        }, 100);
        
        // Preload page-specific resources immediately
        preloadPageResources(pageId, preloadContainer);
        
        console.log(`✅ Page "${pageId}" preload initiated`);
        
        // Track preloading in analytics
        trackCustomEvent('page_preloaded', 'Performance', pageId, priority);
        
    } catch (error) {
        console.error(`❌ Error preloading page "${pageId}":`, error);
    }
}

function preloadPageResources(pageId, container) {
    // Preload specific resources based on page type
    switch (pageId) {
        case 'stock-locator':
            // Instead of preloading iframe, prefetch the main resource
            console.log('📡 Prefetching stock locator resources...');
            
            // Prefetch the main iframe source
            const prefetchLink = document.createElement('link');
            prefetchLink.rel = 'prefetch';
            prefetchLink.href = 'https://vendables.retool.com/embedded/public/a5fade68-1cff-41b7-b9c0-5f7e419eecc0';
            prefetchLink.onload = () => console.log('✅ Stock locator resource prefetched');
            prefetchLink.onerror = () => console.warn('❌ Stock locator prefetch failed');
            document.head.appendChild(prefetchLink);
            
            // Also try DNS prefetch for the domain
            const dnsPrefetch = document.createElement('link');
            dnsPrefetch.rel = 'dns-prefetch';
            dnsPrefetch.href = 'https://vendables.retool.com';
            document.head.appendChild(dnsPrefetch);
            
            // And preconnect for faster connection
            const preconnect = document.createElement('link');
            preconnect.rel = 'preconnect';
            preconnect.href = 'https://vendables.retool.com';
            preconnect.crossOrigin = 'anonymous';
            document.head.appendChild(preconnect);
            
            console.log('📦 Stock locator resources prefetched');
            break;
            
        case 'locations':
            // Preload Mapbox resources and store data
            console.log('🗺️ Preloading map resources...');
            if (typeof window.storeData !== 'undefined') {
                console.log('📍 Store data already available for preloading');
            }
            // Mapbox scripts are already loaded, so this will be fast
            break;
            
        default:
            // For other pages, preload any images
            const images = container.querySelectorAll('img');
            images.forEach(img => {
                if (img.src) {
                    const preloadImg = new Image();
                    preloadImg.src = img.src;
                }
            });
            break;
    }
}

function getPreloadedContent(pageId) {
    const cached = preloadedContent.get(pageId);
    if (cached) {
        console.log(`🚀 Using preloaded content for "${pageId}" (cached ${Math.round((Date.now() - cached.timestamp) / 1000)}s ago)`);
        return cached.template;
    }
    return null;
}

function clearOldPreloadedContent() {
    // Clear content older than 10 minutes to free memory
    const tenMinutesAgo = Date.now() - (10 * 60 * 1000);
    
    for (const [pageId, cached] of preloadedContent.entries()) {
        if (cached.timestamp < tenMinutesAgo) {
            console.log(`🗑️ Clearing old preloaded content for "${pageId}"`);
            preloadedContent.delete(pageId);
        }
    }
    
    // Also clear old DOM cache
    for (const [pageId, cached] of cachedPageDOM.entries()) {
        if (cached.timestamp < tenMinutesAgo) {
            console.log(`🗑️ Clearing old cached DOM for "${pageId}"`);
            cachedPageDOM.delete(pageId);
        }
    }
}

// DOM Caching Functions
function cachePageDOM(pageId, domElement) {
    if (!domElement) return;
    
    // Clone the DOM and cache it with timestamp
    const clonedDOM = domElement.cloneNode(true);
    cachedPageDOM.set(pageId, {
        dom: clonedDOM,
        timestamp: Date.now()
    });
    
    console.log(`📦 Cached DOM for "${pageId}"`);
    
    // Preload all resources in this DOM
    preloadDOMResources(clonedDOM, pageId);
}

function getCachedPageDOM(pageId) {
    const cached = cachedPageDOM.get(pageId);
    if (cached) {
        console.log(`📦 Retrieved cached DOM for "${pageId}" (cached ${Math.round((Date.now() - cached.timestamp) / 1000)}s ago)`);
        return cached.dom;
    }
    return null;
}

// Comprehensive Resource Preloading
function preloadDOMResources(domElement, pageId) {
    console.log(`🔄 Preloading resources for "${pageId}"...`);
    
    // Preload all images
    const images = domElement.querySelectorAll('img');
    images.forEach(img => {
        if (img.src && !preloadedResources.has(img.src)) {
            preloadImage(img.src);
            preloadedResources.add(img.src);
        }
        if (img.srcset && !preloadedResources.has(img.srcset)) {
            preloadImage(img.srcset);
            preloadedResources.add(img.srcset);
        }
    });
    
    // Preload background images from CSS
    const elementsWithBg = domElement.querySelectorAll('*');
    elementsWithBg.forEach(el => {
        const style = window.getComputedStyle(el);
        const bgImage = style.backgroundImage;
        if (bgImage && bgImage !== 'none') {
            const matches = bgImage.match(/url\(['"]?([^'"]+)['"]?\)/);
            if (matches && matches[1] && !preloadedResources.has(matches[1])) {
                preloadImage(matches[1]);
                preloadedResources.add(matches[1]);
            }
        }
    });
    
    // Preload any linked resources
    const links = domElement.querySelectorAll('link[href]');
    links.forEach(link => {
        if (link.href && !preloadedResources.has(link.href)) {
            preloadResource(link.href, link.rel);
            preloadedResources.add(link.href);
        }
    });
    
    console.log(`✅ Resource preloading initiated for "${pageId}"`);
}

function preloadImage(src) {
    if (!src) return;
    
    const img = new Image();
    img.onload = () => console.log(`✅ Preloaded image: ${src.substring(0, 50)}...`);
    img.onerror = () => console.warn(`❌ Failed to preload image: ${src.substring(0, 50)}...`);
    img.src = src;
}

function preloadResource(href, type = '') {
    if (!href) return;
    
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = href;
    link.onload = () => console.log(`✅ Preloaded resource: ${href.substring(0, 50)}...`);
    link.onerror = () => console.warn(`❌ Failed to preload resource: ${href.substring(0, 50)}...`);
    
    // Add to head temporarily
    document.head.appendChild(link);
    
    // Remove after 30 seconds to clean up
    setTimeout(() => {
        if (link.parentNode) {
            link.parentNode.removeChild(link);
        }
    }, 30000);
}

// Smart iframe management to prevent reloading
function createOrGetCachedIframe(pageId, iframeConfig) {
    const cacheKey = `${pageId}-iframe`;
    
    // Check if we already have a cached iframe
    if (cachedIframes.has(cacheKey)) {
        const cachedIframe = cachedIframes.get(cacheKey);
        console.log(`📦 Using cached iframe for "${pageId}"`);
        return cachedIframe;
    }
    
    // Create new iframe and cache it
    console.log(`🆕 Creating new iframe for "${pageId}"`);
    const iframe = document.createElement('iframe');
    iframe.src = iframeConfig.src;
    iframe.title = iframeConfig.title;
    iframe.allow = iframeConfig.allow || '';
    iframe.style.cssText = iframeConfig.style || '';
    iframe.onload = iframeConfig.onload || null;
    
    // Cache the iframe
    cachedIframes.set(cacheKey, iframe);
    
    return iframe;
}

function insertCachedIframe(containerId, pageId, iframeConfig) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.warn(`Container "${containerId}" not found for iframe insertion`);
        return;
    }
    
    // Remove any existing iframe in the container
    const existingIframe = container.querySelector('iframe');
    if (existingIframe) {
        existingIframe.remove();
    }
    
    const cacheKey = `${pageId}-iframe`;
    
    // Check if we have a cached iframe that's already loaded
    if (cachedIframes.has(cacheKey)) {
        const cachedIframe = cachedIframes.get(cacheKey);
        console.log(`📦 Reusing cached iframe for "${pageId}" - no reload needed!`);
        
        // Move the cached iframe to the container (this preserves all state)
        if (cachedIframe.parentNode) {
            cachedIframe.parentNode.removeChild(cachedIframe);
        }
        container.appendChild(cachedIframe);
        
        // Hide loading immediately since iframe is already loaded
        setTimeout(() => {
            if (typeof hideLoading === 'function') {
                hideLoading();
            }
        }, 50);
        
        return;
    }
    
    // Create new iframe for first time
    console.log(`🆕 Creating new iframe for "${pageId}"`);
    const iframe = document.createElement('iframe');
    iframe.src = iframeConfig.src;
    iframe.title = iframeConfig.title;
    iframe.allow = iframeConfig.allow || '';
    iframe.style.cssText = iframeConfig.style || '';
    
    // Set up onload handler
    iframe.onload = () => {
        console.log(`✅ Iframe loaded for "${pageId}" - now cached for future use`);
        if (typeof hideLoading === 'function') {
            hideLoading();
        }
    };
    
    // Cache the iframe
    cachedIframes.set(cacheKey, iframe);
    
    // Add to container
    container.appendChild(iframe);
    console.log(`✅ New iframe inserted and cached for "${pageId}"`);
}

function preloadRelatedPages(currentPageId) {
    // Intelligent preloading based on user navigation patterns
    const relatedPages = {
        'home': ['stock-locator', 'locations'],
        'stock-locator': ['locations', 'home'],
        'locations': ['stock-locator', 'contact'],
        'about': ['contact', 'home'],
        'contact': ['about', 'privacy'],
        'privacy': ['contact', 'about']
    };
    
    const related = relatedPages[currentPageId] || [];
    console.log(`🔗 Preloading related pages for "${currentPageId}":`, related);
    
    related.forEach((pageId, index) => {
        setTimeout(() => {
            if (!preloadedContent.has(pageId)) {
                preloadPageContent(pageId, index + 1);
            }
        }, index * 50); // Much faster staggering - 50ms between pages
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Load shared components
    loadSharedComponents();
    
    // Initialize dark mode
    initializeDarkMode();
    
    // Check if we're in development (localhost) or production
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    console.log('Environment detection - isLocalhost:', isLocalhost);
    
    let initialPage = 'home';
    
    if (isLocalhost) {
        // For localhost, always start with home page
        // No hash-based routing to avoid conflicts
        console.log('Localhost detected - starting with home page');
        
    } else {
        // For production, use clean URLs
        const path = window.location.pathname;
        const cleanPath = path.replace(/\.html$/, '').replace(/\/$/, '');
        console.log('Production path:', path, 'cleanPath:', cleanPath);
        
        if (cleanPath === '' || cleanPath === '/index') {
            initialPage = 'home';
        } else if (cleanPath === '/stock-locator') {
            initialPage = 'stock-locator';
        } else if (cleanPath === '/locations') {
            initialPage = 'locations';
        } else if (cleanPath === '/about') {
            initialPage = 'about';
        } else if (cleanPath === '/contact') {
            initialPage = 'contact';
        } else if (cleanPath === '/privacy') {
            initialPage = 'privacy';
        } else {
            // Handle 404 - redirect to home
            console.warn('Unknown path:', path, '- redirecting to home');
            window.history.replaceState({ page: 'home' }, '', '/');
            initialPage = 'home';
        }
        console.log('Production initial page:', initialPage);
    }
    
    // Load initial page content
    console.log('Loading initial page:', initialPage);
    loadPageContent(initialPage);
    
    // Track initial page view
    setTimeout(() => trackPageView(initialPage), 1000);
    
    // Start preloading system immediately after initial page loads
    if (initialPage === 'home') {
        // Start preloading all pages immediately after homepage loads
        setTimeout(preloadAllPages, 100); // Just 100ms to let homepage settle
    } else {
        // If user landed on a specific page, preload everything immediately
        setTimeout(() => {
            preloadPageContent('home', 1);
            preloadAllPages(); // Preload all other pages too
        }, 100);
    }
    
    // Clean up old preloaded content periodically
    setInterval(clearOldPreloadedContent, 5 * 60 * 1000); // Every 5 minutes
    
    // Make functions globally available
    window.navigateToPage = navigateToPage;
});

// Hide loading overlay when iframe loads
function hideLoading() {
    console.log('hideLoading function called');
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        console.log('Loading overlay found, hiding...');
        setTimeout(() => {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 500);
        }, 1000);
    } else {
        console.warn('Loading overlay not found');
    }
}

// Handle iframe loading errors
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.querySelector('iframe');
    if (iframe) {
        iframe.addEventListener('error', function() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.innerHTML = `
                    <div style="text-align: center;">
                        <h3>Unable to load stock locator</h3>
                        <p>Please refresh the page or contact support if the issue persists.</p>
                        <button onclick="location.reload()" style="
                            background: white;
                            color: rgba(255, 0, 0, 1);
                            border: none;
                            padding: 0.5rem 1rem;
                            border-radius: 6px;
                            cursor: pointer;
                            margin-top: 1rem;
                        ">Refresh Page</button>
                    </div>
                `;
            }
        });
    }
});

// Mapbox Store Locator Implementation
let map;
let stores = [];
let markers = [];
let userLocation = null;
let userCountry = null;
let userRegion = null;
let nearestStore = null;

// Initialize the store locator when locations page is loaded
function initializeStoreLocator() {
    console.log('Initializing Mapbox store locator...');
    console.log('Available store data:', window.storeData);
    
    // Check if we have the store data
    if (typeof window.storeData === 'undefined') {
        console.error('Store data not loaded');
        return;
    }
    
    stores = window.storeData.features;
    console.log('Store features loaded:', stores.length, 'stores');
    
    // Get user's geographic location and customize experience
    getUserGeography();
    
    // Set Mapbox access token from environment variable or config
    mapboxgl.accessToken = window.MAPBOX_TOKEN || 'pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw'; // Fallback demo token
    
    // Check if dark mode is active to set initial map style
    const isDarkMode = document.body.classList.contains('dark-mode');
    const initialStyle = isDarkMode 
        ? 'mapbox://styles/mapbox/dark-v11' 
        : 'mapbox://styles/mapbox/streets-v12';
    
    // Initialize the map
    map = new mapboxgl.Map({
        container: 'map',
        style: initialStyle,
        center: [-0.3366, 53.7446], // Hull coordinates
        zoom: 12
    });
    
    // Add map controls
    map.addControl(new mapboxgl.NavigationControl());
    map.addControl(new mapboxgl.FullscreenControl());
    
    // Wait for the map to load
    map.on('load', function() {
        console.log('Map loaded, adding stores...');
        addStoresToMap();
        populateStoreList();
    });
}

function addStoresToMap() {
    // Create custom marker icons
    createCustomMarkerIcons();
    
    // Add each store as a custom marker
    stores.forEach(store => {
        const coordinates = store.geometry.coordinates;
        const properties = store.properties;
        
        // Create custom marker element
        const markerElement = createMarkerElement(properties);
        
        // Create marker and add to map
        const marker = new mapboxgl.Marker(markerElement)
            .setLngLat(coordinates)
            .addTo(map);
        
        // Add click event to marker with debugging
        markerElement.addEventListener('click', function(e) {
            console.log('Marker clicked!', properties.name);
            e.stopPropagation();
            showStorePopup(coordinates, properties);
            highlightStore(properties.id);
        });
        
        // Add visual feedback for testing
        markerElement.addEventListener('mouseenter', function() {
            console.log('Marker hovered:', properties.name);
        });
        
        markers.push(marker);
    });
}

function createCustomMarkerIcons() {
    // Add logo image to the map (option 1)
    map.loadImage('images/popattack-logo.png', function(error, image) {
        if (error) {
            console.warn('Logo image not found, using vending machine icon instead');
        } else {
            map.addImage('logo-marker', image);
        }
    });
}

function createMarkerElement(properties) {
    const markerDiv = document.createElement('div');
    markerDiv.className = 'custom-marker';
    
    // Traditional map marker style with logo (currently active)
    markerDiv.innerHTML = `
        <div class="marker-logo-traditional">
            <div class="logo-circle">
                <img src="images/popattack-logo.png" alt="Pop Attack" />
            </div>
        </div>
    `;
    
    // Alternative styles (commented out):
    
    // Option 1: Simple circular logo marker
    /*
    markerDiv.innerHTML = `
        <div class="marker-logo">
            <img src="images/popattack-logo.png" alt="Pop Attack" />
        </div>
    `;
    */
    
    // Option 2: Custom vending machine SVG icon
    /*
    markerDiv.innerHTML = `
        <div class="marker-icon vending-machine">
            <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Vending machine body -->
                <rect x="2" y="4" width="20" height="26" rx="2" fill="#FFD700" stroke="#FFA500" stroke-width="1"/>
                <!-- Screen -->
                <rect x="4" y="6" width="16" height="8" rx="1" fill="#000" opacity="0.8"/>
                <!-- Product slots -->
                <rect x="5" y="16" width="3" height="3" rx="0.5" fill="#FF6B6B"/>
                <rect x="10.5" y="16" width="3" height="3" rx="0.5" fill="#4ECDC4"/>
                <rect x="16" y="16" width="3" height="3" rx="0.5" fill="#45B7D1"/>
                <rect x="5" y="21" width="3" height="3" rx="0.5" fill="#96CEB4"/>
                <rect x="10.5" y="21" width="3" height="3" rx="0.5" fill="#FFEAA7"/>
                <rect x="16" y="21" width="3" height="3" rx="0.5" fill="#DDA0DD"/>
                <!-- Coin slot -->
                <rect x="19" y="10" width="1.5" height="0.5" rx="0.25" fill="#C0C0C0"/>
                <!-- Selection buttons -->
                <circle cx="6.5" cy="11" r="0.5" fill="#FFF" opacity="0.8"/>
                <circle cx="8.5" cy="11" r="0.5" fill="#FFF" opacity="0.8"/>
                <circle cx="10.5" cy="11" r="0.5" fill="#FFF" opacity="0.8"/>
                <!-- Base -->
                <rect x="1" y="29" width="22" height="2" rx="1" fill="#B8860B"/>
            </svg>
        </div>
    `;
    */
    
    return markerDiv;
}

function showStorePopup(coordinates, properties) {
    const popup = new mapboxgl.Popup()
        .setLngLat(coordinates)
        .setHTML(createPopupHTML(properties, coordinates))
        .addTo(map);
}

function createPopupHTML(properties, coordinates) {
    const hours = typeof properties.hours === 'string' ? JSON.parse(properties.hours || '{}') : (properties.hours || {});
    const products = typeof properties.products === 'string' ? JSON.parse(properties.products || '[]') : (properties.products || []);
    
    // Use location name if it's different from the name, otherwise just use name
    const displayName = properties.location && properties.location !== properties.name 
        ? properties.location 
        : properties.name;
    
    // Add distance info if available
    const distanceInfo = properties.distanceFromUser 
        ? `<p class="distance-info">📍 ${properties.distanceFromUser.toFixed(1)}km from you</p>` 
        : '';
    
    return `
        <div class="store-popup">
            <h3>${displayName}</h3>
            <p class="address">${properties.address}</p>
            ${distanceInfo}
            
            <div class="products">
                <strong>Available Products:</strong>
                <div class="product-tags">
                    ${products.slice(0, 3).map(product => `<span class="tag">${product}</span>`).join('')}
                </div>
            </div>
            
            <div class="popup-actions">
                <a href="/stock-locator" onclick="navigateToPage('stock-locator'); return false;" class="btn btn-primary">Check Stock</a>
                <button onclick="showDirections('${properties.id}', [${coordinates[0]}, ${coordinates[1]}])" class="btn btn-secondary">Get Directions</button>
            </div>
        </div>
    `;
}

function populateStoreList() {
    console.log('Populating store list...');
    const storeList = document.getElementById('store-list');
    if (!storeList) {
        console.error('Store list element not found!');
        return;
    }
    
    console.log('Store list element found, clearing and populating...');
    storeList.innerHTML = '';
    
    if (!stores || stores.length === 0) {
        console.error('No stores available to populate');
        storeList.innerHTML = '<div style="padding: 1rem; text-align: center; color: #666;">No stores found</div>';
        return;
    }
    
    stores.forEach((store, index) => {
        console.log(`Adding store ${index + 1}:`, store.properties.name);
        const storeElement = createStoreListItem(store.properties);
        storeList.appendChild(storeElement);
    });
    
    console.log(`Successfully added ${stores.length} stores to the list`);
}

function createStoreListItem(properties) {
    const div = document.createElement('div');
    div.className = 'store-item';
    div.dataset.storeId = properties.id;
    
    // Handle data - it might already be parsed objects or strings
    const hours = typeof properties.hours === 'string' ? JSON.parse(properties.hours || '{}') : (properties.hours || {});
    const products = typeof properties.products === 'string' ? JSON.parse(properties.products || '[]') : (properties.products || []);
    const features = typeof properties.features === 'string' ? JSON.parse(properties.features || '[]') : (properties.features || []);
    
    // Add distance info if available
    const distanceText = properties.distanceFromUser 
        ? `<p class="distance">📍 ${properties.distanceFromUser.toFixed(1)}km away</p>` 
        : '';
    
    div.innerHTML = `
        <div class="store-info">
            <h3>${properties.name}</h3>
            <p class="address">${properties.address}</p>
            <p class="location">${properties.location}</p>
            ${distanceText}
            
            <div class="store-details">
                <!-- Phone details removed -->
            </div>
            
            <div class="store-features">
                ${features.slice(0, 3).map(feature => `<span class="feature-tag">${feature}</span>`).join('')}
            </div>
            
            <div class="store-actions">
                <button onclick="focusStore('${properties.id}')" class="btn btn-secondary btn-sm">View on Map</button>
                <a href="/stock-locator" onclick="navigateToPage('stock-locator'); return false;" class="btn btn-primary btn-sm">Check Stock</a>
            </div>
        </div>
    `;
    
    // Add click event to highlight on map
    div.addEventListener('click', function() {
        focusStore(properties.id);
    });
    
    return div;
}

function focusStore(storeId) {
    const store = stores.find(s => s.properties.id === storeId);
    if (store) {
        map.flyTo({
            center: store.geometry.coordinates,
            zoom: 15
        });
        
        // Show popup
        setTimeout(() => {
            showStorePopup(store.geometry.coordinates, store.properties);
        }, 1000);
        
        // Highlight in sidebar
        highlightStore(storeId);
    }
}

function highlightStore(storeId) {
    // Remove existing highlights
    document.querySelectorAll('.store-item').forEach(item => {
        item.classList.remove('highlighted');
    });
    
    // Add highlight to selected store
    const storeElement = document.querySelector(`[data-store-id="${storeId}"]`);
    if (storeElement) {
        storeElement.classList.add('highlighted');
        storeElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function searchStores() {
    const searchTerm = document.getElementById('store-search').value.toLowerCase();
    const storeItems = document.querySelectorAll('.store-item');
    
    storeItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Toggle store panel functionality
function toggleStorePanel() {
    const panel = document.getElementById('floating-store-panel');
    const toggleText = document.getElementById('panel-toggle-text');
    
    if (panel.classList.contains('panel-open')) {
        panel.classList.remove('panel-open');
        toggleText.textContent = 'Show Stores';
    } else {
        panel.classList.add('panel-open');
        toggleText.textContent = 'Hide Stores';
    }
}

// Integrated Directions Functionality
let directionsControl = null;
let currentRoute = null;

function showDirections(storeId, coordinates) {
    // Get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLocation = [position.coords.longitude, position.coords.latitude];
                getDirections(userLocation, coordinates);
            },
            (error) => {
                // Fallback: use map center or ask user to click
                console.warn('Location access denied:', error);
                showLocationPrompt(coordinates);
            }
        );
    } else {
        showLocationPrompt(coordinates);
    }
}

function showLocationPrompt(destination) {
    // Create a simple prompt for users to click their starting point
    alert('Please click on the map to set your starting location, then we\'ll show directions to the store.');
    
    // Add click listener to map
    map.once('click', (e) => {
        const userLocation = [e.lngLat.lng, e.lngLat.lat];
        getDirections(userLocation, destination);
    });
}

function getDirections(start, end) {
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?geometries=geojson&access_token=${mapboxgl.accessToken}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.routes && data.routes.length > 0) {
                displayRoute(data.routes[0]);
                showDirectionsPanel(data.routes[0]);
            } else {
                alert('Could not find directions to this location.');
            }
        })
        .catch(error => {
            console.error('Directions error:', error);
            alert('Error getting directions. Please try again.');
        });
}

function displayRoute(route) {
    // Remove existing route if any
    if (currentRoute) {
        map.removeLayer('route');
        map.removeSource('route');
    }
    
    // Add route to map
    map.addSource('route', {
        type: 'geojson',
        data: {
            type: 'Feature',
            properties: {},
            geometry: route.geometry
        }
    });
    
    map.addLayer({
        id: 'route',
        type: 'line',
        source: 'route',
        layout: {
            'line-join': 'round',
            'line-cap': 'round'
        },
        paint: {
            'line-color': '#FF0000',
            'line-width': 6
        }
    });
    
    currentRoute = route;
    
    // Fit map to show entire route
    const coordinates = route.geometry.coordinates;
    const bounds = coordinates.reduce((bounds, coord) => {
        return bounds.extend(coord);
    }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
    
    map.fitBounds(bounds, { padding: 50 });
}

function showDirectionsPanel(route) {
    // Create directions panel
    const directionsPanel = document.createElement('div');
    directionsPanel.className = 'directions-panel';
    directionsPanel.innerHTML = `
        <div class="directions-header">
            <h3>🚗 Directions</h3>
            <button onclick="closeDirections()" class="close-directions">×</button>
        </div>
        <div class="directions-summary">
            <p><strong>Distance:</strong> ${(route.distance / 1000).toFixed(1)} km</p>
            <p><strong>Duration:</strong> ${Math.round(route.duration / 60)} minutes</p>
        </div>
        <div class="directions-steps">
            ${route.legs[0].steps.map((step, index) => `
                <div class="direction-step">
                    <span class="step-number">${index + 1}</span>
                    <span class="step-instruction">${step.maneuver.instruction}</span>
                    <span class="step-distance">${(step.distance / 1000).toFixed(1)} km</span>
                </div>
            `).join('')}
        </div>
    `;
    
    // Remove existing directions panel
    const existingPanel = document.querySelector('.directions-panel');
    if (existingPanel) {
        existingPanel.remove();
    }
    
    // Add to page
    document.body.appendChild(directionsPanel);
}

function closeDirections() {
    // Remove directions panel
    const panel = document.querySelector('.directions-panel');
    if (panel) {
        panel.remove();
    }
    
    // Remove route from map
    if (currentRoute) {
        try {
            map.removeLayer('route');
            map.removeSource('route');
        } catch (e) {
            console.log('Route already removed');
        }
        currentRoute = null;
    }
}

// Dark Mode Functionality
function toggleDarkMode() {
    const body = document.body;
    const isDarkMode = body.classList.contains('dark-mode');
    
    if (isDarkMode) {
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
        updateToggleText(false);
        updateMapStyle(false);
    } else {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
        updateToggleText(true);
        updateMapStyle(true);
    }
}

function updateToggleText(isDarkMode) {
    const toggleIcon = document.querySelector('.toggle-icon');
    const toggleText = document.querySelector('.toggle-text');
    
    if (toggleIcon) {
        toggleIcon.textContent = isDarkMode ? '☀️' : '🌙';
    }
    
    if (toggleText) {
        toggleText.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
    }
}

function updateMapStyle(isDarkMode) {
    // Only update map style if map exists (on locations page)
    if (typeof map !== 'undefined' && map) {
        const newStyle = isDarkMode 
            ? 'mapbox://styles/mapbox/dark-v11' 
            : 'mapbox://styles/mapbox/streets-v12';
        map.setStyle(newStyle);
    }
}

function initializeDarkMode() {
    const savedDarkMode = localStorage.getItem('darkMode');
    
    // Default to light mode - only apply dark mode if explicitly saved as true
    if (savedDarkMode === 'true') {
        document.body.classList.add('dark-mode');
        updateToggleText(true);
        updateMapStyle(true);
    } else {
        // Default to light mode
        document.body.classList.remove('dark-mode');
        updateToggleText(false);
        updateMapStyle(false);
    }
}

// Geographic Enhancement Functions
function getUserGeography() {
    console.log('🌍 Getting user geography for enhanced navigation...');
    
    // First try HTML5 Geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                console.log('📍 User location obtained:', userLocation);
                
                // Get more detailed geographic information
                getLocationDetails(userLocation);
                
                // Find nearest store and enhance experience
                findNearestStore();
                enhanceNavigationWithGeography();
            },
            (error) => {
                console.warn('⚠️ Geolocation denied or failed:', error.message);
                // Fallback to IP-based location
                getIPBasedLocation();
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            }
        );
    } else {
        console.warn('⚠️ Geolocation not supported, using IP fallback');
        getIPBasedLocation();
    }
}

function getIPBasedLocation() {
    // Use a free IP geolocation service as fallback
    fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
            userLocation = {
                lat: data.latitude,
                lng: data.longitude
            };
            userCountry = data.country_name;
            userRegion = data.region;
            
            console.log('🌐 IP-based location:', {
                location: userLocation,
                country: userCountry,
                region: userRegion
            });
            
            findNearestStore();
            enhanceNavigationWithGeography();
        })
        .catch(error => {
            console.warn('⚠️ IP geolocation failed:', error);
            // Use default UK location (Hull)
            userLocation = { lat: 53.7446, lng: -0.3366 };
            userCountry = 'United Kingdom';
            userRegion = 'England';
            findNearestStore();
        });
}

function getLocationDetails(location) {
    // Use Mapbox Geocoding API to get detailed location info
    const accessToken = window.MAPBOX_TOKEN;
    if (!accessToken) return;
    
    fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${location.lng},${location.lat}.json?access_token=${accessToken}&types=country,region`)
        .then(response => response.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                data.features.forEach(feature => {
                    if (feature.place_type.includes('country')) {
                        userCountry = feature.text;
                    }
                    if (feature.place_type.includes('region')) {
                        userRegion = feature.text;
                    }
                });
                
                console.log('🏠 Detailed location:', {
                    country: userCountry,
                    region: userRegion
                });
            }
        })
        .catch(error => console.warn('Location details fetch failed:', error));
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    // Haversine formula for calculating distance between coordinates
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c; // Distance in kilometers
}

function findNearestStore() {
    if (!userLocation || stores.length === 0) return;
    
    let minDistance = Infinity;
    let closest = null;
    
    stores.forEach(store => {
        const [storeLng, storeLat] = store.geometry.coordinates;
        const distance = calculateDistance(
            userLocation.lat, userLocation.lng,
            storeLat, storeLng
        );
        
        // Add distance to store properties for later use
        store.properties.distanceFromUser = distance;
        
        if (distance < minDistance) {
            minDistance = distance;
            closest = store;
        }
    });
    
    nearestStore = closest;
    console.log(`🎯 Nearest store: ${closest?.properties.name} (${minDistance.toFixed(1)}km away)`);
    
    // Sort stores by distance for enhanced listings
    stores.sort((a, b) => (a.properties.distanceFromUser || Infinity) - (b.properties.distanceFromUser || Infinity));
}

function enhanceNavigationWithGeography() {
    // Update map center to user location if very close to a store
    if (nearestStore && nearestStore.properties.distanceFromUser < 50) {
        console.log('🎯 User is near a store, centering map on user location');
        if (map && userLocation) {
            map.flyTo({
                center: [userLocation.lng, userLocation.lat],
                zoom: 14,
                duration: 2000
            });
        }
    }
    
    // Add regional content customization
    customizeContentByRegion();
    
    // Update store list to show distances
    if (document.querySelector('.store-list')) {
        populateStoreList();
    }
    
    // Track geography in Google Analytics
    if (userCountry) {
        trackCustomEvent('user_location_detected', 'Geography', userCountry);
        
        // Also track region as separate event
        if (userRegion) {
            trackCustomEvent('user_region_detected', 'Geography', userRegion);
        }
    }
}

function customizeContentByRegion() {
    // Customize content based on user's country/region
    if (userCountry && userCountry !== 'United Kingdom') {
        // Show international shipping info for non-UK users
        const ctaSection = document.querySelector('.cta-section p');
        if (ctaSection && !ctaSection.textContent.includes('International')) {
            ctaSection.textContent += ' (International shipping available!)';
        }
        
        console.log(`🌍 International user detected from ${userCountry}, showing international options`);
    }
    
    // Add nearest store info to homepage if user is close
    if (nearestStore && nearestStore.properties.distanceFromUser < 25) {
        const heroSection = document.querySelector('.hero-content');
        if (heroSection && !document.querySelector('.nearest-store-info')) {
            const nearestInfo = document.createElement('div');
            nearestInfo.className = 'nearest-store-info';
            nearestInfo.innerHTML = `
                <p class="nearest-store-text">
                    📍 Your nearest vending machine: <strong>${nearestStore.properties.name}</strong> 
                    (${nearestStore.properties.distanceFromUser.toFixed(1)}km away)
                    <button onclick="navigateToPage('locations'); setTimeout(() => focusStore('${nearestStore.properties.id}'), 500);" class="btn-link">
                        View Location →
                    </button>
                </p>
            `;
            heroSection.appendChild(nearestInfo);
        }
    }
}

// Make functions globally available
window.initializeStoreLocator = initializeStoreLocator;
window.searchStores = searchStores;
window.focusStore = focusStore;
window.toggleStorePanel = toggleStorePanel;
window.toggleDarkMode = toggleDarkMode;
window.showDirections = showDirections;
window.closeDirections = closeDirections;