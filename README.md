# Helsinki Private Website
A plugin designed for the Helsinki theme to implement Intranet and Extranet websites.

Requires users to login to view website content. Features also protecting media files on the website.

## Dependencies
Depends on the Helsinki Theme for some functions, and the HDS-plugin for styles.

### Required
- [Helsinki Theme](https://github.com/City-of-Helsinki/wordpress-helfi-helsinkiteema): Depends on some theme functions for displaying the login page.
- [Helsinki WordPress plugin](https://github.com/City-of-Helsinki/wordpress-helfi-hds-wp): Depends on HDS styles for properly displaying the login page.

## Configuration
Upon installation the plugin creates a new custom userrole `Helsinki Private User`, which is intended to be used for users logging in to the website. This userrole does not have to be used with the plugin, but it hides the adminbar for logged in users.

By default the frontend login will be enabled automatically on plugin activation. At this point the plugin is ready to be used as is.

Restriction to media files is enabled alongside the login page. This means enabling the frontend login will also enable restriction to media files, and vice versa. 

Further configuration to the login page can be performed in the `Helsinki Private Website` admin submenu, such as adding additional information to the login page or disabling/re-enabling the login page.

## Conflicts
Because the plugin controls access to media files, any other plugin that is also intended for restricting media file -access will most likely conflict with this plugin. It is recommended to disable and/or remove any such plugins before installing this one.

### Assets
(S)CSS and JS source files are stored in `/src`. Asset complitation is done with [Gulp](https://gulpjs.com/) and the processed files can be found in `/assets`.

Install dependencies with `npm install`. Build assets with `gulp scripts` and `gulp styles` or watch changes with `gulp watch`.

## Collaboration
Raise [issues](https://github.com/City-of-Helsinki/wordpress-helfi-linkedevents/issues) for found bugs or development ideas. Feel free to send [pull requests](https://github.com/City-of-Helsinki/wordpress-helfi-linkedevents/pulls) for bugfixes and new or improved features.