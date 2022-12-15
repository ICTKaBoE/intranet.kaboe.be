<!DOCTYPE html>
<html lang="{{site.language}}" dir="{{site.direction}}">

<head>
	{{load:head}}
	<title>{{site.title}}</title>
	{{content:page:css}}
</head>

<body class="theme-light">
	<div class="page">
		<div class="sticky-top">
			{{component:header}}
			{{component:navbar}}
		</div>
		<div class="page-wrapper">
			{{component:pagetitle}}
			<div class="page-body">
				<div class="container-fluid">
					{{content:page}}
				</div>
			</div>
			{{component:footer}}
		</div>
	</div>

	{{component:modal}}
	{{content:page:js}}
	{{load:body}}

	<script>
		let pageId = "{{page:id}}";
	</script>
</body>

</html>