# CodeIgniter
# http://codeigniter.com
# 
# An open source application development framework for PHP 5.2.4 or newer
# 
# NOTICE OF LICENSE
# 
# Licensed under the Open Software License version 3.0
# 
# This source file is subject to the Open Software License (OSL 3.0) that is
# bundled with this package in the files license.txt / license.rst.  It is
# also available through the world wide web at this URL:
# http://opensource.org/licenses/OSL-3.0
# If you did not receive a copy of the license and are unable to obtain it
# through the world wide web, please send an email to
# licensing@ellislab.com so we can send you a copy immediately.
# 
# Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
# http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)



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
