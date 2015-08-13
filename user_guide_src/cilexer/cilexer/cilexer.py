# CodeIgniter
# http://codeigniter.com
# 
# An open source application development framework for PHP
# 
# This content is released under the MIT License (MIT)
#
# Copyright (c) 2014 - 2015, British Columbia Institute of Technology
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#
# Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
# Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
#
# http://opensource.org/licenses/MIT	MIT License

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
