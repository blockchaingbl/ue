<template>
  <div class="loadmore" ref="loadmore">
    <div class="loadmore__body">
      <slot></slot>
    </div>
    <div class="loadmore__footer">
      <load-more tip="正在加载 . . ." v-if="loading"></load-more>
      
      <load-more :show-loading="false" tip="没有更多了" background-color="#fbf9fe" v-else></load-more>
    </div>
  </div>
</template>

<script>
import { mapGetters ,mapActions} from 'vuex';
import { LoadMore } from 'vux'
export default {
  props: {
    /**
     * 是否加载
     */
    loading: {
      type: Boolean,
      default: false,
    },
    /**
     * 滚动外部容器, 选择器字符串
     */
    container: {
      default: () => (global),
    },

    /**
     * 距离底部多远加载
     */
    distance: {
      type: Number,
      default: 10,
    },
  },
  components: {
        LoadMore
    },
  data() {
    return {
      dom: null, // 外部容器dom
    }
  },
  mounted () {
     document.addEventListener('scroll',this.scroll,true);
   
  },

  methods: {
     ...mapActions([
      'setScrollloding',
    ]),
    /**
     * 滚动钩子
     */
    scroll() {
      this.dom=this.$el.querySelector(this.container);
      const viewHeight = global.innerHeight
      let parentNode
      if (this.dom !== global) {
        parentNode = this.$el
      } else {
        parentNode = this.$el.parentNode
      }
      if (parentNode) {
        const rect = parentNode.getBoundingClientRect()
        if ((rect.bottom-viewHeight-this.distance < 60)) {
           this.setScrollloding(true);
           this.load()
        }else{
          this.setScrollloding(false);
        }

      }


    },
    /**
     * 加载一组数据的方法
     */
    load() {
      this.$emit('load')
    },
  },
  beforeDestroy() {
    this.setScrollloding(false);
    if (this.dom) {
      this.dom.removeEventListener('scroll', this.scroll, false)
    }
  },
}
</script>
<style lang="less">
.loadmore {
  user-select: none;
  
  &__footer {
    color: #628cf8;
    padding:10px 20px;
    text-align: center;
  }
  .weui-loadmore{
    margin: 0 auto;
    line-height: 24px;
    height: 24px;
  }
  .weui-loadmore_line .weui-loadmore__tips{
    top: 0;
  }
  .vux-loadmore.weui-loadmore_line:before, .vux-loadmore.weui-loadmore_line:after{
    top: 10px;
  }
  .tc-loading {
    ~ span {
      vertical-align: middle;

    }
  }
}
</style>