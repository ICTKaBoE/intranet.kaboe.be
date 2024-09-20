<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}">

<head>
	{{load:head}}
	<title>{{setting:site.title}}</title>
	{{content:page:css}}
</head>

<body>
	<div class="page">
		<div class="page-wrapper">
			{{component:generalMessage}}

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
	{{component:toast}}


	<script>
		let pageId = "{{page:id}}";
		let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
	</script>

	{{content:page:js}}
	{{load:body}}
</body>

</html>