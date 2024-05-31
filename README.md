# Color Labeling Posts and Pages

A WordPress plugin to color-label rows for Posts and Pages in the WordPress Dashboard.

## Description

This plugin allows you to tag or color-label the individual rows of Pages and Posts in the WordPress Dashboard, similar to how layers are color-labeled in Photoshop.

**Features:**
- Color-label rows under the "All Posts" screen.
- Color-label rows under the "All Pages" screen.
- Enter custom hexadecimal color codes.
- Close the color picker without selecting a color.
- Seamlessly integrates into the WordPress admin interface.

**Benefits:**
- Improved organization of posts and pages.
- Quick visual identification of different content items.
- Enhanced workflow efficiency.

## Installation

1. Upload the `color-labeling-posts-and-pages` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to the "Posts" or "Pages" sections in the WordPress Dashboard.
4. Hover over a post or page title and click the "Color-Label" link.
5. Use the color picker interface to select a predefined color or enter a custom color.

## Usage

- Hover over a post/page title and click the \"Color Label\" link.
- Select a color from the predefined options or enter a custom color code.

## Frequently Asked Questions

**How do I change the colors?**

You can select from predefined colors or enter a custom hexadecimal color code using the color picker interface.

**Can I remove a color label?**

Yes, but just in the way of replacing a color with white or very light grey, which are default WordPress colors for these rows of pages and posts. When deactivating this plugin, all color labels done with this plugin will vanish.

## Screenshots

![Color Picker Interface](assets/screenshot.jpg)

## Changelog

**1.1.3**
- Took out faulty 'reset' button / functionality. As simple alternative solution, kept the 2 default / original background colors for these striped rows: A very light grey #f6f7f7 and white #ffffff. Exactly equal to the default striped row values in WordPress site's Pages and Posts pages. 

- Much of this code is actually from 1.0 because that code worked fine in the way of keeping color labels saved in Posts and Pages pages, when navigating away and then navigating back. Somehow this stopped working properly in v 1.1.2

### 1.0
- Initial release.

## License

This plugin is licensed under the GPLv2 or later.

## Donations

If you find this plugin useful, consider supporting its development with a donation. [Donate here](https://www.venmo.com/u/bridean77).

