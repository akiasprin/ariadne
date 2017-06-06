<template>
  <div class="panel">
    <panel-title :title="$route.meta.title">
      <el-button @click.stop="on_refresh" size="small">
        <i class="fa fa-refresh"></i>
      </el-button>

    </panel-title>

    <div class="panel-body">
      <el-form>
        <div v-for="item in table_data">
          <div style="margin-bottom: 10px"> 卖家：
            <el-tag type="" v-text="item[0].user.name"></el-tag>
          </div>
          <el-table :data="item"
                    border
                    style="width: 100%"
                    @selection-change.navie="handleSelectionChangeIndex(item[0].user_id)"
                    @selection-change="handleSelectionChange"
          >
            <el-table-column type="selection" width="50" :selectable="SelectableCheck"></el-table-column>
            <el-table-column label="商品名称" prop="title">
            </el-table-column>
            <el-table-column label="单价" width="150" prop="price"></el-table-column>
            <el-table-column label="数量" width="200" prop="pivot.quantity">
              <template scope="props">
                <el-input-number size="small"
                                 v-model="props.row.pivot.quantity"
                                 :min="1"
                                 style="margin-top: 10px"
                                 @change="handleModifyQuantity(props.row)"
                                 :disabled="props.row.state != 2"
                ></el-input-number>
              </template>
            </el-table-column>
            <el-table-column label="小计" width="150" prop="">
              <template scope="props">
                <span v-text="'￥'+props.row.price*props.row.pivot.quantity"></span>
              </template>
            </el-table-column>
            <el-table-column
              label="操作"
              width="70">
              <template scope="props">
                <el-button type="" size="small" icon="delete" @click="on_remove(props.row, item)"></el-button>
              </template>
            </el-table-column>


          </el-table>
          <br>
        </div>

        <el-col :span=24>
          <el-form-item style="float: right; margin-top: 22px;">
            <el-button type="primary" :loading="on_submit_loading" @click="on_submit">立即提交
            </el-button>
          </el-form-item>
        </el-col>
      </el-form>
    </div>
  </div>
</template>

<script type="text/javascript">
  import {panelTitle, bottomToolBar} from 'components'
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  export default{
    data(){
      return {
        on_submit_loading: false,
        state: 15,
        table_data: null,
        multipleSelection: [],
        //请求时的loading效果
        load_data: true,
        selection: [],
        selection_id: null,

        SelectableCheck: (row, index)=>{
          return row.state === 2
        }
      }
    },
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO
      }),
    },
    components: {
      panelTitle,
      bottomToolBar
    },
    created(){
      this.get_table_data()
    },
    methods: {
      handleSelectionChange(selection) {
        console.log(selection)
        this.selection[this.selection_id] = [];
        for (let i = 0; i < selection.length; i++) {
          if (!this.selection[selection[i].user_id]) {
            this.selection[selection[i].user_id] = [];
          }
          this.selection[selection[i].user_id].push([selection[i].id, selection[i].pivot.quantity]);
        }
        console.log(this.selection)
      },
      handleSelectionChangeIndex(id) {
        this.selection_id = id;
      },
      handleModifyQuantity(good) {
        if (this.selection[good.user_id]) {
          for (let item of this.selection[good.user_id]) {
            if (item[0] === good.id) {
              item[1] = good.pivot.quantity
              break
            }
          }
        }
      },
      //刷新
      on_refresh(){
        this.get_table_data()
      },
      //刷新
      on_remove(good, data){
        this.load_data = true
        this.$fetch.cart.save({
          good: [good.id, -1e9]
        }).then(({}) => {
          for (let i = 0; i < data.length; i++) {
            if (data[i].id === good.id) {
              data.splice(i, 1)
              break
            }
          }
          this.get_table_data()
          this.load_data = false
        }).catch(() => {
          this.load_data = false
        })

      },
      //获取数据
      get_table_data(){
        this.load_data = true
        this.$fetch.cart.list({})
          .then(({data}) => {
            this.table_data = data
            this.load_data = false
          })
          .catch(() => {
            this.load_data = false
          })
      },
      on_submit(){
        this.load_data = true
        let tmp = [];
        for (let i = 0; i < this.selection.length; i++) {
          if (this.selection[i])
          tmp = tmp.concat(tmp, this.selection[i]);
        }
        console.log(tmp);
        this.$fetch.order.save(null, {
          address_id: 1,
          goods: tmp,
          })
          .then(({data}) => {
            this.load_data = false
          })
          .catch(() => {
            this.load_data = false
          })
      },
    }
  }
</script>
