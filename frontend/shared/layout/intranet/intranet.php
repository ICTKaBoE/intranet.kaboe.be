<!DOCTYPE html>
<html lang="{{setting:site.language}}" dir="{{setting:site.direction}}">

<head>
    {{load:head}}
    <title>{{setting:site.title.intranet}}</title>
    {{content:page:css}}
</head>

<body data-bs-theme="{{layout:theme}}">
    <div class="page">
        {{component:navbar}}
        {{component:header}}

        <div class="page-wrapper">
            {{component:generalMessage}}

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
    {{component:manual}}


    <script>
        let pageId = "{{page:id}}";
        let siteVersion = ("{{setting:site.version}}").replaceAll(".", "");
    </script>

    {{content:page:js}}
    {{load:body}}
</body>

</html>