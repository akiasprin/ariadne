<template>
  <div class="panel">
    <panel-title :title="$route.meta.title"></panel-title>
    <div class="panel-body">
    </div>
  </div>
</template>
<script type="text/javascript">
    import {panelTitle} from 'components'

    export default{
        data(){
            return {
            }
        },
        created(){
        },
        methods: {

        },
        components: {
            panelTitle
        },
    }
</script>
