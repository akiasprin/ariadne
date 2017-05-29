<template>
  <div class="panel">
    <panel-title :title="$route.meta.title">
      <el-button @click.stop="on_refresh" size="small">
        <i class="fa fa-refresh"></i>
      </el-button>
      <el-button type="primary" icon="plus" size="small" @click="open_address_dialog('new')">地址添加</el-button>
    </panel-title>

    <div class="panel-body">

      <el-dialog title="收货地址" :visible.sync="addressFormVisible"
                 :lock-scroll="false">
        <el-form>
          <el-col :span=12>
            <el-form-item label="联系人:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.name"></el-input>
            </el-form-item>
            <el-form-item label="省份:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.province"></el-input>
            </el-form-item>
            <el-form-item label="市区:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.area"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span=12>
            <el-form-item label="联系电话:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.phone"></el-input>
            </el-form-item>
            <el-form-item label="城市:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.city"></el-input>
            </el-form-item>
            <el-form-item label="街道:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.street"></el-input>
            </el-form-item>
            <el-form-item label="邮编:" labelWidth="80px" style="max-width: 300px">
              <el-input v-model="address_form.post_code"></el-input>
            </el-form-item>
          </el-col>
        </el-form>
        <span slot="footer" class="dialog-footer">
                    <el-button @click="addressFormVisible = false">取消</el-button>
                    <el-button type="primary" @click="submit_address">提交</el-button>
                  </span>
      </el-dialog>

      <el-table
        :data="tableData"
        border
        style="width: 100%">
        <el-table-column
          prop="name"
          label="联系人">
        </el-table-column>
        <el-table-column
          prop="phone"
          label="联系电话"
          width="160">
        </el-table-column>
        <el-table-column
          prop="province"
          label="省份"
          width="150">
        </el-table-column>
        <el-table-column
          prop="city"
          label="城市"
          width="150">
        </el-table-column>
        <el-table-column
          prop="area"
          label="市区"
          width="120">
        </el-table-column>
        <el-table-column
          prop="street"
          label="街道"
          width="150">
        </el-table-column>
        <el-table-column
          prop="opt"
          label="操作"
          width="115">
          <template scope="props">
            <el-button type="" size="small" icon="edit" @click="open_address_dialog('edit', props.row)"></el-button>
            <el-button type="danger" size="small" icon="delete" @click="remove_address(props.row)"></el-button>
          </template>
        </el-table-column>
      </el-table>

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
  import ElTag from "../../../node_modules/element-ui/packages/tag/src/tag";
  import ElCol from "element-ui/packages/col/src/col";
  export default{


    data(){
      return {
        addressFormVisible: false,
        tableData: null,
        currentPage: 1,
        skip: 0,
        total: 0,
        take: 15,
        load_data: true,
        address_form: {},
        address_ref: null,
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
      this.get_data()
    },
    methods: {
      copy_data(u, v) {
        v.name = u.name
        v.phone = u.phone
        v.province = u.province
        v.city = u.city
        v.area = u.area
        v.street = u.street
        v.post_code = u.post_code
      },
      open_address_dialog(type, address) {
        this.addressFormVisible = true
        this.address_ref = address
        if (type === 'edit') {
          this.address_form.id = address.id
          this.copy_data(address, this.address_form)
        } else {
          this.address_form = {}
        }
      },
      submit_address() {
        this.on_submit_loading = true
        this.$fetch.address.save(this.address_form.id, this.address_form)
          .then(({data, msg}) => {
            this.$message.success(msg)
            if (this.address_form.id) {
              this.copy_data(data, this.address_ref)
            } else {
              this.tableData.push(data)
            }
            this.on_submit_loading = false
            this.addressFormVisible = false
          })
          .catch(() => {
            this.on_submit_loading = false
          })
      },
      remove_address(address) {
        this.on_submit_loading = true
        this.$fetch.address.del(address.id)
          .then(({msg}) => {
            for (let i = 0; i < this.tableData.length; i++) {
              if (this.tableData[i].id === address.id) {
                this.tableData.splice(i, 1)
                break
              }
            }
            this.$message.success(msg)
            this.on_submit_loading = false
          })
          .catch(() => {
            this.on_submit_loading = false
          })
      },
      get_data(){
        this.load_data = true
        this.$fetch.address.list({
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
