/**
 * 模拟滑屏操作，@author:云淡风轻
 * 完美解决微信下拉黑底问题
 */
var sim_swipe = {


    //开启滚动
    enableScroll:function(dom){

        var isInertiaScroll = false; //是否正在惯性滚动
        var start_point = 0;  //触摸开始时记录的坐标，用于计算速率
        var start_time = 0; //触摸开始时的时间，用于计算速率

        var last_point = 0;  //上一次记录的触摸坐标，用于计算滚动的偏移
        var last_scrollTop = 0; //上一次的滚动高度，用于计算滚动偏移
        var minInertiaSpeed = 0.5; //开启惯性滚动的阙值

        $(dom).unbind("touchstart");
        $(dom).bind("touchstart",function(e){

            //触摸开始时，停止惯性滚动
            $(dom).stopTime();
            isInertiaScroll = false;

            //记录开始坐标与当前滚动高度
            last_point = e.originalEvent.changedTouches[0].clientY;
            last_scrollTop = $(dom).scrollTop();

            start_time =  new Date().getTime();
            start_point = e.originalEvent.changedTouches[0].clientY;

        });


        $(dom).unbind("touchmove");
        $(dom).bind("touchmove",function(e){

            var currentY = e.originalEvent.changedTouches[0].clientY;
            var offsetScrollY = last_point - currentY;
            var scrollTop = last_scrollTop + offsetScrollY; //计算当前的滚动高度

            //滚动到顶，重置，以免来回拖拉时的偏移量也被计算进去
            if(scrollTop<=0)
            {
                last_point = e.originalEvent.changedTouches[0].clientY;
                last_scrollTop = $(dom).scrollTop();
            }

            if(scrollTop>=($(dom)[0].scrollHeight-$(window).height()))
            {
                last_point = e.originalEvent.changedTouches[0].clientY;
                last_scrollTop = $(dom).scrollTop();
            }

            $(dom).scrollTop(scrollTop);
        });




        $(dom).unbind("touchend");
        $(dom).bind("touchend",function(e){

            //触摸期间的偏移
            var offset = start_point - e.originalEvent.changedTouches[0].clientY;
            //触摸耗时
            var speed_time = new Date().getTime() - start_time;
            //计算速率
            var speed = offset/speed_time;
            if(Math.abs(speed)>minInertiaSpeed)
            {
                inertiaScroll(dom,speed);
            }
        });


        var inertiaTime = 5;  //惯性滚动的定时器间隔
        var moveRatio = 15; //每次惯性滚动的倍率
        var resistance = 0.01; //阻力，即加速度每帧的减少量

        //惯性滚动
        var inertiaScroll = function(dom,speed){
            if(!isInertiaScroll)
            {
                isInertiaScroll  = true;
                var direction = speed<0?-1:1; //负数表示向上滚，正数向下

                var velocity = Math.abs(speed);  //加速度

                $(dom).everyTime(inertiaTime,function(){

                    $(dom).scrollTop($(dom).scrollTop()+velocity*direction*moveRatio);

                    velocity-=resistance;
                    if(velocity<=0)
                    {
                        $(dom).stopTime();
                        isInertiaScroll = false;
                    }
                });

            }
        };
    },


    //禁止滚动
    disableScroll:function(dom){
        $(dom).unbind("touchmove");
        $(dom).unbind("touchstart");
        $(dom).unbind("touchend");
    },

    //翻页触发的速率阙值
    pageMinSpeed:0.3,

    init:function(){
        window.addEventListener('touchmove', function(e){
            e.preventDefault();
        }, { passive: false });
    }
};