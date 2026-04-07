# Pop Attack UK Website

A modern, responsive website for Pop Attack UK, a collectible card game vending machine business delivering premium technology to the UK.

## 🏗️ Project Structure

```
webdev/
├── index.html              # Main homepage
├── robots.txt              # Robots directives
├── sitemap.xml             # XML sitemap
├── stock-locator.html      # Stock locator page
├── about.html              # About us page
├── contact.html            # Contact information
├── privacy.html            # Privacy policy
├── css/
│   └── styles.css          # All website styles
├── js/
│   └── main.js             # JavaScript functionality
├── images/
│   └── logo-placeholder.txt # Logo placeholder instructions
└── components/
    ├── header.html          # Reusable header component
    └── footer.html          # Reusable footer component
```

## 🚀 Features

- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Modern UI**: Clean, professional design with smooth animations
- **SEO Optimized**: Proper meta tags and semantic HTML structure
- **Fast Loading**: Optimized CSS and JavaScript
- **Accessibility**: Proper alt tags and semantic markup
- **Cross-browser Compatible**: Works in all modern browsers

## 🎨 Design System

- **Primary Color**: Red (#FF0000) - Brand color
- **Secondary Colors**: White, light gray (#f8f9fa), dark gray (#212529)
- **Typography**: System fonts (Apple system, Segoe UI, Roboto, etc.)
- **Layout**: CSS Grid and Flexbox for responsive layouts
- **Components**: Reusable cards, buttons, and sections

## 📱 Pages Overview

### Homepage (`index.html`)
- Hero section with main value proposition
- Feature cards highlighting key benefits
- Call-to-action sections
- Company information

### Stock Locator (`stock-locator.html`)
- Real-time stock locator iframe
- Information about premium vending technology
- Product categories and benefits
- **About**: Detailed company information
- **Contact**: Contact details and support information
- **Privacy**: Privacy policy and legal information

## 🛠️ Technical Details

### CSS Structure
- Reset and base styles
- Header and navigation styles
- Component styles (cards, buttons, sections)
- Responsive design with mobile-first approach
- Custom scrollbar styling

### JavaScript Features
- Page navigation system (for future SPA functionality)
- Loading overlay management for iframes
- Error handling for external content
- Responsive navigation enhancements

### External Dependencies
- **Stock Locator**: Embedded Retool iframe for real-time inventory
- **Discord**: External Discord server link
- **Logo**: Currently using placeholder Imgur URL

## 🔧 Customization

### Adding Your Logo
1. Place your logo file in the `images/` directory
2. Update all HTML files to reference your local logo file
3. Recommended format: PNG or SVG, 200x80px minimum

### Modifying Colors
Edit the CSS variables in `css/styles.css`:
```css
:root {
    --primary-color: #FF0000;
    --secondary-color: #212529;
    --background-color: #f8f9fa;
}
```

### Adding New Pages
1. Create new HTML file in root directory
2. Include header and footer components using placeholder divs
3. Add navigation links in header component
4. Update JavaScript for page-specific functionality
5. Add URL rewrite rules to `.htaccess` for clean URLs

### URL Structure
The website uses clean URLs without `.html` extensions:
- `/` → Home page
- `/stock-locator` → Stock locator
- `/about` → About page
- `/contact` → Contact page
- `/privacy` → Privacy page

### Server Configuration
The `.htaccess` file handles:
- Clean URL rewriting
- Removal of `.html` extensions
- Redirects from old URLs to clean URLs
- Compression and caching

## 📊 Performance

- **CSS**: Minified and optimized (511 lines)
- **JavaScript**: Efficient and modular (109 lines)
- **Images**: Optimized loading with proper alt tags
- **External Resources**: Minimal dependencies

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📝 Content Management

### Updating Stock Information
- Stock levels are managed through the embedded Retool iframe
- No manual updates required for inventory
- Real-time synchronization with vending machines

### Adding New Content
- Edit HTML files directly
- CSS changes go in `css/styles.css`
- JavaScript functionality in `js/main.js`

## 🔒 Security

- No sensitive data stored locally
- External links open in new tabs
- Form submissions handled securely
- Privacy policy compliant with UK regulations

## 📞 Support

For technical support or questions:
- **Email**: chris@vendables.com
- **Phone**: 033 078 90801
- **Discord**: https://discord.gg/popattack

## 📄 License

© 2025 Pop Attack UK LTD. All rights reserved.

---

**Pop Attack UK** - Delivering premium vending machine technology to UK collectors! 🇬🇧 