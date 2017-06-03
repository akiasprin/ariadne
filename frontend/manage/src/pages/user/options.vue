<template>
  <div class="panel">
    <panel-title :title="$route.meta.title">
      <el-button @click.stop="on_refresh" size="small">
        <i class="fa fa-refresh"></i>
      </el-button>
    </panel-title>

    <div class="panel-body">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="昵称:" style="max-width: 300px;">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="密码:" style="max-width: 300px;">
          <el-input type="password" v-model="form.password" placeholder="留空不修改密码"></el-input>
        </el-form-item>
        <el-form-item label="电子邮箱:" style="max-width: 300px;">
          <el-input v-model="form.email"></el-input>
        </el-form-item>
        <el-form-item label="个性签名:" style="max-width: 300px;">
          <el-input v-model="form.sign"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="on_submit_form" :loading="on_submit_loading">立即提交
          </el-button>
          <el-button @click="$router.back()">取消</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>
<script type="text/javascript">
  import {panelTitle, bottomToolBar} from 'components'
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  import {SET_USER_INFO} from 'store/actions/type'
  export default{
    data() {
      return {
        form: {},
        on_submit_loading: false,
        load_data: true,
      }
    },
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO,
      })
    },
    components: {
      panelTitle,
      bottomToolBar
    },
    created(){
      this.fetch_data()
    },
    methods: {
      ...mapActions({
        set_user_info: SET_USER_INFO,
      }),
      fetch_data(){
        this.load_data = true
        this.$fetch.user.get(
          this.get_user_info.user.id
        ).then(({data}) => {
          this.form = data
          this.load_data = false
        }).catch(() => {
          this.load_data = false
        })
      },
      on_submit_form() {
        this.on_submit_loading = true
        this.$fetch.user.save(this.get_user_info.user.id, this.form)
          .then(({data, msg}) => {
            this.set_user_info({
              user: data,
              login: true
            })
            this.$message.success(msg)
            this.on_submit_loading = false
          })
          .catch(() => {
            this.on_submit_loading = false
          })
      },
      on_refresh(){
        this.fetch_data()
      },
    }
  }
</script>
