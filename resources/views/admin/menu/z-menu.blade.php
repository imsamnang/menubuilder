<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<title>Simple Management Menu</title>
	<link rel="stylesheet" type="text/css" href="{{asset('/css/style.css')}}">
	{{-- <link rel="stylesheet" href="{{ asset('assets/font-awesome/4.5.0/css/font-awesome.min.css') }}" /> --}}
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"  />
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<div class="container">
		<div id="load"></div>
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="card">
					<div class="card-header">Management Menu</div>
					<div class="card-body">
							<form class="form-horizontal" action="/action_page.php">
									<input type="hidden" id="id">
									<input type="hidden" id="nestable-output">
									<div class="form-group">
											<label class="control-label col-sm-2" for="label">label:</label>
											<div class="col-sm-12">
													<input type="text" class="form-control" id="label" placeholder="Enter label"
															name="label">
											</div>
									</div>

									<div class="form-group">
											<label class="control-label col-sm-2" for="link">link:</label>
											<div class="col-sm-12">
													<input type="text" class="form-control" id="link" placeholder="Enter link"
															name="link">
											</div>
									</div>

									<div class="form-group">
											<div class="col-sm-offset-2 col-sm-12">
													<button type="reset" id="reset" class="btn btn-default">Reset</button>
													<button type="submit" id="submit" class="btn btn-primary pull-right">Submit</button>
											</div>
									</div>
							</form>
					</div>
					<div class="card-body">
							<menu id="nestable-menu">
									<button type="button" class="btn" data-action="expand-all">Expand All</button>
									<button type="button" class="btn" data-action="collapse-all">Collapse All</button>
							</menu>
					</div>
					<div class="card-body">
							<div class="cf nestable-lists">
									<div class="dd" id="nestable">
											@php
											function get_menu($items,$class = 'dd-list') {
											$html = "<ol class=\"".$class."\" id=\"menu-id\">";
													foreach($items as $key=>$value) {
													$html.= '<li class="dd-item dd3-item" data-id="'.$value['id'].'">
															<div class="dd-handle dd3-handle"></div>
															<div class="dd3-content"><span
																			id="label_show'.$value['id'].'">'.$value['label'].'</span>
																	<span class="span-right">/<span
																					id="link_show'.$value['id'].'">'.$value['link'].'</span>
																			&nbsp;&nbsp;
																			<a class="edit-button" id="'.$value['id'].'" label="'.$value['label'].'"
																					link="'.$value['link'].'"><i class="fa fa-pencil"></i></a>
																			<a class="del-button" id="'.$value['id'].'"><i
																							class="fa fa-trash"></i></a></span>
															</div>';
															if(array_key_exists('child',$value)) {
															$html .= get_menu($value['child'],'child');
															}
															$html .= "
													</li>";
													}
													$html .= "</ol>";
											return $html;
											}
											print get_menu($menu);
											@endphp

									</div>
							</div>
					</div>
					<div class="card-footer">
							<button class="btn btn-success pull-right" id="save">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="{{ asset('/js/jquery-2.1.4.min.js') }}"></script>
	<script src="{{asset('/js/jquery.nestable.js')}}"></script>
	<script>
		$(document).ready(function(){
			$("#load").hide();
			var updateOutput = function(e)
			{
				var list   = e.length ? e : $(e.target),
					output = list.data('output');
				if (window.JSON) {
					output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
				} else {
					output.val('JSON browser support required for this demo.');
				}
			};
			// activate Nestable for list 1
			$('#nestable').nestable({ group: 1})
			.on('change', updateOutput);
			// output initial serialised data
			updateOutput($('#nestable').data('output', $('#nestable-output')));
			$('#nestable-menu').on('click', function(e)
			{
				var target = $(e.target),
					action = target.data('action');
				if (action === 'expand-all') {
					$('.dd').nestable('expandAll');
				}
				if (action === 'collapse-all') {
					$('.dd').nestable('collapseAll');
				}
			});
			
			$("#submit").click(function(e){
					e.preventDefault();
			$("#load").show();
			var dataString = { 
							label : $("#label").val(),
							link : $("#link").val(),
							id : $("#id").val(),
							_token : "{{csrf_token()}}",
							};
					$.ajax({
							type: "POST",
							url: "{{url('/menu/save_menu')}}",
							data: dataString,
							dataType: "json",
							cache : false,
							success: function(response){
								if(response.success){
										var data = response.data;
										if(response.type == 'add'){
												var li = '<li class="dd-item dd3-item" data-id="'+data.id+'" >\
																		<div class="dd-handle dd3-handle"></div>\
																		<div class="dd3-content"><span id="label_show'+data.id+'">'+data.label+'</span>\
																			<span class="span-right">/<span id="link_show'+data.id+'">'+data.link+'</span> &nbsp;&nbsp; \
																					<a class="edit-button" id="'+data.id+'" label="'+data.label+'" link="'+data.link+'" ><i class="fa fa-pencil"></i></a>\
																					<a class="del-button" id="'+data.id+'"><i class="fa fa-trash"></i></a>\
																			</span> \
																		</div>';
												$("#menu-id").append(li);
										} else if(response.type == 'edit'){
												$('#label_show'+data.id).html(data.label);
												$('#link_show'+data.id).html(data.link);
										}
								}
							
								$('#label').val('');
								$('#link').val('');
								$('#id').val('');
								$("#load").hide();
							} ,error: function(xhr, status, error) {
							alert(error);
							},
					});
			});

			$('.dd').on('change', function() {
				$("#load").show();				
				var dataString = { 
						data : $("#nestable-output").val(),
						_token : "{{csrf_token()}}",
						};
				$.ajax({
					type: "POST",
					url: "{{url('/menu/save')}}",
					data: dataString,
					cache : false,
					success: function(data){
					$("#load").hide();
					} ,error: function(xhr, status, error) {
					alert(error);
					},
				});
			});

			$("#save").click(function(){
				$("#load").show();				
				var dataString = { 
						_token : "{{csrf_token()}}",
						data : $("#nestable-output").val(),
						};
				$.ajax({
						type: "POST",
						url: "{{url('/menu/save')}}",
						data: dataString,
						cache : false,
						success: function(data){
						$("#load").hide();
						alert('Data has been saved');
				
						} ,error: function(xhr, status, error) {
						alert(error);
						},
				});
			});
	
			$(document).on("click",".del-button",function() {
					var x = confirm('Delete this menu?');
					var id = $(this).attr('id');
					if(x){
							$("#load").show();
							$.ajax({
									type: "POST",
									url: "{{url('/menu/delete')}}",
									data: { id : id , _token : "{{csrf_token()}}",},
									cache : false,
									success: function(data){
									$("#load").hide();
									$("li[data-id='" + id +"']").remove();
									} ,error: function(xhr, status, error) {
									alert(error);
									},
							});
					}
			});

			$(document).on("click",".edit-button",function() {
					var id = $(this).attr('id');
					var label = $(this).attr('label');
					var link = $(this).attr('link');
					$("#id").val(id);
					$("#label").val(label);
					$("#link").val(link);
			});

			$(document).on("click","#reset",function() {
					$('#label').val('');
					$('#link').val('');
					$('#id').val('');
			});
		});   
	</script>
</body>
</html>