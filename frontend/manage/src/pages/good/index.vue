<template>
  <div class="panel">
    <panel-title :title="$route.meta.title">
      <el-button @click.stop="on_refresh" size="small">
        <i class="fa fa-refresh"></i>
      </el-button>
      <router-link :to="{name: 'salesGoodsAdd'}" tag="span">
        <el-button type="primary" icon="plus" size="small">商品添加</el-button>
      </router-link>

    </panel-title>

    <div class="panel-body">
      <el-table
        :data="tableData"
        v-loading="load_data"
        element-loading-text="拼命加载中.."
        border>
        <el-table-column type="expand">
          <template scope="props">
            <el-form label-position="left" class="goods-table-expand" inline>
              <el-form-item label="发布用户">
                <span>{{ props.row.user.name }}</span>
              </el-form-item>
              <el-form-item label="商品描述">
                <span>{{ props.row.desc }}</span>
              </el-form-item>
              <el-form-item label="更新时间">
                <span>{{ props.row.updated_at }}</span>
              </el-form-item>
              <el-form-item label="发布时间">
                <span>{{ props.row.created_at }}</span>
              </el-form-item>
              <el-form-item label="所属分类">
                <el-tag type="primary" v-for="o in props.row.categories" :key="o" v-text="o.name"
                        style="margin-right: 5px"></el-tag>
              </el-form-item>
              <el-form-item label="所属标签">
                <el-tag type="warning" v-for="o in props.row.categories" :key="o" v-text="o.name"
                        style="margin-right: 5px"></el-tag>
              </el-form-item>
            </el-form>
          </template>
        </el-table-column>
        <el-table-column
          prop="id"
          label="商品ID"
          width="100">
          <template scope="props">
            {{props.row.id}}
          </template>
        </el-table-column>
        <el-table-column
          prop="title"
          label="商品名称">
          <template scope="props">
            <el-popover trigger="hover" placement="right-start">
              <img v-bind:src="props.row.cover" style="width: 300px"/>
              <div slot="reference" class="name-wrapper">
                <span v-text="props.row.title"
                      style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden; display: block;"></span>
              </div>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column
          prop="price"
          label="价格"
          width="150">
          <template scope="props">
            ￥{{props.row.price}}
          </template>
        </el-table-column>
        <el-table-column
          prop="total"
          label="数量"
          width="80">
        </el-table-column>
        <el-table-column
          prop="state"
          label="状态"
          width="80">
          <template scope="props">
            <el-tag v-text="States[props.row.state-1]" v-bind:type="Type[props.row.state-1]"></el-tag>
          </template>
        </el-table-column>
        <el-table-column
          label="操作"
          width="100">
          <template scope="props">
            <router-link :to="{name: 'salesGoodsSave', params: {id: props.row.id}}" tag="span">
              <el-button type="info" size="small" icon="edit">修改</el-button>
            </router-link>
          </template>
        </el-table-column>
      </el-table>

      <bottom-tool-bar>
        <div style="padding: 6px 5px;" slot="handler">
          <el-checkbox-group v-model="checkedStates" @change="handleCheckedStatesChange" :min=1>
                <span v-for="state in States" style="margin-right: 10px; margin-bottom: 15px">
                  <el-checkbox :label="state" :key="state" :gutter="12">{{state}}</el-checkbox>
                </span>
          </el-checkbox-group>
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
  const stateOptions = ['草稿', '在售', '缺货', '下架']
  const stateOptionsMap = {'草稿': 1, '在售': 2, '缺货': 4, '下架': 8}
  const stateTagTypes = ['gray', 'success', 'warning', 'danger']
  export default{


    data(){
      return {
        stateVal: 3,
        checkedStates: ['草稿', '在售'],
        States: stateOptions,
        Type: stateTagTypes,
        tableData: null,
        currentPage: 1,
        skip: 0,
        total: 0,
        take: 15,
        load_data: true,
      }
    },
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO
      })
    },
    components: {
      ElTag,
      panelTitle,
      bottomToolBar
    },
    created(){
      this.get_data()
    },
    methods: {
      get_data(){
        this.load_data = true
        this.$fetch.good.list({
          skip: this.take * (this.currentPage - 1),
          take: this.take,
          user: this.get_user_info.user.id,
          state: this.stateVal
        }).then(({data: {result, total}}) => {
          this.total = total
          this.tableData = result
          this.load_data = false
        }).catch(() => {
          this.load_data = false
        })
      },
      on_refresh(){
        this.get_data()
      },
      handleCurrentChange(val) {
        this.currentPage = val
        this.get_data()
      },
      handleCheckedStatesChange(val) {
        this.stateVal = 0;
        for (let o of this.checkedStates) {
          this.stateVal += stateOptionsMap[o]
        }
        this.get_data()
      },
    }
  }
</script>

<style>
  .goods-table-expand {
    font-size: 0;
  }

  .goods-table-expand label {
    width: 90px;
    color: #99a9bf;
  }

  .goods-table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
    width: 50%;
  }
</style>
