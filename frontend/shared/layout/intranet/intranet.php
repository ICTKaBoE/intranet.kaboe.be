<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}" data-bs-navbar-position="vertical" data-bs-layout="fluid" data-bs-theme="{{layout:theme}}">

<head>
    {{load:head}}
    <title>{{setting:site.title.intranet}}</title>
    {{content:page:css}}
</head>

<body>
    <div class="page">
        {{component:navbar}}
        {{component:header}}

        <div class="page-wrapper">
            {{component:generalMessage}}

            <main class="page-body">
                <div class="container-fluid">
                    {{content:page}}
                </div>
            </main>

            {{component:footer}}
        </div>
    </div>

    {{component:modal}}
    {{component:toast}}
    {{component:manual}}


    <script>
        let pageId = "{{page:id}}";
        let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
    </script>

    {{content:page:js}}
    {{load:body}}
</body>

</html>