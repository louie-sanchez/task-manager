var TMP = TMP || {};
(function( $ ) {
	'use strict';

	TMP.extra = {

		init: function() {
			TMP.extra.userList();
		},


		userList: function() {
			var $window = $(window);
			var $body = $('body');
			var	$userList = $('#userList');

			if ($userList.length > 0) {
				$userList.each(function() {
					var listItem = $('#userList ul > li');
					var IDs ;
					var finalIDs = [];
					if(listItem.length){
						finalIDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						jQuery("#finalUserListHere").val(finalIDs);
					}
					$(document).on('input', '#userSearch',function(event) {
						var searchText = $('#userSearch').val();
						if(searchText.length){ $userList.show(); } else{ $userList.hide(); }
						var input, filter, ul, li, a, i;
						input = document.getElementById("userSearch");
						filter = input.value.toUpperCase();
						ul = document.getElementById("userList");
						li = ul.getElementsByTagName("li");
						for (i = 0; i < li.length; i++) {
							a = li[i].getElementsByTagName("a")[0];
							if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
								li[i].style.display = "";
							} else {
								li[i].style.display = "none";
							}
						}
					});
					$(document).on('click', '.tmp #userSearch', function () {
						$userList.show();
					});
					$(document).on('click', '#userList ul > li a', function (e) {
						IDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						var id = $(this).attr("data-id");
						if(IDs.indexOf(id) === -1){
							var name = $(this).attr("data-name");
							var htmlCode = '<li><span class="tab" data-id="'+id+'">'+name+'</span> <i class="close"></i></li>';
							$('#userListAdded ul').append(htmlCode);
							finalIDs.push(id);
							$('#userList').css('display', 'none');
							$('#userSearch').val('');
						}else{
							$('#userList').css('display', 'none');
							$('#userSearch').val('');
							alert('Member Already Added.');
						}
						$("#finalUserListHere").val(finalIDs);
					});
					$(document).on('click', '#userListAdded ul > li i.close', function (e) {
						IDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						var did = $(this).parent().find('span').attr("data-id");
						if(IDs.indexOf(did) > -1){
							$(this).parent().remove();
							finalIDs.splice(IDs.indexOf(did), 1);
						}
						$("#finalUserListHere").val(finalIDs);
					});

				});
			}
		},

	};

	$(document).ready( TMP.extra.init );

})( jQuery );

