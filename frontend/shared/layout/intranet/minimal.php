<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}" data-bs-layout="fluid" data-bs-theme="{{layout:theme}}">

<head>
    {{load:head}}
    <title>{{setting:site.title.intranet}}</title>
    {{content:page:css}}
</head>

<body>
    <div class="page">
        {{component:header?mode=minimal}}
        <div class="page-wrapper">
            {{component:generalMessage}}
            <main class="page-body">
                <div class="container-fluid">
                    {{content:page}}
                </div>
            </main>
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