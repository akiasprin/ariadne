<template>
  <div class="panel">

    <panel-title :title="$route.meta.title"></panel-title>
    <div class="panel-body"
         v-loading="load_data"
         element-loading-text="拼命加载中">
      <el-row>

        <el-col :span="12">
          <el-table
                  :data="form.timelines"
                  style="width: 100%">
            <el-table-column
                    prop="operated_at"
                    label="操作日期"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="user.name"
                    label="操作用户"
                    width="180">
            </el-table-column>
            <el-table-column
                    prop="state"
                    label="修改状态">
              <template scope="props">
                <span v-text="States[props.row.state-1]"></span>
              </template>
            </el-table-column>
          </el-table>
        </el-col>

        <el-col :span="12">

          <el-form ref="form" :model="form" :rules="rules" :options="options">

              <el-form-item label="状态:"  label-width="80px">
                <el-select v-model=form.state placeholder="请选择">
                  <el-option
                          v-for="item in stateOptions"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value">
                  </el-option>
                </el-select>
              </el-form-item>


            <el-form-item label="物流公司:"  label-width="80px" v-if="form.state == 3">
              <el-input v-model="form.express_name" placeholder="物流公司"></el-input>
            </el-form-item>
            <el-form-item label="物流单号:"  label-width="80px" v-if="form.state == 3">
              <el-input v-model="form.express_code" placeholder="物流单号"></el-input>
            </el-form-item>

            <el-form-item style="float: right">
              <el-button type="primary" @click="on_submit_form" :loading="on_submit_loading">立即提交</el-button>
              <el-button @click="$router.back()">取消</el-button>
            </el-form-item>
          </el-form>

        </el-col>
      </el-row>
    </div>

  </div>
</template>

<script type="text/javascript">
  import {panelTitle} from 'components'
  import ElInput from "../../../node_modules/element-ui/packages/input/src/input";
  import ElTag from "../../../node_modules/element-ui/packages/tag/src/tag";
  const stateOptions = ['未付款', '已付款', '已发货', '已完成',
      '未付款买家取消', '已付款卖家取消', '已发货买家取消(未确认)', '已发货买家取消(否决)',
      '已发货买家取消(同意)', '退货(未确认)', '退货(否决)',
      '退货(同意)']
  const stateOptionsMap = {'未付款':1, '已付款':2, '已发货':4, '已完成':8,
      '未付款买家取消':16, '已付款卖家取消':32, '未收货买家取消(未确认)':64, '未收货买家取消(否决)':128,
      '未收货买家取消(同意)':256, '退货(未确认)':512, '退货(否决)':1024,
      '退货(同意)':2048
  }
  export default{
    data(){
      return {
          States: stateOptions,
          form:{
              purchased_at: '',
              state: 1,
              quality: 1
          },
          stateOptions : [],
        route_id: this.$route.params.id,
        load_data: false,
        on_submit_loading: false,
        rules: {
          name: [{required: true, message: '姓名不能为空', trigger: 'blur'}]
        },
          options: {  plugins: [
              'advlist autolink lists link image charmap print preview anchor',
              'searchreplace visualblocks code fullscreen',
              'insertdatetime media table contextmenu paste code'
          ],
              toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
              min_height: 400

      }
      }
    },
    created(){
      this.route_id && this.get_form_data()
        for (let i = 0; i < stateOptions.length; i++) {
            this.stateOptions.push({'value': i+1, 'label': stateOptions[i]});
        }
        console.log(this.stateOptions)
    },
    methods: {
      //获取数据
      get_form_data(){
        this.load_data = true
        this.$fetch.order.get(this.route_id)
          .then(({data}) => {
            this.form = data
            this.load_data = false
          })
          .catch(() => {
            this.load_data = false
          })
      },
      //提交
      on_submit_form(){
        this.$refs.form.validate((valid) => {
          if (!valid) return false
          this.on_submit_loading = true
          this.$fetch.api_order.save(this.route_id, this.form)
            .then(({msg}) => {
              this.$message.success(msg)
              setTimeout(this.$router.back(), 500)
            })
            .catch(() => {
              this.on_submit_loading = false
            })
        })
      }
    },
    components: {
        ElTag,
        ElInput,
        panelTitle,
    }
  }

</script>
