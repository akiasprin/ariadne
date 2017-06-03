<template>
  <div class="panel">
    <panel-title :title="$route.meta.title"></panel-title>
    <div class="panel-body"
         v-loading="load_data"
         element-loading-text="拼命加载中">
      <el-row>
        <el-col :span=24>
          <el-form ref="form" :model="form" :rules="rules">
            <el-tabs type="border-card">
              <el-tab-pane label="基本信息">
                <el-col :span=10>
                  <el-form-item label="标题:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.title"></el-input>
                  </el-form-item>
                  <el-form-item label="描述:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.desc" placeholder="16字以内"></el-input>
                  </el-form-item>
                  <el-form-item label="省份:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.province" placeholder="请输入内容"></el-input>
                  </el-form-item>
                  <el-form-item label="城市:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.city" placeholder="请输入内容"></el-input>
                  </el-form-item>
                  <el-form-item label="数量:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.total" placeholder="请输入内容"></el-input>
                  </el-form-item>
                  <el-form-item label="单位:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.unit" placeholder="请输入内容"></el-input>
                  </el-form-item>
                  <el-form-item label="价格:" labelWidth="50px" style="max-width: 300px">
                    <el-input v-model="form.price" placeholder="请输入内容"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span=12>
                  <el-form-item label="封面:" labelWidth="80px">
                    <el-upload
                      class="cover-uploader"
                      name="cover"
                      action="http://localhost:1088/api/upload"
                      :headers="{'Authorization': 'Bearer ' + get_token}"
                      :show-file-list="false"
                      :on-success="handleUploadSuccess">
                      <img v-if="form.cover" :src="form.cover" class="cover">
                      <i v-else class="el-icon-plus
                cover-uploader-icon"></i>
                    </el-upload>
                  </el-form-item>
                  <el-form-item label="新旧:" labelWidth="80px">
                    <el-select v-model="form.quality">
                      <el-option
                        v-for="item in qualityOptions"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                      </el-option>
                    </el-select>
                  </el-form-item>
                  <el-form-item label="购置日期:" v-if="form.quality == 2" labelWidth="80px">
                    <el-date-picker
                      v-model="form.purchased_at"
                      align="right"
                      type="date"
                      style="width: 218px;"
                      :picker-options="purchasedAtOptions">
                    </el-date-picker>
                  </el-form-item>
                  <el-form-item label="分类:" style="width: 100%" labelWidth="80px">
                    <el-cascader
                      :options="cates"
                      :props="cateTreeProp"
                      v-model="form.categories">
                    </el-cascader>
                  </el-form-item>
                  <el-form-item label="状态:" labelWidth="80px">
                    <el-select v-model=form.state placeholder="请选择">
                      <el-option
                        v-for="item in stateOptions"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-tab-pane>
              <el-tab-pane label="详细内容">
                <vue-editor v-model="form.content"></vue-editor>
              </el-tab-pane>
              <el-tab-pane label="评论管理">
                <el-table
                  :data="form.comments"
                  style="width: 100%">
                  <el-table-column
                    prop="created_at"
                    label="日期"
                    width="180">
                  </el-table-column>
                  <el-table-column
                    prop="user.name"
                    label="用户"
                    width="160">
                  </el-table-column>
                  <el-table-column
                    prop="content"
                    label="内容">
                  </el-table-column>
                  <el-table-column
                    label="操作"
                    width="160">
                    <template scope="props">
                      <el-button type="" size="small" icon="edit"
                                 @click="open_comment_dialog('edit', props.row)"
                                 :disabled="get_user_info.user.id !== props.row.user.id"></el-button>
                      <el-button type="info" size="small"
                                 @click="open_comment_dialog('reply', props.row)"
                                 icon="message"></el-button>
                      <el-button type="danger" size="small" icon="delete"
                                 @click="remove_comment(props.row)"
                                 :disabled="get_user_info.user.id !== props.row.user.id"></el-button>
                    </template>
                  </el-table-column>
                </el-table>
                <el-dialog title="商品评论" :visible.sync="commentFormVisible"
                           :lock-scroll="false">
                  <el-form :model="form">
                    <el-input
                      type="textarea"
                      :rows="5"
                      placeholder="请输入内容"
                      style="margin: 20px 0"
                      v-model="comment_form.content">
                    </el-input>
                  </el-form>
                  <span slot="footer" class="dialog-footer">
                    <el-button @click="commentFormVisible = false">取消</el-button>
                    <el-button type="primary" @click="submit_comment">提交</el-button>
                  </span>
                </el-dialog>
              </el-tab-pane>
              <el-col :span=24>
                <el-form-item style="float: right; margin-top: 22px;">
                  <el-button type="primary" @click="on_submit_form" :loading="on_submit_loading">立即提交
                  </el-button>
                  <el-button @click="$router.back()">取消</el-button>
                </el-form-item>
              </el-col>
            </el-tabs>
          </el-form>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script type="text/javascript">
  import {panelTitle} from 'components'
  import {GET_TOKEN, GET_USER_INFO} from "../../store/getters/type";
  import {mapGetters} from "vuex";
  import {VueEditor} from 'vue2-editor'
  export default{
    computed: {
      ...mapGetters({
        get_token: GET_TOKEN,
        get_user_info: GET_USER_INFO
      })
    },
    data(){
      return {
        form: {
          cover: '',
          state: '',
          quality: '',
          purchased_at: '',
        },
        cates: [],
        comment_form: {
          content: '',
          user: {
            name: '',
          },
          good_id: this.$route.params.id,
        },
        cateTreeProp: {
          label: 'name',
          value: 'id',
          children: 'child'
        },
        purchasedAtOptions: {
          shortcuts: [{
            text: '今天',
            onClick(picker) {
              picker.$emit('pick', new Date());
            }
          }, {
            text: '昨天',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24);
              picker.$emit('pick', date);
            }
          }, {
            text: '一周前',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', date);
            }
          }]
        },
        qualityOptions: [{
          value: 1,
          label: '全新'
        }, {
          value: 2,
          label: '二手'
        }],
        stateOptions: [{
          value: 1,
          label: '草稿'
        }, {
          value: 2,
          label: '在售'
        }, {
          value: 3,
          label: '缺货'
        }, {
          value: 4,
          label: '下架'
        }],
        comment_ref: null,
        route_id: this.$route.params.id,
        load_data: false,
        on_submit_loading: false,
        commentFormVisible: false,
        rules: {
          name: [{required: true, message: '姓名不能为空', trigger: 'blur'}]
        },
      }
    },
    created(){
      this.fetch_data()
    },
    methods: {
      fetch_data() {
        this.load_data = true
        this.$fetch.category.list()
          .then(({data}) => {
            this.cates = data
            if (this.route_id) {
              this.$fetch.good.get(this.route_id)
                .then(({data}) => {
                  this.form = data
                })
            }
            this.load_data = false
          })
          .catch(() => {
            this.load_data = false
          })
      },
      open_comment_dialog(type, comment) {
        this.commentFormVisible = true
        if (type === 'edit') {
          this.comment_ref = comment
          this.comment_form.id = comment.id
          this.comment_form.content = comment.content
        } else {
          this.comment_form.id = null
          this.comment_form.content = '@' + comment.user.name + ' '
        }
      },
      submit_comment() {
        this.on_submit_loading = true
        this.$fetch.comment.save(this.comment_form.id, this.comment_form)
          .then(({data, msg}) => {
            this.$message.success(msg)
            if (this.comment_form.id) {
              this.comment_ref.content = data.content
            } else {
              this.form.comments.push(data)
            }
            this.on_submit_loading = false
            this.commentFormVisible = false
          })
          .catch(() => {
            this.on_submit_loading = false
          })
      },
      remove_comment(comment) {
        this.on_submit_loading = true
        this.$fetch.comment.del(comment.id)
          .then(({msg}) => {
            for (let i = 0; i < this.form.comments.length; i++) {
              if (this.form.comments[i].id === comment.id) {
                this.form.comments.splice(i, 1)
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
      on_submit_form() {
        this.$refs.form.validate((valid) => {
          if (!valid) return false
          this.on_submit_loading = true
          this.$fetch.good.save(this.route_id, this.form)
            .then(({msg}) => {
              this.$message.success(msg)
              setTimeout(this.$router.back(), 500)
            })
            .catch(() => {
              this.on_submit_loading = false
            })
        })
      },
      handleUploadSuccess(res, file) {
        this.form.cover = res.url;
      },
    },
    components: {
      panelTitle,
      VueEditor,
    }
  }

</script>
<style>
  .cover-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }

  .cover-uploader .el-upload:hover {
    border-color: #20a0ff;
  }

  .cover-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }

  .cover {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
