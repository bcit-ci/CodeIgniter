<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('Não é permitido acesso direto de script');

$lang['db_invalid_connection_str'] = 'Não foi possível determinar as configurações de banco de dados baseado na string de conexão informada.';
$lang['db_unable_to_connect'] = 'Não foi possível conectar no seu servidor de banco de dados usando as confirgurações informadas.';
$lang['db_unable_to_select'] = 'Não foi possível selecionar o banco de dados especificado: %s';
$lang['db_unable_to_create'] = 'Não foi possível criar o banco de dados especificado: %s';
$lang['db_invalid_query'] = 'A consulta enviada não é válida.';
$lang['db_must_set_table'] = 'Você precisa configurar a tabela do banco que será usada em sua consulta.';
$lang['db_must_use_set'] = 'Você precisa usar o método "set" para atualizar uma entrada.';
$lang['db_must_use_index'] = 'Você precisa especificar um índice .';
$lang['db_batch_missing_index'] = 'Uma ou mais linhas enviadas para atualização em lote falta especificar um índice.';
$lang['db_must_use_where'] = 'Updates não são permitidos a menos que contenha "where"';
$lang['db_del_must_use_where'] = 'Deletar não é permitido a menos que contenha "where"ou "like".';
$lang['db_field_param_missing'] = 'Para buscar campos é necessário ter o nome da tabela como parâmetro.';
$lang['db_unsupported_function'] = 'Este recurso não está disponível para o banco de dados que você está usando. ';
$lang['db_transaction_failure'] = 'Transação falhou: Rollback efetuado.';
$lang['db_unable_to_drop'] = 'Não foi possível efetuar apagar o banco de dados especificado.';
$lang['db_unsupported_feature'] = 'Recurso não suportado para a plataforma de banco de dados que está sendo utilizada.';
$lang['db_unsupported_compression'] = 'O formato de compressão de dos escolhido não é suportador pelo seu servidor.';
$lang['db_filepath_error'] = 'Não foi possível escrever dados para o caminho enviado.';
$lang['db_invalid_cache_path'] = 'O caminho de cache enviado não é válido ou editável.';
$lang['db_table_name_required'] = 'Para esta operação é necessário um nome para a tabela .';
$lang['db_column_name_required'] = 'Para esta operação é necessário um nome de coluna.';
$lang['db_column_definition_required'] = 'Para esta operação é necessário definir uma coluna.';
$lang['db_unable_to_set_charset'] = 'Não é possível definir um conjunto de caractéres para a conexão do cliente: %s';
$lang['db_error_heading'] = 'Ocorreu um erro no banco de dados';
