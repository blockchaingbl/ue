
@extends('manage.layouts.page')
@section('page_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">充值记录（共 {{$lists->total()}} 条记录）</h3>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>数量</th>
                <th>备注</th>
                <th>增加时间</th>

            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key =>$item)
                <tr>
                    <td>{{$item->amount}}</td>
                    <td>{{$item->memo}}</td>
                    <td>{{$item->create_time}}</td>
                    <td>{{$item->cp_amount}}</td>
                    <td>{{$item->cp_total}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="position:relative;top:10px;">{!! $lists->appends(['coin_type'=>$coin_type])->render() !!}</div>
        <script>
            function clearSearch(){
                $("input[name='keyword']").val('')
                $('#cp_log_date').val('')
            }
            $('#cp_log_date').daterangepicker({
                "linkedCalendars": false,
                "autoUpdateInput": false,
                "locale": {
                    format: 'YYYY-MM-DD',
                    separator: '~',
                    applyLabel: "应用",
                    cancelLabel: "取消",
                    resetLabel: "重置",
                }
            }, function() {
                if(!this.startDate){
                    this.element.val('');
                }else{
                    this.element.val(this.startDate.format(this.locale.format) + this.locale.separator + this.endDate.format(this.locale.format));
                }
            });
            function appeal(id) {
                layer.confirm('是否确认添加算力', {icon: 3, title:'提示'}, function(index){
                    $.ajax({
                        url:'{{'platformlog.calc.add'}}',
                        data:{id_arr:[id]},
                        type:'post',
                        success:function (res) {
                            layer.close(index);
                            layer.msg(res.message);
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    })

                });
            }

        $(function () {
            $('#status').val('{{$param['status']}}');
            $('#status').change(function () {
                $('#search_form').submit();
            });
            $('#check_all').change(function () {
                var flag = $('#check_all').prop('checked');
                if(flag){
                    $('.id_arr').not(':disabled').prop('checked',true)
                }else{
                    $('.id_arr').prop('checked',false)
                }
            })
        })
        function appealMulti() {
            layer.confirm('是否确认批量增加', {icon: 3, title:'提示'}, function(index){
                var id_arr= [];
                $("input[name='id_arr']:checkbox:checked").each(function(){
                    id_arr.push($(this).val())
                })
                if(id_arr.length<=0){
                    layer.msg('请选择要增加的算力');
                    return false;
                }
                $.ajax({
                    url:'{{'platformlog.calc.add'}}',
                    data:{id_arr:id_arr},
                    type:'post',
                    success:function (res) {
                        layer.close(index);
                        layer.msg(res.message);
                        if(res.errcode==0){
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }

                    }
                })
        })}


        </script>

@stop
