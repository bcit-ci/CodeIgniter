import re
import copy

from pygments.lexer import DelegatingLexer
from pygments.lexers.web import PhpLexer, HtmlLexer

__all__ = ['CodeIgniterLexer']


class CodeIgniterLexer(DelegatingLexer):
    """
    Handles HTML, PHP, JavaScript, and CSS is highlighted
    PHP is highlighted with the "startline" option
    """

    name = 'CodeIgniter'
    aliases = ['ci', 'codeigniter']
    filenames = ['*.html', '*.css', '*.php', '*.xml', '*.static']
    mimetypes = ['text/html', 'application/xhtml+xml']

    def __init__(self, **options):
        super(CodeIgniterLexer, self).__init__(HtmlLexer,
                                               PhpLexer,
                                               startinline=True)
