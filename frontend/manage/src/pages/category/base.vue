<template>
    <div class="panel">
        <panel-title :title="$route.meta.title">
            <el-button @click.stop="on_refresh" size="small">
                <i class="fa fa-refresh"></i>
            </el-button>

            <el-button type="primary" icon="plus" size="small" @click.stop="append">根目录添加</el-button>

        </panel-title>

        <div class="panel-body">
            <el-col>
                <el-tree
                        ref="ccc"
                        :data="cates"
                        :props="treeprops"
                        show-checkbox
                        node-key="id"
                        default-expand-all
                        :expand-on-click-node="false"
                        :render-content="renderContent"
                        style="border-width: 0px;">
                </el-tree>
            </el-col>
        </div>

    </div>
</template>

<script type="text/javascript">
  import {panelTitle, bottomToolBar} from 'components'
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  import ElCol from "element-ui/packages/col/src/col";
  import {Message} from "element-ui";
  export default{


    data(){
      return {
        on_submit_loading: false,
        treeprops: {
          label: 'name',
          value: 'id',
          children: 'child'
        },
        isIndeterminate: true,
        //请求时的loading效果
        load_data: true,
        cates: [],
      }
    },
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO
      })
    },
    components: {
      ElCol,
      panelTitle,
      bottomToolBar
    },
    created(){
      this.get_table_data()
    },
    methods: {
      append(node, store, _data) {
        let insert = (type, name, slug)  => {
          this.$prompt('添加的分类地址名', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            inputValue: slug,
            inputPattern: /\S/,
            inputErrorMessage: '不能为空'
          }).then(({value}) => {
            let tmp = {name: name, slug: value, parent_id: type === 'root' ? 0 : node.data.id}
            this.$fetch.category.save(null, tmp)
              .then(({data}) => {
                tmp.id = data.id
                type === 'root' ? this.$refs.ccc.store.append(tmp) : store.append(tmp, _data);
              }).catch((e) => {
            })
          }).catch((e) => {
            })
        }
        this.$prompt('添加的分类名', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPattern: /\S/,
          inputErrorMessage: '不能为空'
        }).then(({value}) => {
          Message({message: '尝试获取翻译..'})
          this.$fetch.category.translate(value).then(({data: {translation}}) => {
            if (store) {
              insert('child', value, translation[0].replace(' ', '-'))
            } else {
              insert('root', value, translation[0].replace(' ', '-'))
            }
          }).catch((e) => {
            Message({message: '翻译获取超时..', type: 'warning'})
            if (node) {
              insert('child', value, value.replace(' ', '-'))
            } else {
              insert('root', value, value.replace(' ', '-'))
            }
          })
        }).catch((e) => {
        })
      },

      remove(store, _data) {
        this.$fetch.category.del(_data.id)
          .then(() => {
            store.remove(_data);
          })
      },

      edit(store, _data) {
        let edit = (name, slug)  => {
          this.$prompt('修改的分类地址名', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            inputValue: slug,
            inputPattern: /\S/,
            inputErrorMessage: '不能为空'
          }).then(({value}) => {
            let tmp = {name: name, slug: value}
            this.$fetch.category.save(_data.id, tmp)
              .then(({data}) => {
                _data.name = tmp.name
                _data.slug = tmp.slug
              }).catch((e) => {
            })
          }).catch((e) => {
          })
        }
        this.$prompt('修改的分类名', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputValue: _data.name,
          inputPattern: /\S/,
          inputErrorMessage: '不能为空'
        }).then(({value}) => {
          Message({message: '尝试获取翻译..'})
          this.$fetch.category.translate(value).then(({data: {translation}}) => {
              edit(value, translation[0].replace(' ', '-'))
          }).catch((e) => {
            Message({message: '翻译获取超时..', type: 'warning'})
            edit(value, value.replace(' ', '-'))
          })
        }).catch((e) => {
        })
      },

      renderContent(h, {node, data, store}) {
        return (
          <span>
          <span>
          <span>{node.label}</span>
        </span>
        <span class="tree-btns">
          <i class="el-icon-plus"  on-click={ () => this.append(node, store, data) }></i>
        <i class="el-icon-edit"  on-click={ () => this.edit(store, data) }></i>
        <i class="el-icon-close"  on-click={ () => this.remove(store, data) }></i>
        </span>
        </span>);
      },
      //刷新
      on_refresh(){
        this.get_table_data()
      },
      //获取数据
      get_table_data(){
        this.load_data = true
        this.$fetch.category.list()
          .then(({data}) => {
            this.cates = data
          })
          .catch(() => {
            this.load_data = false
          })
      },
    }
  }
</script>
<style>
    .el-tree-node {
        position: relative;
    }
    .tree-btns {
        display: none;
        position: relative;
        top: 0;
        left: 10px;
        font-size: 12px;
    }
    .el-tree-node__content:hover .tree-btns {
        display: inline-block;
    }
    .el-tree-node i {
        margin: 0 5px;
        color: #999;
    }
    .el-tree-node i:hover{
        color: #20a0ff;

    }
</style>
