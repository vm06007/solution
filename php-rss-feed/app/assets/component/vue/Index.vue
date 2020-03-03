<template>

    <div>
        <!--<b-navbar toggleable="lg" type="dark" variant="dark">-->
        <b-navbar toggleable="lg" class="navbar top-navbar col-lg-12 col-12 p-0">
            <div class="container">

            </div>
        </b-navbar>

        <div class="main-panel pt-3">
            <div class="container">
                <router-view :key="key_page"></router-view>
            </div>
        </div>
    </div>

</template>

<script>

    export default {
        name: 'app',

        // do not use ()=>([]) if we need this.$store here
        data() {
            return {
                key_page:''
            }
        },
        mounted: function () {


        },

        created() {

        },
        methods: {

        },

        watch: {

            $route(to, from) {
                this.key_page = this.$route.fullPath;
            }
        },
        computed: {

        }
    }
</script>

<style scoped>

</style>