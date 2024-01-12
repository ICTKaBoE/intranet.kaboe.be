export default class TinyMCE {
    static INSTANCES = {};

    constructor(element) {
        this.element = element;
        this.id = this.element.id || false;
        this.disabled = this.element.hasAttribute("disabled");

        this.init();
    }

    static ScanAndCreate = () => {
        $("[role='tinymce']").each((ids, el) => {
            if (!TinyMCE.INSTANCES.hasOwnProperty(el.getAttribute("id")))
                TinyMCE.INSTANCES[el.getAttribute("id")] = new TinyMCE(el);
        });
    };

    init = () => {
        let options = {
            selector: `#${this.id}`,
            height: 300,
            menubar: false,
            statusbar: false,
            plugins: [
                "advlist",
                "autolink",
                "lists",
                "link",
                "image",
                "charmap",
                "preview",
                "anchor",
                "searchreplace",
                "visualblocks",
                "code",
                "fullscreen",
                "insertdatetime",
                "media",
                "table",
                "code",
                "help",
                "wordcount",
            ],
            toolbar:
                "undo redo | image table | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
            content_style:
                "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }",
        };

        tinymce.init(options);
        if (this.disabled) this.disable();
    };

    setValue = (value) => {
        tinymce.get(this.id).setContent(value);
    };

    getValue = () => tinymce.get(this.id).getContent();

    enable = () => {
        tinymce.get(this.id).mode.set("design");
    };

    disable = () => {
        tinymce.get(this.id).mode.set("readonly");
    };
}
