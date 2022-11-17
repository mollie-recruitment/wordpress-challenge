# Fnugg Resort

---

A block to display up-to-date weather information for resorts within the Fnugg organization for WordPress version 6.1 or later.

---

The Fnugg Resort block allows your editors the ability to add any resorts they wish to posts or pages, displaying up to date
weather information to site visitors for the selected resorts.

Relying on the Fnugg API for data, the block will display the following information:
- The resort name
- An image of the resort
- The current temperature
- The current wind conditions
- The current snow conditions

## Contributing

Contributions are always welcome, no matter how large or small. You will find all the files needed to build, adn develop the plugin further included.

### Requirements
You will need `node` with the `npm` package manager installed on your machine to build the plugin.

You may also use the bundled `wp-env` environment, if you have Docker installed, if not, you can of course use your local development environment of choice.

### Building

During development, you will want to use `npm run start` to continually build any changes, and `npm run build` to build the plugin for production.

If you wish to use the included development environment, which relies on Docker, `npm run env start` will get you up and running, while `npm run env stop` will stop the environment.
