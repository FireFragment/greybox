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

### Types
Furthermore, this project uses two types - `national` and `international` with tiny differences amongst each other. 

Default type is national. Configurations for `development` and `production` can be edited through `.env.international-dev` or `.env.international-prod` respectively. Debug version is not available for international type.

## Development
### Releasing versions
After commiting and pushing big progress, you are encouraged to create a new release.

**Preparation:**
- Generate new GitHub token in [administration](https://github.com/settings/tokens)
- Add this token to `.GIT_TOKEN` file located in root folder

**Releasing:**
- Run `./release.sh` script
- Fill in requested information - version number, release title and release description
- Wait for the script to make builds and upload release

### Compiles and hot-reloads for development
```
npm run serve
```

This command is in **national development** mode by default. Use `npm run serve:debug` to start local server with debug mode, `serve:prod` for production or `serve:int` for international development type.

### Compiles and minifies for production
```
npm run build
```

This command builds **national production** mode by default. Use `npm run build:debug` to build debug version, `build:dev` for development or `build:int` for international production type.


### Lints and fixes files
```
npm run lint
```

## Deployment
This project is ready to be deployed on apache webserver with history router mode. To change the directory of the app, you need to edit it in `vue.config.js:2`, `src/router.js:19` and `public/.htaccess:7` and rebuild.

History hash mode is used for development to make things easier.