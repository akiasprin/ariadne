<template>
    <div class="panel">
        <panel-title :title="$route.meta.title"></panel-title>
        <div class="panel-body"
             v-loading="load_data"
             element-loading-text="拼命加载中">
            <el-row>
                <el-col :span="24">
                    <el-form ref="form" :model="form" :rules="rules" :options="options">
                        <el-col :span="15">

                            <el-form-item style="width: 100%">
                                <el-input v-model="form.title"></el-input>
                            </el-form-item>

                            <tinymce id="editor" v-model= "form.content" :content="form.content"
                                     :options="options"></tinymce>

                        </el-col>
                        <el-col :span="9">

                            <el-form-item label="封面:" label-width="80px">
                                <el-upload
                                        class="cover-uploader"
                                        name="cover"
                                        action="http://localhost:1088/api/upload"
                                        :headers="{'Authorization': 'Bearer '+get_token}"
                                        :show-file-list="false"
                                        :on-success="handleUploadSuccess">
                                    <img v-if="form.cover" :src="form.cover" class="cover">
                                    <i v-else class="el-icon-plus
                cover-uploader-icon"></i>
                                </el-upload>
                            </el-form-item>


                            <el-col :span="12">

                                <el-form-item label="描述:" label-width="80px">
                                    <el-input v-model="form.desc" placeholder="16字以内"></el-input>
                                </el-form-item>

                                <el-form-item label="省份:" label-width="80px">
                                    <el-input v-model="form.province" placeholder="请输入内容"></el-input>
                                </el-form-item>
                                <el-form-item label="数量:" label-width="80px">
                                    <el-input v-model="form.total" placeholder="请输入内容"></el-input>
                                </el-form-item>

                                <el-form-item label="新旧:" label-width="80px">
                                    <el-select v-model="form.quality">
                                        <el-option
                                                v-for="item in qualityOptions"
                                                :key="item.value"
                                                :label="item.label"
                                                :value="item.value">
                                        </el-option>
                                    </el-select>
                                </el-form-item>

                                <el-form-item label="购置日期:" label-width="80px" v-if="form.quality == 2">
                                    <el-date-picker
                                            v-model="form.purchased_at"
                                            align="right"
                                            type="date"
                                            style="width: 100%;"
                                            :picker-options="pickerOptions">
                                    </el-date-picker>
                                </el-form-item>

                            </el-col>

                            <el-col :span="12">

                                <el-form-item label="分类:" label-width="80px" style="width: 100%">
                                    <el-cascader
                                            :options="cates"
                                            :props="cateTreeProp"
                                            v-model="form.categories">
                                    </el-cascader>
                                </el-form-item>
                                <el-form-item label="城市:" label-width="80px">
                                    <el-input v-model="form.city" placeholder="请输入内容"></el-input>
                                </el-form-item>

                                <el-form-item label="价格:" label-width="80px">
                                    <el-input v-model="form.price" placeholder="请输入内容"></el-input>
                                </el-form-item>
                                <el-form-item label="单位:" label-width="80px">
                                    <el-input v-model="form.unit" placeholder="请输入内容"></el-input>
                                </el-form-item>


                                <el-form-item label="状态:" label-width="80px">
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

                            <el-form-item style="float: right">
                                <el-button type="primary" @click="on_submit_form" :loading="on_submit_loading">立即提交
                                </el-button>
                                <el-button @click="$router.back()">取消</el-button>
                            </el-form-item>

                        </el-col>
                    </el-form>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script type="text/javascript">
  import {panelTitle} from 'components'
  import ElInput from "../../../node_modules/element-ui/packages/input/src/input";
  import {GET_TOKEN, GET_USER_INFO} from "../../store/getters/type";
  import {mapGetters} from "vuex";
  export default{
    computed: {
      ...mapGetters({
        get_token: GET_TOKEN
      })
    },
    data(){
      return {
        cateTreeProp: {
          label: 'name',
          value: 'id',
          children: 'child'
        },
        pickerOptions: {
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
        form: {
          cover: '',
          state: '',
          quality: '',
          purchased_at: '',
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
        cates: [],
        route_id: this.$route.params.id,
        load_data: false,
        on_submit_loading: false,
        rules: {
          name: [{required: true, message: '姓名不能为空', trigger: 'blur'}]
        },
        options: {

          plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern imagetools"
          ],
          toolbar1: "insertfile undo redo | formatselect fontselect fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table hr pagebreak blockquote",
          toolbar2: "bold italic underline strikethrough subscript superscript | forecolor backcolor charmap emoticons | link unlink image media | cut copy paste | insertdatetime fullscreen code",
          menubar: false,
          image_advtab: true,
          height: '460',

        }
      }
    },
    created(){
      this.get_form_data()
    },
    methods: {
      handleUploadSuccess(res, file) {
        this.form.cover = res.url;
      },
      beforeUpload(file) {
        const isJPG = file.type === 'image/jpeg';
        const isLt2M = file.size / 1024 / 1024 < 2;

        if (!isJPG) {
          this.$message.error('上传头像图片只能是 JPG 格式!');
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        return isJPG && isLt2M;
      },
      //获取数据
      get_form_data(){
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
      //提交
      on_submit_form(){
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
      }
    },
    components: {
      ElInput,
      panelTitle,
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
