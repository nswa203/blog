
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

window.Vue = require('vue');
window.Slug = require('slug');
Slug.defaults.mode = "rfc3986";
var _ = require('lodash');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('folders',	require('./components/Folders.vue'));
Vue.component('images',		require('./components/Images.vue'));
Vue.component('slugwidget',	require('./components/slugWidget.vue'));
Vue.component('slugwidget2',	require('./components/slugWidget2.vue'));

//const app = new Vue({
//    el: '#app'
//});

//require('./manage');
