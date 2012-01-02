<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_media extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
			$this->dbforge->add_field("`media_bf_users_id` BIGINT(20) NOT NULL");
			$this->dbforge->add_field("`media_tanggalupload` DATETIME NOT NULL");
			$this->dbforge->add_field("`media_judul` VARCHAR(50) NOT NULL");
			$this->dbforge->add_field("`media_deskripsi` VARCHAR(500) NOT NULL");
			$this->dbforge->add_field("`media_mime` VARCHAR(20) NOT NULL");
			$this->dbforge->add_field("`media_media` LONGBLOB NOT NULL");
			$this->dbforge->add_field("`media_thumbnail` LONGBLOB NOT NULL");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('media');

	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->drop_table('media');

	}
	
	//--------------------------------------------------------------------
	
}