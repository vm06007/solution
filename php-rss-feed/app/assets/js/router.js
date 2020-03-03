import Router from 'vue-router'

// components
import home from '../component/vue/Home'
import notfound from '../component/vue/notFound'

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'homepage',
            component: home
        },
        {
            path: '*',
            name: 'notfound',
            component: notfound
        }
    ]
});