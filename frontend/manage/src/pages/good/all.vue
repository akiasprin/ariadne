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
        <el-col :span="8" v-for="(item, index) in table" :key="item">
          <div style="padding: 10px">

            <el-card :body-style="{ padding: '0px' }">
              <img v-bind:src="item.cover" class="image">
              <div style="padding: 14px;">
                <span v-text="item.title"></span>
                <div class="bottom clearfix">
                  <el-button type="text" class="button" @click="on_add_to_cart(item.id)">添加到购物车</el-button>
                </div>
              </div>
            </el-card>
          </div>
        </el-col>

      <bottom-tool-bar>
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
  export default{
    data() {
      return {
        table: null,
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
      panelTitle,
      bottomToolBar
    },
    created(){
      this.fetch_data()
    },
    methods: {
      fetch_data() {
        this.load_data = true
        this.$fetch.good.list({
          state: 2,
          skip: this.take * (this.currentPage - 1),
          take: this.take,
        }).then(({data: {result, total}}) => {
          this.total = total
          this.table = result
          this.load_data = false
        }).catch(() => {
          this.load_data = false
        })
      },
      on_add_to_cart(id) {
        this.load_data = true
        this.$fetch.cart.save({
          good: [id, 1]
        }).then(({}) => {
          this.load_data = false
        }).catch(() => {
          this.load_data = false
        })

      },
      on_refresh(){
        this.fetch_data()
      },
      handleCurrentChange(val) {
        this.currentPage = val
        this.fetch_data()
      },
      handleCheckedStatesChange(val) {
        this.stateVal = 0;
        for (let o of this.checkedStates) {
          this.stateVal += stateOptionsMap[o]
        }
        this.fetch_data()
      },
    }
  }
</script>

<style>
  .time {
    font-size: 13px;
    color: #999;
  }

  .bottom {
    margin-top: 13px;
    line-height: 12px;
  }

  .button {
    padding: 0;
    float: right;
  }

  .image {
    width: 100%;
    display: block;
  }

  .clearfix:before,
  .clearfix:after {
    display: table;
    content: "";
  }

  .clearfix:after {
    clear: both
  }
</style>