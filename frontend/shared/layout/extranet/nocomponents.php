<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}">

<head>
	{{load:head}}
	<title>{{setting:site.title.extranet}}</title>
	{{content:page:css}}
</head>

<body data-bs-theme="{{layout:theme}}">
	{{content:page}}

	{{component:toast}};

	<script>
		let pageId = "{{page:id}}";
		let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
	</script>

	{{content:page:js}}
	{{load:body}}
</body>

</html>