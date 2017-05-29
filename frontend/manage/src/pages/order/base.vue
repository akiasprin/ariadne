<template>
    <div class="panel">
        <panel-title :title="$route.meta.title">
            <el-button @click.stop="on_refresh" size="small">
                <i class="fa fa-refresh"></i>
            </el-button>

        </panel-title>

        <div class="panel-body">
            <el-table
                    :data="table_data"
                    v-loading="load_data"
                    element-loading-text="拼命加载中.."
                    border
                    style="width: 100%;">
                <el-table-column type="expand">
                    <template scope="props">
                        <el-form label-position="left" inline class="order-table-expand">

                            <el-form-item
                                    label="商品清单">
                                <img v-for="o in props.row.goods" :key="o" v-bind:src="o.cover" style="width: 120px; height: 120px;
                  margin: 10px 5px 10px 0"/>
                            </el-form-item>
                        </el-form>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="id"
                        label="订单ID"
                        width="100">
                    <template scope="props">
                        {{props.row.id}}
                    </template>
                </el-table-column>
                <el-table-column
                        prop="title"
                        label="订单日期"
                        width="180">
                    <template scope="props">
                        <span v-text="props.row.created_at"
                              style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden; display: block;"></span>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="customer.name"
                        label="订单客户"
                        width="180">
                </el-table-column>
                <el-table-column
                        prop="sum"
                        label="总价"
                        width="150">
                    <template scope="props">
                        ￥{{props.row.sum}}
                    </template>
                </el-table-column>
                <el-table-column
                        prop="state"
                        label="订单状态">
                    <template scope="props">
                        <el-tag v-text="States[props.row.state-1]" v-bind:type="Type[props.row.state-1]"></el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="100">
                    <template scope="props">
                        <router-link :to="{name: 'salesOrdersSave', params: {id: props.row.id}}" tag="span">
                            <el-button type="info" size="small" icon="edit">查看</el-button>
                        </router-link>
                    </template>
                </el-table-column>
            </el-table>

            <bottom-tool-bar>
                <div style="padding: 6px 5px;" slot="handler">
                    <el-row>
                        <el-checkbox-group v-model="checkedStates" @change="handleCheckedStatesChange">
                <span v-for="state in States" style="margin-right: 10px; margin-bottom: 15px">
                  <el-checkbox :label="state" :key="state" :gutter="12">{{state}}</el-checkbox>
                </span>
                        </el-checkbox-group>
                    </el-row>
                </div>
                <div slot="page">

                    <el-pagination
                            @current-change="handleCurrentChange"
                            :current-page="currentPage"
                            :page-size="15"
                            layout="total, prev, pager, next"
                            :total="total">
                    </el-pagination>
                </div>
            </bottom-tool-bar>
        </div>
    </div>
</template>

<script type="text/javascript">
  import {panelTitle, bottomToolBar} from 'components'
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  import ElTag from "../../../node_modules/element-ui/packages/tag/src/tag";
  import ElCol from "element-ui/packages/col/src/col";
  const stateOptions = ['未付款', '已付款', '已发货', '已完成',
    '未付款买家取消', '已付款卖家取消', '未收货买家取消(未确认)', '未收货买家取消(否决)',
    '未收货买家取消(同意)', '退货(未确认)', '退货(否决)',
    '退货(同意)']
  const stateOptionsMap = {
    '未付款': 1, '已付款': 2, '已发货': 4, '已完成': 8,
    '未付款买家取消': 16, '已付款卖家取消': 32, '未收货买家取消(未确认)': 64, '未收货买家取消(否决)': 128,
    '未收货买家取消(同意)': 256, '退货(未确认)': 512, '退货(否决)': 1024,
    '退货(同意)': 2048
  }
  const stateTagTypes = ['gray', 'warning', 'primary', 'success',
    'danger', 'danger', 'danger', 'danger',
    'danger', 'danger', 'danger', 'danger',
    'danger']
  export default{
    data(){
      return {
        checkAll: true,
        checkedStates: ['未付款', '已付款', '已发货', '已完成'],
        States: stateOptions,
        Type: stateTagTypes,
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
      handleCheckAllChange(event) {
        this.checkedStates = event.target.checked ? stateOptions : [];
        this.isIndeterminate = false;
      },
      handleCheckedStatesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.States.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.States.length;
        this.state = 0;
        for (let o of this.checkedStates) {
          this.state += stateOptionsMap[o]
          console.log(o)
        }
        this.get_table_data()
      },
      //刷新
      on_refresh(){
        this.get_table_data()
      },
      //获取数据
      get_table_data(){
        this.load_data = true
        this.$fetch.order.list({
          skip: this.take * (this.currentPage - 1),
          take: this.take,
          merchant: this.get_user_info.user.id,
          state: this.state
        })
          .then(({data: {result, total}}) => {
            this.table_data = result
            this.total = total
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
