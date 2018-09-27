<template>
  <div class="nodata">
       <img src="@/assets/images/nodata.png" alt="" >
       <h4>{{datatip}}</h4>
       
  </div>
</template>

<script>
export default {
     props: ['datatip'],
    components: {
        
    },
     data () {
        return {
            
        }
    },
    mounted () {

    },
    methods:{
        
    }
}
</script>
<style lang="less">
    @import '../assets/css/variable.less';
    .nodata{
        padding:10rem 0 3rem; 
        width:100%;
        text-align: center;
        img{
            width:5.375rem;
        }
        h4{
            color:#a3abc0;
        }
    }
</style>
