@extends('web.layouts.base')
@section('scripts')
<script type="text/javascript">
	
</script>
@endsection
@section('styles')

<style type="text/css">
	html{
		height: 100%;
		width: 100%;
	}
	body{
		padding: 0;
		margin: 0;
		height: 100%;
		width: 100%;
	}
	*{
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
	}
	a{
		text-decoration: none;
	}
	input{outline:none}
	@font-face {
		font-family: 'iconfont';  /* project id 632058 */
		src: url('//at.alicdn.com/t/font_632058_wsy7cxaz3v.eot');
		src: url('//at.alicdn.com/t/font_632058_wsy7cxaz3v.eot?#iefix') format('embedded-opentype'),
		url('//at.alicdn.com/t/font_632058_wsy7cxaz3v.woff') format('woff'),
		url('//at.alicdn.com/t/font_632058_wsy7cxaz3v.ttf') format('truetype'),
		url('//at.alicdn.com/t/font_632058_wsy7cxaz3v.svg#iconfont') format('svg');
	}
    em {
	    font-style: normal;
	}
    .iconfont {
        font-family: "iconfont" !important;
        font-size: 16px;
        font-style: normal;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        vertical-align: baseline;
    }
	#preloader{
		display: none;
	}
	.page-redpacket{
		padding-bottom: 1px;
		background-color: #f5f5f5;
		position: absolute;
		left: 0;
		right: 0;
		top: 0;
		bottom: 0;
	}
	.content{
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 3.125rem;
		overflow-x: hidden;
		overflow-y: scroll;
		padding-bottom: 1rem;
	}
	.page-redpacket .redp-head{
		background: url("{{asset('web/images/redpacket_bg.png')}}") no-repeat top center;
		background-size: 100%;
	    padding-top: 2.75rem;
    	margin-bottom: 1.125rem;
	}
	.head-img {
	    width: 3.125rem;
	    height: 3.125rem;
	    margin: 0 auto;
	    border-radius: 2rem;
	    overflow: hidden;
	    margin-bottom: 0.3125rem;
	}
	.head-img img {
	    display: block;
	    width: 100%;
	    height: 100%;
	}
	.head-text {
	    text-align: center;
	    font-size: 0.875rem;
	    line-height: 1.75rem;
	}
	.redp-form {
	    margin: 0 0.9375rem;
	    background: #fff;
	    border-radius: 5px;
	    padding: 0 0.625rem;
        font-size: 0.875rem;
	}
	.form-input {
	    height: 2.9375rem;
	    line-height: 2.9375rem;
	    position: relative;
	    border-bottom: 1px solid #f0f0f0;
	}
	.form-input-mobile {
	    padding-left: 2.875rem;
	}
	.form-input-mobile span {
	    position: absolute;
	    left: 0;
	    top: 50%;
	    -webkit-transform: translateY(-50%);
	    transform: translateY(-50%);
	    text-align: center;
	    width: 2.875rem;
	    border-right: 1px solid #f0f0f0;
	    height: 1rem;
	    line-height: 1rem;
	    font-size: 1rem;
	    font-family: arial;
	    display: block;
	}
	.form-input input {
	    padding: 0 0.3125rem;
	    height: 100%;
	    width: 100%;
	    background: none;
	    display: block;
	}
	.form-input-mobile input {
	    padding: 0 0.625rem;
	}
	.input-img {
	    position: absolute;
	    right: 0;
	    top: 50%;
	    height: 2.125rem;
	    width: 6.625rem;
	    -webkit-transform: translateY(-50%);
	    transform: translateY(-50%);
	}
	.input-img img {
	    width: 100%;
	    height: 100%;
	    display: block;
	}
	.form-input.form-code {
	    border: none;
	    padding-right: 4rem;
	}
	.input-btn {
	    position: absolute;
	    right: 0;
	    top: 0;
	    color: #de4150;
	    width: 4rem;
	    text-align: center;
	    font-size: 0.75rem;
	}
	.form-confirm-btn {
	    background: #de4150;
	    margin: 0.75rem 0.9375rem 0.3125rem;
	    border-radius: 5px;
	    line-height: 2.625rem;
	    text-align: center;
	    color: #fff;
	    font-size: 1rem;
	}
	.rule-box{
		padding: 0 0.9375rem;
		color: #8f8f8f;
		font-size: 0.75rem;
	}
	.rule-title{
		position: relative;
		height: 2.75rem;
		line-height: 2.75rem;
		width: 9.375rem;
		margin: 0 auto;
		
	}
	.rule-title::after{
		position: absolute;
		display: block;
		content: '';
		top: 50%;
		left: 0;
		height: 1px;
		width: 100%;
		background: #e8dadb;
		z-index: 1;
	}
	.rule-title-con{
		position: absolute;
		left: 50%;
		transform: translateX(-50%);
		height: 100%;
		top: 0;
		background-color: #f5f5f5;
		z-index: 2;
		width: 5.5rem;
		text-align: center;
	}
	.rule-item{
		line-height: 1.125rem;
	}
	.download-app{
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		height: 3.125rem;
		background-color: #fff;
		padding: 0 0.9375rem;
	}
	.app-img{
		width: 2.625rem;
		height: 2.625rem;
		margin-right: 0.625rem;
	}
	.app-img img{
		width: 100%;
		height: 100%;
		display: block;
	}
	.load-text{
		text-align: right;
		color: #590f8a;
		font-size: 0.75rem;
		position: relative;
		padding-right: 1rem;
	}
	.load-text:after{
		display: block;
		content: '\e78b';
		position: absolute;
		right: 0;
		top: 50%;
		-webkit-transform: translateY(-50%);
		transform: translateY(-50%);
		font-size: 0.75rem;
		line-height: 0.75rem;
		height: 0.625rem;
		font-weight: bold;
		font-family: 'iconfont';
	}
	.redp-info-box {
	    margin-top: 0.5rem;
	    text-align: center;
	}
	.info-price {
	    line-height: 4.125rem;
	    margin-bottom: 0.625rem;
	}
	.info-price em {
	    font-size: 1.625rem;
	    color: #c63845;
	}
	.info-mobile {
	    font-size: 0.875rem;
	    line-height: 1rem;
	    color: #999;
	}
	.info-mobile em{
		color: #22262d;
	}
	.redp-record{
		padding: 5px 0.9375rem;
		background-color: #fff;
		margin-top: 1.125rem;
	}
	.record-title{
		line-height: 2.5rem;
		font-size: 0.75rem;
		color: #999;
	}
	.record-item {
	    border-top: 1px solid #f0f0f0;
	    padding: 0.5rem 0;
	}
	.record-item-img {
	    width: 2.1875rem;
	    height: 2.1875rem;
	    overflow: hidden;
	    border-radius: 2rem;
	    margin-right: 0.625rem;
	}
	.record-item-img img {
	    width: 100%;
	    height: 100%;
	    display: block;
	}
	.record-item-info {
	    font-size: 0.9375rem;
	    line-height: 1.375rem;
	}
	.record-item-time {
	    line-height: 1.125rem;
	    color: #999;
	}
	.record-item-type{
		color: #ffbb29;
	}
	.redp-info-tis{
		line-height: 3.125rem;
		color: #999;
		font-size: 1rem;
	}
</style>
@endsection
@section('content')
	<div class="page-redpacket">
		<div class="content">
			<div class="redp-head">
				<div class="head-img">
					<img src="{{asset('web/images/avatar.png')}}">
				</div>
				<div class="head-text">AVATAR的AATC礼包</div>
				<div class="head-text">恭喜发财，吉祥如意</div>
			</div>
			<!-- 未领取 -->
			<div class="redp-form-box">
				<div class="redp-form">
					<div class="form-input form-input-mobile">
						<span>+86</span><input type="number" name="mobile" placeholder="请输入手机号领礼包">
					</div>
					<div class="form-input form-code-img">
						<input type="text" name="verifycode" placeholder="请输入动态验证码"><div class="input-img"><img src="{{asset('web/images/v_code.jpg')}}"></div>
					</div>
					<div class="form-input form-code">
						<input type="text" name="verifycode" placeholder="请输入短信验证码"><div class="input-btn">获取验证码</div>
					</div>
				</div>
				<div class="form-confirm-btn">点击领取礼包</div>
			</div>
			<div class="redp-info-box">
				<!-- 已领取 -->
				<!-- <div class="info-price">
					<em>4</em> AATC
				</div>
				<div class="info-mobile">礼包已放入账号：<em>+8615977555555</em></div>
				<div class="form-confirm-btn">立即领取礼包金额</div> -->
				<!-- 领完 -->
				<!-- <div class="redp-info-tis">来晚了，礼包已领完</div> -->
				<!-- 过期 -->
				<!-- <div class="redp-info-tis">该礼包已过期</div> -->
			</div>
			<!-- 已有人领取过 -->
			<!-- <div class="redp-record"> -->
				<!-- 已领取，未领完 -->
				<!-- <div class="record-title">已领取 1/2 个，共1/2 AATC</div> -->
				<!-- 已领完 -->
				<!-- <div class="record-title">1个礼包，1秒被抢光</div>-->
				<!-- 领取记录 -->
				<!-- <div class="record-block">
					<div class="record-item flex-box">
						<div class="record-item-img">
							<img src="{{asset('web/images/avatar.png')}}">
						</div>
						<div class="record-item-text flex-1">
							<div class="record-item-info flex-box">
								<div class="item-info-name flex-1">周杰伦的外婆</div>
								<div class="item-info-price">4 AATC</div>
							</div>
							<div class="record-item-footer flex-box">
								<div class="record-item-time flex-1">07-07 11:37</div>
								<div class="record-item-type"><i class="iconfont">&#xe61b;</i> 手气最佳</div>
							</div>
						</div>
					</div>
				</div>-->
			<!-- </div>  -->
			<div class="rule-box">
				<div class="rule-title">
					<div class="rule-title-con">礼包领取规则</div>
				</div>
				<div class="rule-text-box">
					<div class="rule-item">1. 输入手机号，点击领礼包；</div>
					<div class="rule-item">2. 安装AVATAR数字货币钱包，并用该手机号登录</div>
					<div class="rule-item">3. 在AVATAR钱包里点击发现-礼包管理-我的礼包，查看收到的礼包;</div>
					<div class="rule-item">4. 收到的礼包金额，自动存入到托管账户中，可以在我的-托管账户里查看 ，并提现到本地钱包；</div>
					<div class="rule-item">5. 同一个礼包，每人只能领取一次。</div>
				</div>
			</div>
		</div>
		<a href="javascript:void(0)" class="download-app flex-box">
			<div class="app-img">
				<img src="{{asset('web/images/app_logo.png')}}">
			</div>
			<div class="load-text flex-1">点击下载AVATAR数字货币钱包</div>
		</a>
	</div>
@endsection