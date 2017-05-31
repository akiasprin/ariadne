<template>
    <div class="register-body">
        <div class="registerWarp"
             v-loading="load_data"
             element-loading-text="正在注册中..."
             @keyup.enter="submit_form">
            <div class="register-title">
                <img src="./images/register_logo.png"/>
            </div>
            <div class="register-form">
                <el-form ref="form" :model="form" :rules="rules" label-width="0">
                    <el-form-item prop="name" class="register-item">
                        <el-input type="name" v-model="form.name" placeholder="请输入昵称：" class="form-input"></el-input>
                    </el-form-item>
                    <el-form-item prop="phone" class="register-item">
                        <el-input type="phone" v-model="form.phone" placeholder="请输入手机号码："
                                  class="form-input" style="max-width: 190px"></el-input>
                      <el-button @click="send_code">发送验证码</el-button>
                    </el-form-item>
                    <el-form-item prop="password" class="register-item">
                        <el-input type="password" v-model="form.password" placeholder="请输入账户密码："
                                  class="form-input"></el-input>
                    </el-form-item>
                    <el-form-item prop="code" class="register-item">
                        <el-input type="code" v-model="form.code" placeholder="请输入验证码："
                                  class="form-input"></el-input>
                    </el-form-item>
                    <el-form-item class="register-item">
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
  import ElButton from "../../../node_modules/element-ui/packages/button/src/button";

  export default{
    components: {ElButton},
    data(){
      return {
        form: {
          email: null,
          password: null,
          name: null,
          phone: null
        },
        rules: {
          email: [{required: true, message: '请输入邮箱地址！', trigger: 'blur'}],
          password: [{required: true, message: '请输入账户密码！', trigger: 'blur'}],
          name: [{required: true, message: '请输入昵称！', trigger: 'blur'}],
          phone: [{required: true, message: '请输入联系电话！', trigger: 'blur'}]
        },
        //请求时的loading效果
        load_data: false
      }
    },
    methods: {
      ...mapActions({
        set_user_info: SET_USER_INFO,
        set_token: SET_TOKEN
      }),
      //提交
      send_code() {
        this.$fetch.user.sendcode(this.form)
          .then(() => {
          })
          .catch(() => {
          })
      },
      submit_form() {
        this.$refs.form.validate((valid) => {
          if (!valid) return false
          this.load_data = true
          //登录提交
          this.$fetch.user.register(this.form)
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
    .register-body {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-image: url(./images/register_bg.jpg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        .registerWarp {
            width: 300px;
            padding: 25px 15px;
            margin: 100px auto;
            background-color: #fff;
            border-radius: 5px;
            .register-title {
                margin-bottom: 25px;
                text-align: center;
            }
            .register-item {
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
