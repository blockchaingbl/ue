{{--批量转账模块，to为多--}}
<script src="{{asset('fanwe_eth.js')}}"></script>
<!-- 模态框（Modal） -->
<div class="modal fade" id="batchSendTo" tabindex="-1" role="dialog" aria-labelledby="batchSendTo" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">批量转账 - 转账期间请勿关闭窗体或刷新页面</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group" style="margin-top:10px; width:100%;">
                        <div id="batch_progressbar">
                            <div id="batch_progresslabel"></div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
</div>
<script>
    /**
     * 从一个钱包转账到多个钱包
     * wallets [
     * {
     *  address:xxx,
     *  contract_address:0x
     *  value:xxx(wei)
     * }
     * ]
     */
    function send_to(ids,fromAddress,fromPrivatekey,gas_price){

        var id = ids.pop();

        var total = $('input[name="export_id"]:checked').length;
        var sendamount = $("#withdraw_id_"+id).attr("sendamount");
        var decimals = $("select[name='coin_type']").find("option:selected").attr("decimals");
        if(!decimals)
        {
            alert("token无效");
            return false;
        }
        sendamount = parseFloat(sendamount);
        for(var i=0;i<decimals;i++){
            sendamount*=10;
        }
        sendamount = "0x" + sendamount.toString(16);
        sendamount = sendamount.split(".");
        sendamount = sendamount[0];

        $.ajax({
            url:'{{url('tx.estimate')}}',
            type:'POST',
            dataType:'json',
            data:{from:fromAddress,id:id,value:sendamount},
            success:function (response) {
                if(response.errcode)
                {
                   alert(response.message);
                   return false;
                }
                else
                {
                    var raw = "0x"+FanweETH.transaction.sign(fromPrivatekey, response.data.nonce, gas_price, response.data.gas, response.data.transfer_address, response.data.transfer_value,response.data.data);
                    $.ajax({
                        url:'{{url('tx.send')}}',
                        type:'POST',
                        dataType:'json',
                        data:{raw:raw,gas_price:gas_price,gas:response.data.gas,nonce:response.data.nonce,id:id,"from":fromAddress,block_chain:response.data.block_chain},
                        success:function (tx) {
                            if(tx.errcode==0)
                            {
                                $("#batchSendTo").modal();
                                var i = total - ids.length;
                                var percent = Math.ceil((i/total)*100);
                                $( "#batch_progressbar" ).progressbar({
                                    value: percent
                                });
                                $("#batch_progresslabel").text( i+"/"+total );
                                if(ids.length==0){
                                    var msg = "转账成功";
                                    $("#batchSendTo").modal("hide");
                                    alert(msg);
                                    location.reload();
                                    return false;
                                };
                                send_to(ids,fromAddress,fromPrivatekey,gas_price);
                            }
                            else
                            {
                                layer.msg(tx.message);
                            }
                        },
                        error:function(res){
                            alert(res.responseText);
                        }
                    })
                }
            },
            error:function(res){
                alert(res.responseText);
            }
        });

    }
</script>

<style>
    #batch_progresslabel {
        position: absolute;
        width:100%;
        text-align: center;
        top: 4px;
        font-weight: bold;
        text-shadow: 1px 1px 0 #fff;
    }
</style>