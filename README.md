# Adverto Master Plugin

![Adverto Master Plugin Banner](assets/images/advertomedia-banner.png)

A comprehensive AI-powered marketing toolkit that combines four powerful tools into one beautiful, cohesive WordPress plugin with Go## 🏆 Credits & Acknowledgements

- **Design Inspiration**: Google Material Design System
- **Icons**: Material Icons by Google
- **AI Technology**: Powered by OpenAI's GPT-4o model
- **Framework**: Built on WordPress coding standards
- **Typography**: Roboto font family by Google
- **Colour Palette**: Custom Adverto brand colours
- **Development**: Adverto Media team

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Adverto Media

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 🌟 About Adverto Media

**Adverto Media** is a digital marketing agency specialising in AI-powered solutions for WordPress websites. We create tools that help businesses improve their SEO, user experience, and conversion rates through intelligent automation.

**Made with ❤️ by the Adverto Media Team**

*Transform your WordPress site with the power of AI and beautiful design.*

---

**Plugin Version**: 1.0.0  
**Tested up to**: WordPress 6.3  
**Requires PHP**: 7.4+  
**Last Updated**: August 2025sign inspired interface.

## 🎯 Features

### 🖼️ **Alt Text Generator AI** ✅
- Bulk generate AI-powered alt texts for images
- Uses OpenAI's GPT-4o model for accurate descriptions
- Beautiful interface with image previews and progress tracking
- Batch processing with real-time status updates
- SEO-optimized alt text generation with customizable prompts
- Support for various image formats and sizes

### 🚀 **SEO Generator AI** ✅
- AI-powered SEO title and meta description generation
- Bulk processing for multiple pages and posts
- Yoast SEO integration for seamless workflow
- Keyword optimization and character count validation
- Custom prompt configuration for better results
- Real-time preview and editing capabilities

### 📱 **Side Tab Manager** ✅
- Fully customizable floating side navigation tabs
- Contact forms, phone numbers, and call-to-actions
- Drag-and-drop reordering with visual feedback
- Multiple positioning options (left/right)
- Icon upload support with WordPress media library
- Responsive design with smooth animations
- Toggle activation with modern switch UI
- Click tracking and analytics

### 📄 **Duplicate SEO Wizard** ✅
- Intelligent page duplication with AI-powered content
- **Multi-Page Location Duplicator** - Create multiple location pages instantly
- Find and replace functionality with smart suggestions
- SEO metadata preservation and optimization
- Perfect for location-based businesses
- Draft mode for review before publishing
- Bulk operations with progress tracking

## 🎨 Beautiful Design

- **Google Material Design** inspired interface with custom Adverto branding
- **Responsive** and mobile-friendly across all devices
- **Smooth animations** and transitions using CSS3
- **Accessible** and user-friendly with ARIA compliance
- **Custom colour scheme** with Adverto brand colours
- **British English** throughout the interface

## ⚡ Quick Start

1. **Install the plugin** - Upload and activate through WordPress admin
2. **Configure your OpenAI API key** - Go to Adverto Master > Settings
3. **Test API connection** - Use the built-in API tester
4. **Start using tools**:
   - Generate alt texts for your images
   - Create SEO titles and descriptions
   - Set up side navigation tabs
   - Duplicate pages with location data

## 🔧 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- OpenAI API key (for AI features)
- Modern browser with JavaScript enabled

## 📖 Documentation

### Getting Your OpenAI API Key

1. Visit [OpenAI Platform](https://platform.openai.com/account/api-keys)
2. Sign in or create an account
3. Generate a new API key
4. Copy and paste it into Adverto Master > Settings
5. Test the connection using the built-in API tester

### Using the Alt Text Generator AI

1. Navigate to **Adverto Master > Alt Text Generator AI**
2. Click **Select Images** to choose from your media library
3. Configure generation settings and custom prompts
4. Click **Generate Alt Texts** to start AI analysis
5. Review generated descriptions with image previews
6. Edit any alt texts as needed
7. Click **Save All Alt Texts** to apply changes

### Using the SEO Generator AI

1. Go to **Adverto Master > SEO Generator AI**
2. Select pages/posts to generate SEO content for
3. Choose between titles, descriptions, or both
4. Review and customize the generated content
5. Save directly to your posts with Yoast SEO integration

### Using the Side Tab Manager

1. Access **Adverto Master > Side Tab Manager**
2. Toggle the side tab activation switch
3. Configure appearance (colours, position)
4. Add navigation items with icons and links
5. Drag and drop to reorder items
6. Preview changes in real-time

### Using the Duplicate SEO Wizard

1. Visit **Adverto Master > Duplicate SEO Wizard**
2. Choose between standard duplication or Multi-Page Location Duplicator
3. Select source page and configure settings
4. For location duplicator: add multiple locations in CSV format
5. Review generated pages in draft mode
6. Publish when satisfied with results

## 🛠️ Development

### Plugin Architecture

```
adverto-master-plugin/
├── adverto-master-plugin.php     # Main plugin file with metadata
├── includes/                     # Core functionality classes
│   ├── class-adverto-core.php          # Plugin core and hook loader
│   ├── class-adverto-activator.php     # Activation handler
│   ├── class-adverto-deactivator.php   # Deactivation handler
│   ├── class-adverto-i18n.php          # Internationalization
│   ├── class-adverto-loader.php        # Hook loader system
│   ├── class-adverto-public.php        # Frontend functionality
│   ├── class-alt-text-generator.php    # Alt text AI processing
│   ├── class-seo-generator.php         # SEO content AI processing
│   ├── class-side-tab.php             # Side tab functionality
│   └── class-duplicate-wizard.php      # Page duplication logic
├── admin/                        # Admin interface
│   ├── class-adverto-admin.php         # Admin core functionality
│   ├── css/
│   │   └── adverto-admin.css          # Material Design styles
│   ├── js/
│   │   └── adverto-admin.js           # Admin JavaScript
│   └── views/                          # Admin page templates
│       ├── dashboard.php              # Main dashboard
│       ├── settings.php               # Settings page
│       ├── alt-text-generator.php     # Alt text tool interface
│       ├── seo-generator.php          # SEO generator interface
│       ├── side-tab-manager.php       # Side tab configuration
│       └── duplicate-wizard.php       # Duplication tool interface
└── assets/                       # Public assets
    ├── css/
    │   └── adverto-public.css         # Frontend styles
    ├── js/
    │   └── adverto-public.js          # Frontend JavaScript
    └── images/                        # Plugin graphics and icons
        ├── adverto-icon.svg
        ├── advertologo.svg
        └── advertomedia-banner.png
```

### Technical Features

- **Modular Architecture**: Each tool is a separate class with its own functionality
- **WordPress Standards**: Follows WordPress coding standards and best practices
- **AJAX Processing**: Asynchronous operations with progress tracking
- **Security**: Nonce verification and capability checks throughout
- **Responsive Design**: Mobile-first approach with Material Design principles
- **Extensibility**: Hook system allows for easy customization and extension
- **British Localisation**: All text strings use British English spelling

### API Integration

The plugin integrates with:
- **OpenAI API**: GPT-4o model for content generation
- **WordPress Media Library**: For image processing and icon uploads
- **Yoast SEO**: Direct integration for SEO metadata
- **WordPress REST API**: For AJAX operations and data handling

### Customisation

The plugin is built with extensibility in mind:

- **Custom AI Prompts**: Modify prompts for each tool in settings
- **Brand Customisation**: Upload custom logos and adjust colour schemes
- **Material Design Theming**: Customise colours and spacing variables
- **Hook System**: Extend functionality through WordPress actions and filters
- **CSS Variables**: Easy theme customisation through CSS custom properties
- **Translation Ready**: Full support for WordPress translation system

## 🎯 Changelog

### Version 1.0.0 - Current Release
- ✅ Complete Alt Text Generator AI with bulk processing
- ✅ Complete SEO Generator AI with Yoast integration  
- ✅ Complete Side Tab Manager with drag-and-drop interface
- ✅ Complete Duplicate SEO Wizard with Multi-Page Location Duplicator
- ✅ Material Design interface with Adverto branding
- ✅ British English localisation throughout
- ✅ Comprehensive API testing and debugging tools
- ✅ Custom logo upload and brand customisation
- ✅ Responsive design for all screen sizes
- ✅ AJAX-powered operations with progress tracking

### Upcoming Features
- 🔄 Advanced analytics and click tracking
- 🔄 Scheduled bulk operations
- 🔄 Multi-language content generation
- 🔄 Integration with Google Analytics
- 🔄 Custom post type support
- 🔄 White-label options for agencies

## 💝 Support & Resources

- **Documentation**: [Complete plugin documentation](https://adverto.com/docs)
- **Video Tutorials**: [YouTube channel](https://youtube.com/@advertomedia)
- **Support Forum**: [Community support](https://adverto.com/support)
- **Email Support**: support@adverto.com
- **Feature Requests**: [GitHub Issues](https://github.com/noahung/adverto-master-plugin/issues)

## 🚀 Performance

- **Lightweight**: Optimised code with minimal database queries
- **Async Processing**: Non-blocking AJAX operations
- **Efficient API Usage**: Smart batching to minimise OpenAI API costs
- **Caching**: Built-in caching for improved performance
- **Mobile Optimised**: Fast loading on all devices

## 🔒 Security

- **Nonce Verification**: All AJAX requests protected with WordPress nonces
- **Capability Checks**: Proper user permission validation
- **Data Sanitisation**: All inputs sanitised and validated
- **Secure API Handling**: OpenAI API keys encrypted in database
- **SQL Injection Protection**: Prepared statements throughout

## 🏆 Credits

- **Design**: Inspired by Google Material Design
- **Icons**: Material Icons by Google
- **AI**: Powered by OpenAI's GPT models
- **Framework**: Built on WordPress standards

## 📄 License

This plugin is licensed under the GPL v2 or later.

---

**Made with ❤️ by the Advertomedia Team**

*Transform your WordPress site with the power of AI and beautiful design.*
