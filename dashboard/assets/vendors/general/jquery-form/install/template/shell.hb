# Make sure node modules are available to Github Desktop
PATH=$PATH:/usr/local/bin:/usr/local/sbin

{{command}}{{#if task}} {{task}}{{/if}}{{#if args}} {{args}}{{/if}}
