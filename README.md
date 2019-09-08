# greybox

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run dev
```

### Compiles and minifies for production
```
npm run build
```

### Run your tests
```
npm run test
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).

## Deployment
This project is ready to be deployed on apache webserver with history router mode. To change the directory of the app, you need to edit it in `vue.config.js:2`, `src/router.js:19` and `public/.htaccess:7` and rebuild.

History hash mode is used for development to make things easier.