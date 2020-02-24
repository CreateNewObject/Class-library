$(".help").mousemove(function() {
	$(".help>a>.triangle>i").addClass("rota").removeClass("rota_2")
	$(".help_list").addClass("bl")
})
$(".help").mouseout(function() {
	$(".help>a>.triangle>i").removeClass("rota").addClass("rota_2")
	$(".help_list").removeClass("bl")
})

$(".alllist_title").mousemove(function() {
	$(".alllist").addClass("btn_alllist")
})
$(".alllist_title").mouseout(function() {
	$(".alllist").removeClass("btn_alllist")
})

$(".rightnavbar>ul>li").mousemove(function() {
	// console.log($(this).children("img"))
	$(this).children("img").addClass('btn_publicnumber').removeClass('htn_publicnumber')
})
$(".rightnavbar>ul>li").mouseout(function() {
	// console.log($(this).children("img"))
	$(this).children("img").addClass('htn_publicnumber').removeClass('btn_publicnumber')
})

$(".tell").mousemove(function() {
	// console.log($(this).children("img"))
	$(this).addClass('btn_tell').removeClass('htn_publicnumber')
})
$(".tell").mouseout(function() {
	// console.log($(this).children("img"))
	$(this).addClass('htn_publicnumber').removeClass('btn_tell')
})

var num=60;

setInterval(function(){
	num--;
	$("#seconds").text(num)
	if (num==-1) {
		$("#tip").html("二维码已失效,请刷新页面重新获取")
		$("#qrcode").attr("src","/home/img/qrcode.png")
	}
},1000)


$.ajax({
	type: "GET",
	url: "/api/blogrollapi", //+tab,
	dataType: "json",
	success: function(data) {
		// console.log(data.data)
		var msg = data.data;

		for(var i=0;i<msg.length;i++){
			var htmlfriendshiplinks='<li><a href="'+msg[i].link+'" target="_blank">'+msg[i].name+'</a></li>'
			$(".friendshiplinks").append(htmlfriendshiplinks)
		}



	}
});




// 登录
var wxtoken,token1="token1",token2="token2";
$.ajax({
	type: "POST",
	async: false,
	url: "/api/wxtoken", //+tab,
	data:{settoken:1},
	dataType: "json",
	success: function(data) {
		wxtoken=data.data
		if(data.code==1){
			localStorage.setItem("token",JSON.stringify(wxtoken))
		}
		
	}
});

var token=JSON.parse(localStorage.getItem("token"));
if(token!=null){
	$.ajax({
		type: "POST",
		url: "/api/userinfo", //+tab,
		data:"token="+token,
		dataType: "json",
		success: function(data) {
// 			if(data.data.mobile==""){
// 				
// 				window.open("/home/user/email","_self")
// 			}
			if(data.code==1){
				var htmllogin='<li>欢迎'+(data.data.nickname==undefined?data.data.mobile:data.data.nickname)+'</li><li>消息</li><li class="signout">退出</li>'
							$(".loginbefor").addClass("dp")
							$(".loginafter").append(htmllogin).removeClass("dp")
				            $(".signout").click(function(){
				
								$(".loginbefor").removeClass("dp")
								$(".loginafter").append(htmllogin).addClass("dp");
								localStorage.removeItem("token");
								 if(token==wxtoken){
									 $.ajax({
									 	type: "POST",
									 	url: "/api/wxtoken", //+tab,
									 	data:{settoken:2},
										dataType: "json",
									 	success: function(data) {
											location.reload()
									 	}
									 });
								 }else{
									 location.reload()
								 }
								 
							})
				
				
			}else if(data.code==-1){
				localStorage.removeItem("token")
			}
			
			

		}
	});
}

if(token){
	$(".personal").mousemove(function() {
	$(".personal>a>.triangle>i").addClass("rota").removeClass("rota_2")
	$(".personal_list").addClass("bl")
})
$(".personal").mouseout(function() {
	$(".personal>a>.triangle>i").removeClass("rota").addClass("rota_2")
	$(".personal_list").removeClass("bl")
})
$(".member").mousemove(function() {
	$(".member>a>.triangle>i").addClass("rota").removeClass("rota_2")
})
$(".member").mouseout(function() {
	$(".member>a>.triangle>i").addClass("rota_2").removeClass("rota")
})
}else{
	$(".triangle a").attr("href","")
}