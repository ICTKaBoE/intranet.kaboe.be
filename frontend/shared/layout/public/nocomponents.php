<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}" data-bs-layout="fluid" data-bs-theme="{{layout:theme}}">

<head>
	{{load:head}}
	<title>{{setting:site.title.default}}</title>
	{{content:page:css}}
</head>

<body>
	{{content:page}}
	{{component:toast}};

	<script>
		let pageId = "{{page:id}}";
		let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
	</script>

	{{component:modal}}
	{{component:toast}}

	{{content:page:js}}
	{{load:body}}
</body>

</html>