<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}" data-bs-layout="fluid" data-bs-theme="{{layout:theme}}">

<head>
    {{load:head}}
    <title>{{setting:site.title.extranet}}</title>
    {{content:page:css}}
</head>

<body>
    <div class="page">
        {{component:navigation?mode=extranet}}

        <div class="page-wrapper">
            {{component:pagetitle?mode=extranet}}

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

    <script>
        let pageId = "{{page:id}}";
        let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
    </script>

    {{content:page:js}}
    {{load:body}}
</body>

</html>