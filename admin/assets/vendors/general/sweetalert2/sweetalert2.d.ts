declare module 'sweetalert2' {
    /**
     * Shorthand function to display a simple SweetAlert modal.
     *
     * ex.
     *   import swal from 'sweetalert2';
     *   swal('The Internet?', 'That thing is still around?', 'question');
     */
    function swal(title: string, message?: string, type?: SweetAlertType): Promise<SweetAlertResult>;

    /**
     * Function to display a SweetAlert modal, with an object of options, all being optional.
     * See the SweetAlertOptions interface for the list of accepted fields and values.
     *
     * ex.
     *   import swal from 'sweetalert2';
     *   swal({
     *     title: 'Auto close alert!',
     *     text: 'I will close in 2 seconds.',
     *     timer: 2000
     *   })
     */
    function swal(settings: SweetAlertOptions & { useRejections?: false }): Promise<SweetAlertResult>;

    /**
     * @deprecated
     * swal() overload for legacy alerts that use { useRejections: true }.
     */
    function swal(settings: SweetAlertOptions & { useRejections: true }): Promise<any>;

    /**
     * A namespace inside the default function, containing utility function for controlling the currently-displayed
     * modal.
     *
     * ex.
     *   import swal from 'sweetalert2';
     *
     *   swal('Hey user!', 'I don\'t like you.', 'warning');
     *
     *   if(swal.isVisible()) { // instant regret
     *     swal.close();
     *   }
     */
    namespace swal {
        /**
         * Reuse configuration by creating a swal instance.
         *
         * @param options the default options to set for this instance.
         */
        function mixin(options?: SweetAlertOptions): typeof swal;

        /**
         * Determines if a modal is shown.
         * Be aware that the library returns a trueish or falsy value, not a real boolean.
         */
        function isVisible(): boolean;

        /**
         * @deprecated
         * If you end up using a lot of the same settings when calling SweetAlert2,
         * you can use setDefaults at the start of your program to set them once and for all!
         *
         * @param defaultOptions The default options to set for all modals.
         */
        function setDefaults(defaultOptions: SweetAlertOptions): void;

        /**
         * @deprecated
         * Resets settings to their default value.
         */
        function resetDefaults(): void;

        /**
         * Closes the currently open SweetAlert2 modal programmatically.
         *
         * @param onComplete An optional callback to be called when the alert has finished closing.
         */
        function close(onComplete?: (modalElement: HTMLElement) => void): void;

        /**
         * Gets the modal title.
         */
        function getTitle(): HTMLElement;

        /**
         * Gets the modal content.
         */
        function getContent(): HTMLElement;

        /**
         * Gets the image.
         */
        function getImage(): HTMLElement;

        /**
         * Gets the close button.
         */
        function getCloseButton(): HTMLElement;

        /**
         * Gets all icons. The current visible icon will have `style="display: flex"`,
         * all other will be hidden by `style="display: none"`.
         */
        function getIcons(): Array<HTMLElement>;

        /**
         * Gets the "Confirm" button.
         */
        function getConfirmButton(): HTMLElement;

        /**
         * Gets the "Cancel" button.
         */
        function getCancelButton(): HTMLElement;

        /**
         * Gets the buttons wrapper.
         */
        function getButtonsWrapper(): HTMLElement;

        /**
         * Gets actions (buttons) wrapper.
         */
        function getActions(): HTMLElement;

        /**
         * Gets the modal footer.
         */
        function getFooter(): HTMLElement;

        /**
         * Gets all focusable elements in the popup.
         */
        function getFocusableElements(): Array<HTMLElement>;

        /**
         * Enables "Confirm" and "Cancel" buttons.
         */
        function enableButtons(): void;

        /**
         * Disables "Confirm" and "Cancel" buttons.
         */
        function disableButtons(): void;

        /**
         * Enables the "Confirm"-button only.
         */
        function enableConfirmButton(): void;

        /**
         * Disables the "Confirm"-button only.
         */
        function disableConfirmButton(): void;

        /**
         * Disables buttons and show loader. This is useful with AJAX requests.
         */
        function showLoading(): void;

        /**
         * Enables buttons and hide loader.
         */
        function hideLoading(): void;

        /**
         * Determines if modal is in the loading state.
         */
        function isLoading(): boolean;

        /**
         * Clicks the "Confirm"-button programmatically.
         */
        function clickConfirm(): void;

        /**
         * Clicks the "Cancel"-button programmatically.
         */
        function clickCancel(): void;

        /**
         * Shows a validation error message.
         *
         * @param error The error message.
         * @deprecated
         */
        function showValidationError(error: string): void;

        /**
         * Hides validation error message.
         *
         * @deprecated
         */
        function resetValidationError(): void;

        /**
         * Shows a validation message.
         *
         * @param validationMessage The validation message.
         */
        function showValidationMessage(validationMessage: string): void;

        /**
         * Hides validation message.
         */
        function resetValidationMessage(): void;

        /**
         * Gets the input DOM node, this method works with input parameter.
         */
        function getInput(): HTMLElement;

        /**
         * Disables the modal input. A disabled input element is unusable and un-clickable.
         */
        function disableInput(): void;

        /**
         * Enables the modal input.
         */
        function enableInput(): void;

        /**
         * Gets the validation message container.
         */
        function getValidationMessage(): HTMLElement;

        /**
         * If `timer` parameter is set, returns number of milliseconds of timer remained.
         * Otherwise, returns undefined.
         */
        function getTimerLeft(): number | undefined;

        /**
         * Stop timer. Returns number of milliseconds of timer remained.
         * If `timer` parameter isn't set, returns undefined.
         */
        function stopTimer(): number | undefined;

        /**
         * Resume timer. Returns number of milliseconds of timer remained.
         * If `timer` parameter isn't set, returns undefined.
         */
        function resumeTimer(): number | undefined;

        /**
         * Toggle timer. Returns number of milliseconds of timer remained.
         * If `timer` parameter isn't set, returns undefined.
         */
        function toggleTimer(): number | undefined;

        /**
         * Check if timer is running. Returns true if timer is running,
         * and false is timer is paused / stopped.
         * If `timer` parameter isn't set, returns undefined.
         */
        function isTimerRunning(): boolean | undefined;

        /**
         * Increase timer. Returns number of milliseconds of an updated timer.
         * If `timer` parameter isn't set, returns undefined.
         *
         * @param n The number of milliseconds to add to the currect timer
         */
        function increaseTimer(n: number): number | undefined;

        /**
         * Provide an array of SweetAlert2 parameters to show multiple modals, one modal after another.
         *
         * @param steps The steps' configuration.
         */
        function queue(steps: Array<SweetAlertOptions | string>): Promise<any>;

        /**
         * Gets the index of current modal in queue. When there's no active queue, null will be returned.
         */
        function getQueueStep(): string | null;

        /**
         * Inserts a modal in the queue.
         *
         * @param step  The step configuration (same object as in the swal() call).
         * @param index The index to insert the step at.
         *              By default a modal will be added to the end of a queue.
         */
        function insertQueueStep(step: SweetAlertOptions, index?: number): number;

        /**
         * Deletes the modal at the specified index in the queue.
         *
         * @param index The modal index in the queue.
         */
        function deleteQueueStep(index: number): void;

        /**
         * Gets progress steps.
         */
        function getProgressSteps(): string[];

        /**
         * Sets progress steps.
         *
         * @param steps The modal steps
         */
        function setProgressSteps(steps: string[]): void;

        /**
         * Shows progress steps.
         */
        function showProgressSteps(): void;

        /**
         * Hides progress steps.
         */
        function hideProgressSteps(): void;

        /**
         * Determines if a given parameter name is valid.
         *
         * @param paramName The parameter to check
         */
        function isValidParameter(paramName: string): boolean;

        /**
         * Normalizes the arguments you can give to swal() in an object of type SweetAlertOptions.
         * ex:
         *     swal.argsToParams(['title', 'text']); //=> { title: 'title', text: 'text' }
         *     swal.argsToParams({ title: 'title', text: 'text' }); //=> { title: 'title', text: 'text' }
         *
         * @param params The array of arguments to normalize.
         */
        function argsToParams(params: SweetAlertArrayOptions | [SweetAlertOptions]): SweetAlertOptions;

        /**
         * An utility function to make SweetAlert rejections silencious (no error in the console when clicking Cancel).
         * ex. swal(...).catch(swal.noop)
         */
        function noop(): void;

        /**
         * An enum of possible reasons that can explain an alert dismissal.
         */
        enum DismissReason {
            cancel, backdrop, close, esc, timer
        }
    }

    export type SweetAlertType = 'success' | 'error' | 'warning' | 'info' | 'question';

    export interface SweetAlertResult {
        value?: any;
        dismiss?: swal.DismissReason;
    }

    type SyncOrAsync<T> = T | Promise<T>;

    type ValueOrThunk<T> = T | (() => T);

    export type SweetAlertArrayOptions = [string] | [string, string] | [string, string, string];

    export interface SweetAlertOptions {
        /**
         * The title of the modal, as HTML.
         * It can either be added to the object under the key "title" or passed as the first parameter of the function.
         *
         * @default null
         */
        title?: string;

        /**
         * The title of the modal, as text. Useful to avoid HTML injection.
         *
         * @default null
         */
        titleText?: string;

        /**
         * A description for the modal.
         * It can either be added to the object under the key "text" or passed as the second parameter of the function.
         *
         * @default null
         */
        text?: string;

        /**
         * A HTML description for the modal.
         * If "text" and "html" parameters are provided in the same time, "text" will be used.
         *
         * @default null
         */
        html?: string | HTMLElement | JQuery;

        /**
         * The footer of the modal, as HTML.
         *
         * @default null
         */
        footer?: string | JQuery;

        /**
         * The type of the modal.
         * SweetAlert2 comes with 5 built-in types which will show a corresponding icon animation: 'warning', 'error',
         * 'success', 'info' and 'question'.
         * It can either be put in the array under the key "type" or passed as the third parameter of the function.
         *
         * @default null
         */
        type?: SweetAlertType;

        /**
         * Whether or not SweetAlert2 should show a full screen click-to-dismiss backdrop.
         * Either a boolean value or a css background value (hex, rgb, rgba, url, etc.)
         *
         * @default true
         */
        backdrop?: boolean | string;

        /**
         * Whether or not an alert should be treated as a toast notification.
         * This option is normally coupled with the `position` parameter and a timer.
         * Toasts are NEVER autofocused.
         *
         * @default false
         */
        toast?: boolean;

        /**
         * The container element for adding modal into (query selector only).
         *
         * @default 'body'
         */
        target?: string;

        /**
         * Input field type, can be text, email, password, number, tel, range, textarea, select, radio, checkbox, file
         * and url.
         *
         * @default null
         */
        input?:
            'text' | 'email' | 'password' | 'number' | 'tel' | 'range' | 'textarea' | 'select' | 'radio' | 'checkbox' |
            'file' | 'url';

        /**
         * Modal window width, including paddings (box-sizing: border-box). Can be in px or %.
         *
         * @default null
         */
        width?: number | string;

        /**
         * Modal window padding.
         *
         * @default null
         */
        padding?: number;

        /**
         * Modal window background (CSS background property).
         *
         * @default '#fff'
         */
        background?: string;

        /**
         * Modal window position
         *
         * @default 'center'
         */
        position?:
            'top' | 'top-start' | 'top-end' | 'top-left' | 'top-right' |
            'center' | 'center-start' | 'center-end' | 'center-left' | 'center-right' |
            'bottom' | 'bottom-start' | 'bottom-end' | 'bottom-left' | 'bottom-right';

        /**
         * Modal window grow direction
         *
         * @default false
         */
        grow?: 'row' | 'column' | 'fullscreen' | false;

        /**
         * A custom CSS class for the modal.
         *
         * @default ''
         */
        customClass?: string;

        /**
         * A custom CSS class for the container.
         *
         * @default ''
         */
        customContainerClass?: string;

        /**
         * Auto close timer of the modal. Set in ms (milliseconds).
         *
         * @default null
         */
        timer?: number;

        /**
         * If set to false, modal CSS animation will be disabled.
         *
         * @default true
         */
        animation?: ValueOrThunk<boolean>;

        /**
         * By default, SweetAlert2 sets html's and body's CSS height to auto !important.
         * If this behavior isn't compatible with your project's layout, set heightAuto to false.
         *
         * @default true
         */
        heightAuto?: boolean;

        /**
         * If set to false, the user can't dismiss the modal by clicking outside it.
         * You can also pass a custom function returning a boolean value, e.g. if you want
         * to disable outside clicks for the loading state of a modal.
         *
         * @default true
         */
        allowOutsideClick?: ValueOrThunk<boolean>;

        /**
         * If set to false, the user can't dismiss the modal by pressing the Escape key.
         * You can also pass a custom function returning a boolean value, e.g. if you want
         * to disable the escape key for the loading state of a modal.
         *
         * @default true
         */
        allowEscapeKey?: ValueOrThunk<boolean>;

        /**
         * If set to false, the user can't confirm the modal by pressing the Enter or Space keys,
         * unless they manually focus the confirm button.
         * You can also pass a custom function returning a boolean value.
         *
         * @default true
         */
        allowEnterKey?: ValueOrThunk<boolean>;

        /**
         * If set to false, SweetAlert2 will allow keydown events propagation to the document.
         *
         * @default true
         */
        stopKeydownPropagation?: boolean;

        /**
         * Useful for those who are using SweetAlert2 along with Bootstrap modals.
         * By default keydownListenerCapture is false which means when a user hits Esc,
         * both SweetAlert2 and Bootstrap modals will be closed.
         * Set keydownListenerCapture to true to fix that behavior.
         *
         * @default false
         */
        keydownListenerCapture?: boolean;

        /**
         * If set to false, a "Confirm"-button will not be shown.
         * It can be useful when you're using custom HTML description.
         *
         * @default true
         */
        showConfirmButton?: boolean;

        /**
         * If set to true, a "Cancel"-button will be shown, which the user can click on to dismiss the modal.
         *
         * @default false
         */
        showCancelButton?: boolean;

        /**
         * Use this to change the text on the "Confirm"-button.
         *
         * @default 'OK'
         */
        confirmButtonText?: string;

        /**
         * Use this to change the text on the "Cancel"-button.
         *
         * @default 'Cancel'
         */
        cancelButtonText?: string;

        /**
         * Use this to change the background color of the "Confirm"-button (must be a HEX value).
         *
         * @default '#3085d6'
         */
        confirmButtonColor?: string;

        /**
         * Use this to change the background color of the "Cancel"-button (must be a HEX value).
         *
         * @default '#aaa'
         */
        cancelButtonColor?: string;

        /**
         * A custom CSS class for the "Confirm"-button.
         *
         * @default null
         */
        confirmButtonClass?: string;

        /**
         * A custom CSS class for the "Cancel"-button.
         *
         * @default null
         */
        cancelButtonClass?: string;

        /**
         * Use this to change the aria-label for the "Confirm"-button.
         *
         * @default ''
         */
        confirmButtonAriaLabel?: string;

        /**
         * Use this to change the aria-label for the "Cancel"-button.
         *
         * @default ''
         */
        cancelButtonAriaLabel?: string;

        /**
         * Whether to apply the default swal2 styling to buttons.
         * If you want to use your own classes (e.g. Bootstrap classes) set this parameter to false.
         *
         * @default true
         */
        buttonsStyling?: boolean;

        /**
         * Set to true if you want to invert default buttons positions.
         *
         * @default false
         */
        reverseButtons?: boolean;

        /**
         * Set to false if you want to focus the first element in tab order instead of "Confirm"-button by default.
         *
         * @default true
         */
        focusConfirm?: boolean;

        /**
         * Set to true if you want to focus the "Cancel"-button by default.
         *
         * @default false
         */
        focusCancel?: boolean;

        /**
         * Set to true to show close button in top right corner of the modal.
         *
         * @default false
         */
        showCloseButton?: boolean;

        /**
         * Use this to change the `aria-label` for the close button.
         *
         * @default 'Close this dialog'
         */
        closeButtonAriaLabel?: string;

        /**
         * Set to true to disable buttons and show that something is loading. Useful for AJAX requests.
         *
         * @default false
         */
        showLoaderOnConfirm?: boolean;

        /**
         * Function to execute before confirm, may be async (Promise-returning) or sync.
         *
         * ex.
         *   swal({
         *    title: 'Multiple inputs',
         *    html:
         *      '<input id="swal-input1" class="swal2-input">' +
         *      '<input id="swal-input2" class="swal2-input">',
         *    focusConfirm: false,
         *    preConfirm: () => [
         *      document.querySelector('#swal-input1').value,
         *      document.querySelector('#swal-input2').value
         *    ]
         *  }).then(result => swal(JSON.stringify(result));
         *
         * @default null
         */
        preConfirm?: (inputValue: any) => SyncOrAsync<any | void>;

        /**
         * Add a customized icon for the modal. Should contain a string with the path or URL to the image.
         *
         * @default null
         */
        imageUrl?: string;

        /**
         * If imageUrl is set, you can specify imageWidth to describes image width in px.
         *
         * @default null
         */
        imageWidth?: number;

        /**
         * If imageUrl is set, you can specify imageHeight to describes image height in px.
         *
         * @default null
         */
        imageHeight?: number;

        /**
         * An alternative text for the custom image icon.
         *
         * @default ''
         */
        imageAlt?: string;

        /**
         * A custom CSS class for the customized icon.
         *
         * @default null
         */
        imageClass?: string;

        /**
         * Input field placeholder.
         *
         * @default ''
         */
        inputPlaceholder?: string;

        /**
         * Input field initial value.
         *
         * @default ''
         */
        inputValue?: SyncOrAsync<string>;

        /**
         * If input parameter is set to "select" or "radio", you can provide options.
         * Object keys will represent options values, object values will represent options text values.
         */
        inputOptions?: SyncOrAsync<Map<string, string> | { [inputValue: string]: string }>;

        /**
         * Automatically remove whitespaces from both ends of a result string.
         * Set this parameter to false to disable auto-trimming.
         *
         * @default true
         */
        inputAutoTrim?: boolean;

        /**
         * HTML input attributes (e.g. min, max, step, accept...), that are added to the input field.
         *
         * ex.
         *   swal({
         *     title: 'Select a file',
         *     input: 'file',
         *     inputAttributes: {
         *       accept: 'image/*'
         *     }
         *   })
         *
         * @default null
         */
        inputAttributes?: { [attribute: string]: string; };

        /**
         * Validator for input field, may be async (Promise-returning) or sync.
         *
         * ex.
         *   swal({
         *     title: 'Select color',
         *     input: 'radio',
         *     inputValidator: result => !result && 'You need to select something!'
         *   })
         *
         * @default null
         */
        inputValidator?: (inputValue: string) => SyncOrAsync<string | null>;

        /**
         * A custom validation message for default validators (email, url).
         *
         * ex.
         *   swal({
         *     input: 'email',
         *     validationMessage: 'Adresse e-mail invalide'
         *   })
         *
         * @default null
         */
        validationMessage?: string;

        /**
         * A custom CSS class for the input field.
         *
         * @default null
         */
        inputClass?: string;

        /**
         * Progress steps, useful for modal queues, see usage example.
         *
         * @default []
         */
        progressSteps?: string[];

        /**
         * Current active progress step.
         *
         * @default swal.getQueueStep()
         */
        currentProgressStep?: string;

        /**
         * Distance between progress steps.
         *
         * @default null
         */
        progressStepsDistance?: string;

        /**
         * Function to run when modal built, but not shown yet. Provides modal DOM element as the first argument.
         *
         * @default null
         */
        onBeforeOpen?: (modalElement: HTMLElement) => void;

        /**
         * Function to run after modal has been disposed.
         *
         * @default null
         */
        onAfterClose?: () => void;

        /**
         * Function to run when modal opens, provides modal DOM element as the first argument.
         *
         * @default null
         */
        onOpen?: (modalElement: HTMLElement) => void;

        /**
         * Function to run when modal closes, provides modal DOM element as the first argument.
         *
         * @default null
         */
        onClose?: (modalElement: HTMLElement) => void;

        /**
         * Determines whether given `inputValidator` and `preConfirm` functions should be expected to to signal
         * validation message by rejecting, or by their respective means (see documentation for each option).
         *
         * @default false
         * @deprecated
         */
        expectRejections?: boolean;
    }

    export default swal;
}

/**
 * These interfaces aren't provided by SweetAlert2, but its definitions use them.
 * They will be merged with 'true' definitions.
 */

// tslint:disable:no-empty-interface

interface JQuery {
}

interface Promise<T> {
}

interface Map<K, V> {
}
