<template>
    <div v-show="loaded">

        <div>
            <h3>Rating</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>Pos.</th>
                    <th>Word</th>
                    <th>Counter</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item,index) in word_rating_list" :key="index">
                    <td>{{index}}</td>
                    <td>{{item.word}}</td>
                    <td>{{item.counter}}</td>

                </tr>
                </tbody>
            </table>
        </div>

        <div>
            <h3>Articles</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>Id</th>
                    <th>Updated</th>
                    <th>Unique</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item,index) in feed_item_list" :key="index">
                    <td>{{item.id}}</td>
                    <td>{{item.updated}}</td>
                    <td>{{item.unique_code}}</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>

</template>

<script>

    import API from '../../js/lib/api'

    export default {
        name: "home",

        components: {

        },

        data() {
            return {
                status_is_admin: false,
                loaded: false,

                /* feed */
                feed_item_list:[],

                /* words */
                word_rating_list:[]
            }
        },
        created() {

        },
        mounted() {
            console.log('[Index]---------- Mounted Home--------------');

            this.user = twigCategories;
            this.status_is_admin = (this.user.roles.includes('ROLE_ADMIN'));

            this.loadFeedList();

        },
        methods: {

            loadFeedList: function () {
                this.loaded = false;
                return API.loadFeedList().then(result=>{
                    this.feed_item_list = result['articles'];
                    this.word_rating_list = result['summary']
                    this.loaded = true;
                }).catch(e=>{
                    console.error(e);
                })

            }


        },
        computed: {



            /*warn_message_email() {
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(this.message_email);
            },
            warn_message_reply() {
                if (this.message_reply === '') return true;
                let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(this.message_reply);
            },
            warn_send_email() {
                return this.warn_message_email * this.warn_message_reply !== 1;
            },
            invalid_receiver_email() {
                if (this.message_email.length === 0) {
                    return 'Please enter receiver email'
                }
                return this.warn_message_email === true ? '' : 'Wrong email'
            },
            valid_receiver_email() {
                return this.warn_message_email === true ? 'Thank you' : ''
            },
            invalid_reply_email() {
                if (this.message_reply.length === 0) {
                    return 'Please enter reply email'
                }
                return this.warn_message_reply === true ? '' : 'Wrong email'
            },
            valid_reply_email() {
                return this.warn_message_reply === true ? 'Thank you' : ''
            }*/

        }
    }
</script>

<style scoped>

</style>
