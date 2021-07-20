# greybox 2.0 - Vue frontend

This branch of greybox 2.0 serves as the frontend web application that allows users to interact with Lumen backend API.

## Environments
This project runs in 3 environments - development, debug and production.

- **Development** should be used for local development only
- **Debug** should be used for private debug version of the server
- **Production** should be used for publicly available live version of the server

Main difference is that only **production** has Vue devtools turned off and uses live API and only **development** uses history hash mode.

Configuration can be changed in `.env.{environment name}` files in root folder. Current mode will be visible next to logo in navbar (except for production).

Current environment can be detected in code by global process constant `this.env.VUE_APP_MODE` in `.vue` files or `process.env.VUE_APP_MODE` in `.js` files.

#### Modes
Furthermore, this project uses two modes - `normal` and `PDS` with tiny differences amongst each other (filtering events etc.). 

Default mode is normal. Configurations for PDS `development` and `production` can be edited through `.env.pds-dev` or `.env.pds-prod` respectively. Debug version is not available for PDS.

Current mode can be detected in code by global boolean constant `this.$isPDS` in `.vue` files or `Vue.prototype.$isPDS` in `.js` files.

## Development process
- Minor changes should be done on `vue-frontend-dev` branch
- Major change should be done on separate branch (e. g. `vue-frontend-password-reset`) and merged into `vue-frontend-dev` later
- Once development branch is deployed on development server and fully tested, it should be merged into `vue-frontend-prod`
- New version should be released for every push to `vue-frontend-prod` branch (see bellow)

## Intial setup
This project requires [Node.js](https://nodejs.org/) and [npm](https://www.npmjs.com/) to build the code.

Once you have Node.js and npm installed on your computer, run `npm install` to install dependencies.

## Building code
#### Development
Use variations of `npm run serve` command for different environments:
- `serve` for **development** environment of **normal** mode
- `serve:debug` for **debug** environment of **normal** mode
- `serve:prod` for **production** environment of **normal** mode
- `serve:pds` for **development** environment of **PDS** mode

#### Production
Use variations of `npm run build` command for different environments:
- `build` for **production** environment of **normal** mode
- `build:debug` for **debug** environment of **normal** mode
- `build:dev` for **development** environment of **normal** mode
- `build:pds` for **production** environment of **PDS** mode


## Code style
Greybox uses `@vue/prettier` and `plugin:vue/essential` extensions of ESLint. You can lint your code manually by running

```
npm run lint
```
It is recommended to set git hook to automatically lint your code before committing. All commited code should be linted!

## Releasing versions
After every merge into `vue-frontend-prod` branch, a new release should be made for production deployment.

#### Preparation
- Obtain rights to publish new releases to repository on GitHub
- Generate new GitHub token in [administration](https://github.com/settings/tokens)
- Add this token to `.GIT_TOKEN` file located in root folder
    - This token is used only as a header in requests to GitHub API

#### Releasing
- Run `./release.sh` script
- Fill in requested information - version number (using [semantic versioning](https://semver.org/)), release title and release description
- Wait for the script to make builds and upload release

## Deployment
The folder containing project build is ready to be deployed on Apache webserver with history router mode. Neither `Node.js` nor any other web language have to be present on the server since the build contains just static HTML files and assets.

More information about deploying a Vue application can be find in [Vue documentation](https://cli.vuejs.org/guide/deployment.html). 

To change the directory of the app, you need to edit line `3` of desired `.env`, add a new rewrite block to `public/.htaccess` and rebuild.