<!DOCTYPE html>
<html lang="{{site.language}}" dir="{{site.direction}}">

<head>
	{{load:head}}
	<title>{{site.title}}</title>
	{{content:page:css}}
</head>

<body>
	{{component:generalMessage}}
	{{content:page}}
	{{component:modal}}
	{{component:toast}}

	<script>
		let pageId = "{{page:id}}";
	</script>

	{{content:page:js}}
	{{load:body}}
</body>

</html>