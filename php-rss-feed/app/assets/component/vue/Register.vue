<template>

    <div>
        <div class="d-md-flex align-items-center h-md-100 p-5 justify-content-center">
            <div class="border rounded p-5">
                <h3 class="mb-4 text-center">Registration</h3>
                <form name="registration_form" method="post" onkeydown="return event.key != 'Enter';">
                    <div class="form-group">
                        <label for="registration_form_email" class="required">Email</label>

                        <input type="email"
                               id="registration_form_email"
                               name="registration_form[email]"
                               required="required"
                               class="form-control"
                               :class="emailBorderColor"
                               v-model="test_email"
                        />
                        <span class="small text-danger" v-show="status_email_exist">Email exist</span>
                        <span
                            class="small text-danger" v-show="status_email_wrong">Email is wrong</span>

                    </div>
                    <div class="form-group">
                        <label for="registration_form_plainPassword_first" class="required">Password</label>
                        <input type="password" id="registration_form_plainPassword_first"
                               name="registration_form[plainPassword][first]" required="required" class="form-control"
                               v-model="password1"
                               :class="passwordBorderColor"
                        />
                    </div>
                    <div class="form-group">
                        <label for="registration_form_plainPassword_second" class="required">Confirm
                            Password</label>
                        <input type="password" id="registration_form_plainPassword_second"
                               name="registration_form[plainPassword][second]" required="required"
                               class="form-control"
                               :class="passwordBorderColor"
                               v-model="password2"
                        >
                        <span class="small text-danger" v-show="!status_passwords_equal">Passwords not equal</span>
                    </div>
                    <button class="btn btn-primary btn-block" v-show="status_allow_register">Register</button>
                    <span class="btn btn-primary btn-block disabled" v-show="!status_allow_register">Register
                    </span>
                    <input type="hidden" id="registration_form__token" name="registration_form[_token]"
                           v-bind:value="csrf_token"/>
                </form>
            </div>
        </div>
    </div>

</template>

<script>

    import axios from 'axios';

    export default {
        name: 'app',

        // do not use ()=>([]) if we need this.$store here
        data() {
            return {
                key_page: '',
                csrf_token: '',

                test_email: '',
                password1: '',
                password2: '',

                status_email_wrong: false,
                status_email_exist: false,
                status_allow_register: false,
                status_passwords_equal: true
            }
        },
        mounted: function () {
            this.csrf_token = twigData['csrf_token'];
        },

        created() {

        },
        methods: {
            validEmail: function (email) {
                var re = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                return re.test(String(email).toLowerCase());
            },
            checkEmailExist: function (value) {
                axios.post('/email', {'email': value}).then(response => {
                    this.status_email_exist = true;
                    if (typeof response.data.status !== 'undefined') {
                        if (typeof response.data.exist !== 'undefined') {
                            if (!response.data.exist) {
                                this.status_email_exist = false;
                            } else {
                                console.log('Exists');
                                this.status_allow_register = false;
                            }
                        }
                    }
                }).catch(e => {
                    console.error(e);
                })
            }

        },
        watch: {
            $route(to, from) {
                this.key_page = this.$route.fullPath;
            },
            test_email: function (value) {
                this.status_email_wrong = !this.validEmail(value);
                if (!this.status_email_wrong) {
                    this.checkEmailExist(value);
                }
            },
        },
        computed: {
            emailBorderColor: function () {
                if (this.status_email_wrong || this.status_email_exist) {
                    return 'border-danger';
                }
                if (this.password1 === this.password2 && this.password2.length) {
                    this.status_allow_register = true;
                }
                return '';
            },
            passwordBorderColor: function () {
                if (this.password2.length) {
                    if (this.password1 !== this.password2) {
                        this.status_passwords_equal = false;
                        this.status_allow_register = false;
                        return 'border-danger';
                    }
                    this.status_passwords_equal = true;
                    if (!this.status_email_wrong && !this.status_email_exist) {
                        this.status_allow_register = true;
                    }
                }
                return ''
            }
        }
    }
</script>

<style scoped>

</style>