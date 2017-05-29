<template>
    <div class="login-body">
        <div class="loginWarp"
             v-loading="load_data"
             element-loading-text="正在登陆中..."
             @keyup.enter="submit_form">
            <div class="login-title">
                <img src="./images/login_logo.png"/>
            </div>
            <div class="login-form">
                <el-form ref="form" :model="form" :rules="rules" label-width="0">
                    <el-form-item prop="email" class="login-item">
                        <el-input v-model="form.email" placeholder="邮箱地址" class="form-input"
                                  :autofocus="true"></el-input>
                    </el-form-item>
                    <el-form-item prop="password" class="login-item">
                        <el-input type="password" v-model="form.password" placeholder="账户密码"
                                  class="form-input"></el-input>
                    </el-form-item>
                    <el-form-item class="login-item">
                        <el-button size="large" icon="check" class="form-submit" @click="submit_form"></el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
  import {mapActions} from 'vuex'
  import {SET_USER_INFO, SET_TOKEN} from 'store/actions/type'

  export default{
    data(){
      return {
        form: {
          email: null,
          password: null
        },
        rules: {
          email: [{required: true, message: '请输入邮箱地址！', trigger: 'blur'}],
          password: [{required: true, message: '请输入账户密码！', trigger: 'blur'}]
        },
        load_data: false
      }
    },
    methods: {
      ...mapActions({
        set_user_info: SET_USER_INFO,
        set_token: SET_TOKEN
      }),
      //提交
      submit_form() {
        this.$refs.form.validate((valid) => {
          if (!valid) return false
          this.load_data = true
          //登录提交
          this.$fetch.user.login(this.form)
            .then(({data, msg}) => {
              this.set_user_info({
                user: data,
                login: true
              })
              this.set_token(data.access_token)
              this.$message.success(msg)
              setTimeout(this.$router.push({path: '/'}), 500)
            })
            .catch(() => {
              this.load_data = false
            })
        })
      }
    }
  }
</script>
<style lang="scss" type="text/scss" rel="stylesheet/scss">
    .login-body {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-image: url(./images/login_bg.jpg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        .loginWarp {
            width: 300px;
            padding: 25px 15px;
            margin: 100px auto;
            background-color: #fff;
            border-radius: 5px;
            .login-title {
                margin-bottom: 25px;
                text-align: center;
            }
            .login-item {
                .el-input__inner {
                    margin: 0 !important;
                }
            }
            .form-input {
                input {
                    margin-bottom: 15px;
                    font-size: 12px;
                    height: 40px;
                    border: 1px solid #eaeaec;
                    background: #eaeaec;
                    border-radius: 5px;
                    color: #555;
                }
            }
            .form-submit {
                width: 100%;
                color: #fff;
                border-color: #6bc5a4;
                background: #6bc5a4;
                &:active, &:hover {
                    border-color: #6bc5a4;
                    background: #6bc5a4;
                }
            }
        }
    }
</style>
