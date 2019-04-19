<template>
  <div class="record-bottom">
        <box gap="0 35px 20px" class="record-btn-box flex-box">
            <x-button type="primary" style="border-radius:99px;" class='record-btn' link="/deals/need">发布需求</x-button>
        </box>
  </div>
</template>

<script>
export default {
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
    .record-bottom{
        height: 100%;
        .record-btn-box {
            height: 100%;
            justify-content: space-between;
			.record-btn{
	            font-size: 0.9375rem;
	            height: 2.4375rem;
	            line-height: 2.4375rem;
	        }
        }
    }
</style>
