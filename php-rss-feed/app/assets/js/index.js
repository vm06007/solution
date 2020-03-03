import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// app specific
import router from './router'
import index from '../component/vue/Index'

Vue.use(BootstrapVue);
Vue.use(Vuex);
Vue.use(VueRouter);

// Error console.log
Vue.config.errorHandler = function (err, vm, info)  {
    console.error(err,vm,info);
};

Vue.config.productionTip = false;
Vue.config.devtools = process.env.NODE_ENV === 'development';

let application = new Vue({
    el: '#vueApp',
    router,
    template: '<index/>',
    components: { index}
});