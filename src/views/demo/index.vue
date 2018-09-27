<template >

    <fwpull :top-load-method="refresh">demo</fwpull>
    

</template>
<script>
import fw_pull from "@/components/fw_pull";
export default {
    components: { 
        fwpull: fw_pull,
    },
    data () {
        return {
        }
    },  
    mounted () {
   
    },
    methods: {
      refresh:function(loaded){
          console.log("111下拉刷新");
      },
        
    }
}
</script>
<style lang="less" scoped>
    @import '../../assets/css/variable.less';
    
    .error-404{
        height: 100%;
        background: #fff;
        text-align: center;
        font-size: @fs-middle;
        padding-top: 20%;
        color: #956d72;
        .error-image{
            width: 11.875rem;
            height: 11.875rem;
            display: block;
            margin: 0 auto 3.125rem;
        }
        .error{
            font-size: 2.1875rem;
            font-family: arial;
        }
    }
</style>


