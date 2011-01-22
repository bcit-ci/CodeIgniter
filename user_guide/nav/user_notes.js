/**
 * This is the base URL of the note hosting application
 */
//var base_url = 'http://ci-notes-exp.katzgrau.com/';
var base_url = 'http://user-notes.codeigniter.com/';

/**
 * This is the version number of the current set of notes
 */
var version = '2.0.0';

/**
 * This will hold the key of the currently viewed document
 */
var document_key = '';

/**
 * This is where the official user notes are kept (CodeIgntier Site)
 *  We need this to know where to point 'global' permalinks to
 */

//var official_user_notes = 'file:///Users/katzgrau/Dev/reactor/user_guide/';
var official_user_notes = 'http://codeigniter.com/user_guide/';

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
        return;
    }

    /* Show the add link */
    set_add_link();

    /* Is this doc commentable? */
    if(user_guide_notes === false)
    {
        document.getElementById('note_block').style.display = 'none';
    }

    /* No notes? say so */
    if(user_guide_notes.notes.length == 0)
    {
        note_container.innerHTML = 'There are no notes for this document yet.';
    }

    var list, head, body, item;
    list = document.createElement('ul');

    /* Load the content into DOM elements (in memory), then display everything
     * at once
     */
    for(var i = 0; i < user_guide_notes.notes.length; i++)
    {
        anch = document.createElement('a');
        perm = document.createElement('a');
        head = document.createElement('h4');
        body = document.createElement('div');
        item = document.createElement('li');

        head.setAttribute('class', 'note_header');
        body.setAttribute('class', 'note_body');
        anch.setAttribute('name', 'note-' + user_guide_notes.notes[i].id);
        perm.setAttribute('href', build_permalink(user_guide_notes.notes[i].id, user_guide_notes.document_path));
        perm.setAttribute('class', 'note_permalink');

        head.innerHTML = user_guide_notes.notes[i].byline + ' on ' + user_guide_notes.notes[i].whenline;
        body.innerHTML = user_guide_notes.notes[i].body;
        perm.innerHTML = 'Permalink';

        item.appendChild(anch);
        item.appendChild(head);
        item.appendChild(perm);
        item.appendChild(body);
        list.appendChild(item);
    }

    var old = document.getElementById('notes');

    /* Display */
    note_container.replaceChild(list, old);

}

/**
 * Set the location of the 'Add Note' link
 */
function set_add_link()
{
    var link  = document.getElementById('add_note');
    link.href = base_url + 'version/' + version + '/documents/'+ document_key +'/notes/add';
}

/**
 * Hide the link to add a note
 */
function hide_add_link()
{
    var link  = document.getElementById('add_note');
    link.style.display = 'none';
}

/**
 * Build a permink to a user note, given it's id and the path
 */
function build_permalink(n_id, n_path)
{
   return official_user_notes + n_path + '#note-' + n_id;
}