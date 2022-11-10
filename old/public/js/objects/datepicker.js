class DatePicker {
    static INSTANCES = {};

    constructor(element) {
        this.input = element;
        this.id = this.input.id || false;

        this.init();
    }

    init = () => {
        this.createPicker();
    };

    createPicker = () => {
        let settings = {
            element: this.input,
            lang: 'nl-BE',
            buttonText: {
                previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        };

        this.litePicker = new Litepicker(settings);
    };

    setDate = (date) => {
        this.litePicker.setDate(date);
    };
}