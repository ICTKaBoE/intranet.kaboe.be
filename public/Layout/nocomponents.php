<!DOCTYPE html>
<html lang="{{site.language}}" dir="{{site.direction}}">

<head>
	{{load:head}}
	<title>{{site.title}}</title>
	{{content:page:css}}
</head>

<body>
	{{content:page}}
	{{component:modal}}
	{{content:page:js}}
	{{load:body}}
</body>

</html>