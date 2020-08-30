# greybox

## Project setup
```
npm install
```
## Environments
This project runs in 3 environments - development, debug and production.

- **Development** should be used for local development only
- **Debug** should be used for development version of the server
- **Production** should be used for live version of the server

Main difference is that only **production** has Vue devtools turned off and uses live API.

Configuration can be changed in `.env.[mode]` files in root folder. Current mode will be visible next to logo in navbar (except for production).

### PDS
This project uses two types - `normal` and `PDS` with tiny differences amongst each other (filtering events etc.). 

Default type is normal. Configurations for PDS `development` and `production` can be edited through `.env.pds-dev` or `.env.pds-prod` respectively. Debug version is not available for PDS.

## Development

### Run watcher
```
npm run serve
```

This command is in **national development** mode by default. Use `npm run serve:debug` to start local server with debug mode, `serve:prod` for production or `serve:pds` for PDS development.

### Compile and minify for production
```
npm run build
```

This command builds **national production** mode by default. Use `npm run build:debug` to build debug version, `build:dev` for development or `build:pds` for PDS production.


### Lint and fix files
```
npm run lint
```

### Development process
- All new development should be done on `vue-frontend-dev` branch or, in case of a bigger change, on a separate branch (e. g. `vue-frontend-password-reset`) and merged into `dev` later
- Once development branch is deployed on development server and fully tested, it should be merged into `vue-frontend-prod`

### Releasing versions
After every merge into `vue-frontend-prod` branch, a new release should be made (see below) for production deployment

**Preparation:**
- Generate new GitHub token in [administration](https://github.com/settings/tokens)
- Add this token to `.GIT_TOKEN` file located in root folder

**Releasing:**
- Run `./release.sh` script
- Fill in requested information - version number, release title and release description
- Wait for the script to make builds and upload release

## Deployment
This project is ready to be deployed on apache webserver with history router mode. To change the directory of the app, you need to edit it in `vue.config.js:2`, `src/router.js:19` and `public/.htaccess:7` and rebuild.

History hash mode is used for development to make things easier.