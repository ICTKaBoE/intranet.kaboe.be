<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}">

<head>
	{{load:head}}
	<title>{{setting:site.title}}</title>
	{{content:page:css}}
</head>

<body>
	{{content:page}}

	<script>
		let pageId = "{{page:id}}";
		let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
	</script>

	{{content:page:js}}
	{{load:body}}
</body>

</html>