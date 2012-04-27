<?php

if ( ! class_exists('CI_DB_query_builder'))
{
	class Mock_Database_DB_QueryBuilder extends CI_DB_active_record {}
}
else
{
	class Mock_Database_DB_QueryBuilder extends CI_DB_query_builder {}
}
