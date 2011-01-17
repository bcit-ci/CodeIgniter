/**
 * This is the base URL of the note hosting application
 */
var base_url = 'http://ci-notes-exp.katzgrau.com/';

/**
 * This is the version number of the current set of notes
 */
var version = '2.0.0';

/**
 * This will hold the key of the currently viewed document
 */
var document_key = '';

/**
 * Include the js/json that contains the notes
 */
function include_notes(key)
{
    document_key = key;
    var url      = base_url + 'version/' + version + '/documents/' + key + '/notes.js';

    document.write('<sc' + 'ript src="' + url +'"></scr' + 'ipt>');
}

/**
 * Load the actual content into the DOM
 */
function load_notes()
{
    var note_container = document.getElementById('note_section');

    /* In case the user is offline */
    if(typeof user_guide_notes == 'undefined')
    {
        note_container.innerHTML = 'The notes are not accesible. You may be offline';
        hide_add_link();
    }

    /* Show the add link */
    set_add_link();

    /* Is this doc commentable? */
    if(user_guide_notes === false)
    {
        document.getElementById('note_block').style.display = 'none';
    }

    /* No notes? say so */
    if(user_guide_notes.length == 0)
    {
        note_container.innerHTML = 'There are no notes for this document yet.';
    }

    var list, head, body, item;
    list = document.createElement('ul');

    /* Load the content into DOM elements (in memory), then display everything
     * at once
     */
    for(var i = 0; i < user_guide_notes.length; i++)
    {
        head = document.createElement('h4');
        body = document.createElement('div');
        item = document.createElement('li');

        head.setAttribute('class', 'note_header');
        body.setAttribute('class', 'note_body');

        head.innerHTML = user_guide_notes[i].email + ' at ' + user_guide_notes[i].created;
        body.innerHTML = user_guide_notes[i].body;

        item.appendChild(head);
        item.appendChild(body);
        list.appendChild(item);
    }

    var old = document.getElementById('notes');

    /* Display */
    note_container.replaceChild(list, old);
}

/* Set the location of the 'Add Note' link */
function set_add_link()
{
    var link  = document.getElementById('add_note');
    link.href = base_url + 'version/' + version + '/documents/'+ document_key +'/notes/add';
}

/* Hide the link to add a note */
function hide_add_link()
{
    var link  = document.getElementById('add_note');
    link.style.display = 'none';
}