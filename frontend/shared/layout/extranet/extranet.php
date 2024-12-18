<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}">

<head>
    {{load:head}}
    <title>{{setting:site.title.extranet}}</title>
    {{content:page:css}}
</head>

<body data-bs-theme="{{layout:theme}}">
    <div class="page">
        {{component:navigation?mode=extranet}}

        <div class="page-wrapper">
            {{component:pagetitle?mode=extranet}}

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

    {{load:body}}
    {{content:page:js}}
</body>

</html>