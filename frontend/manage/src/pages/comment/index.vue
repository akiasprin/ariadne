<template>
  <div class="panel">
    <panel-title :title="$route.meta.title">
      <el-button @click.stop="on_refresh" size="small">
        <i class="fa fa-refresh"></i>
      </el-button>
    </panel-title>

    <div class="panel-body">

      <el-table
        :data="comments"
        style="width: 100%">
        <el-table-column
          prop="created_at"
          label="日期"
          width="180">
        </el-table-column>
        <el-table-column
          prop="title"
          label="商品名称"
          width="250">
          <template scope="props">
            <el-popover trigger="hover" placement="right-start">
              <img v-bind:src="props.row.good.cover" style="width: 300px"/>
              <div slot="reference" class="name-wrapper">
                <span v-text="props.row.good.title"
                      style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden; display: block;"></span>
              </div>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column
          prop="content"
          label="内容">
        </el-table-column>
        <el-table-column
          label="操作"
          width="180">
          <template scope="props">
            <el-button type="" size="small" icon="edit"
                       @click="open_comment_dialog('edit', props.row)"></el-button>
            <el-button type="info" size="small"
                       @click="open_comment_dialog('reply', props.row)"
                       icon="message"></el-button>
            <el-button type="danger" size="small" icon="delete"
                       @click="remove_comment(props.row)"></el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-dialog title="商品评论" :visible.sync="commentFormVisible"
                 :lock-scroll="false">
        <el-form>
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
  export default{


    data(){
      return {
        comment_form: {
          content: null,
          user: {
            name: null,
          },
          good_id: this.$route.params.id,
        },
        comment_ref: null,
        commentFormVisible: false,
        comments: null,
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
      open_comment_dialog(type, comment) {
        this.commentFormVisible = true
        this.comment_form.good_id = comment.good_id
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
              this.comments.push(data)
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
            for (let i = 0; i < this.comments.length; i++) {
              console.log(this.comments[i].id)
              if (this.comments[i].id === comment.id) {
                this.comments.splice(i, 1)
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
        this.$fetch.comment.list({
          skip: this.take * (this.currentPage - 1),
          take: this.take,
          user: this.get_user_info.user.id,
          state: this.stateVal
        }).then(({data: {result, total}}) => {
          this.total = total
          this.comments = result
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
