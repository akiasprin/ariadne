<template>
  <div class="main-content ofh">
    <el-row>
      <el-col :span="24">
        <h-section></h-section>
          <div class="pageContent">
            <el-row>
              <el-col :span="24">
                <slot></slot>
              </el-col>
            </el-row>
        </div>
      </el-col>
    </el-row>
    <v-footer></v-footer>
  </div>
</template>
<script type="text/javascript">
  import hSection from './headerSection'
  import vFooter from './footer'

  export default{
    name: 'content',
    components: {
      hSection,
      vFooter
    }
  }
</script>
