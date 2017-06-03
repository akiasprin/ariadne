<template>
    <div class="panel">
        <panel-title :title="$route.meta.title">
            <el-button @click.stop="on_refresh" size="small">
                <i class="fa fa-refresh"></i>
            </el-button>

        </panel-title>

        <div class="panel-body">
            <div v-for="item in table_data">
                <div style="margin-bottom: 10px"> 卖家：<el-tag type="" v-text="item[0].user.name"></el-tag> </div>
            <el-table :data="item" border style="width: 100%">
                <el-table-column type="selection" width="50"> </el-table-column>
                <el-table-column label="商品名称" prop="title">
                </el-table-column>
                <el-table-column label="单价" width="150" prop="price"> </el-table-column>
                <el-table-column label="数量" width="200" prop="quantity">
                </el-table-column>
                <el-table-column label="小计" width="150" prop="goodTotal"> </el-table-column>
            </el-table> <br>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
  import {panelTitle, bottomToolBar} from 'components'
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  import ElTag from "../../../node_modules/element-ui/packages/tag/src/tag";
  import ElCol from "element-ui/packages/col/src/col";
  export default{
    data(){
      return {
        isIndeterminate: true,
        state: 15,
        table_data: null,
        //当前页码
        currentPage: 1,
        skip: 0,
        //数据总条目
        total: 0,
        //每页显示多少条数据
        take: 15,
        //请求时的loading效果
        load_data: true,
      }
    },
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO
      })
    },
    components: {
      ElCol,
      ElTag,
      panelTitle,
      bottomToolBar
    },
    created(){
      this.get_table_data()
    },
    methods: {
      //刷新
      on_refresh(){
        this.get_table_data()
      },
      //获取数据
      get_table_data(){
        this.load_data = true
        this.$fetch.cart.list({
          skip: this.take * (this.currentPage - 1),
          take: this.take,
          merchant: this.get_user_info.user.id,
          state: this.state
        })
          .then(({data}) => {
          console.log(data)
            this.table_data = data
            this.load_data = false
          })
          .catch(() => {
            this.load_data = false
          })
      },
      //单个删除
      delete_data(item){
        this.$confirm('此操作将删除该数据, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        })
          .then(() => {
            this.load_data = true
            this.$fetch.api_table.del(item)
              .then(({msg}) => {
                this.get_table_data()
                this.$message.success(msg)
              })
              .catch(() => {
              })
          })
          .catch(() => {
          })
      },
      //页码选择
      handleCurrentChange(val) {
        this.currentPage = val
        this.get_table_data()
      },
    }
  }
</script>

<style>
    .order-table-expand {
        font-size: 0;
    }

    .order-table-expand label {
        width: 90px;
        color: #99a9bf;
    }

    .order-table-expand .el-form-item {
        margin-right: 0;
        margin-bottom: 0;
    }
</style>
