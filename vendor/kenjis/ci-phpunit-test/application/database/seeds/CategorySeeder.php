<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CategorySeeder extends Seeder {

	private $table = 'category';

	public function run()
	{
		$this->db->truncate($this->table);

		$data = [
			'id' => 1,
			'name' => 'Book',
		];
		$this->db->insert($this->table, $data);
		
		$data = [
			'id' => 2,
			'name' => 'CD',
		];
		$this->db->insert($this->table, $data);
		
		$data = [
			'id' => 3,
			'name' => 'DVD',
		];
		$this->db->insert($this->table, $data);
	}

}
